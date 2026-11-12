<?php
// modulos/ventas_agregar.php

// 1. Procesar el guardado de la venta completa con Pagos Múltiples
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'guardar_venta') {
    $id_clientes        = intval($_POST['id_clientes'] ?? 0);
    $total              = floatval($_POST['total_final'] ?? 0);
    $id_usuarios        = isset($_SESSION['id_usuarios']) ? intval($_SESSION['id_usuarios']) : 1;
    
    // Capturamos el comprobante y la letra seleccionados
    $tipo_comprobante   = trim($_POST['tipo_comprobante'] ?? 'Factura');
    $letra_comprobante  = strtoupper(trim($_POST['letra_comprobante'] ?? 'B'));
    $es_factura         = (strcasecmp($tipo_comprobante, 'Factura') === 0);

    $articulos_json     = $_POST['articulos_json'] ?? '[]';
    $articulos          = json_decode($articulos_json, true);

    $pagos_json         = $_POST['pagos_json'] ?? '[]';
    $pagos              = json_decode($pagos_json, true);


    if ($id_clientes > 0 && !empty($articulos) && $total > 0 && !empty($pagos)) {
        mysqli_begin_transaction($conexion);

        try {
            // A. Insertar Cabecera de Venta
            $primer_metodo = intval($pagos[0]['id_metodo']);
            $q_venta = "INSERT INTO ventas (fecha_hora, id_clientes, id_usuarios, id_metodos_pago, total, esta_eliminado) 
                        VALUES (NOW(), $id_clientes, $id_usuarios, $primer_metodo, $total, 0)";
            mysqli_query($conexion, $q_venta);
            $id_venta_generada = mysqli_insert_id($conexion);

            // B. Insertar cada Ítem en ventas_detalle y descontar stock
            foreach ($articulos as $item) {
                $id_art = intval($item['id']);
                $cant   = intval($item['cantidad']);
                $precio = floatval($item['precio']);
                $subt   = $cant * $precio;

                $q_det = "INSERT INTO ventas_detalle (id_ventas, id_articulos, cantidad, precio_unitario, subtotal) 
                          VALUES ($id_venta_generada, $id_art, $cant, $precio, $subt)";
                mysqli_query($conexion, $q_det);

                // Movimiento OUT en inventario
                $q_mov = "INSERT INTO movimientos_stock (id_productos, id_usuarios, tipo_movimiento, cantidad, fecha_movimiento, comentarios, esta_eliminado) 
                          VALUES ($id_art, $id_usuarios, 'OUT', $cant, NOW(), 'Venta #$id_venta_generada', 0)";
                mysqli_query($conexion, $q_mov);
            }

            // C. Registrar cada forma de pago individualmente
            foreach ($pagos as $pago) {
            $id_metodo = intval($pago['id_metodo']);
            $monto_p   = floatval($pago['monto']);
            $nombre_m  = mysqli_real_escape_string($conexion, $pago['nombre_metodo']);

            if ($monto_p > 0) {
                $q_vp = "INSERT INTO ventas_pagos (id_ventas, id_metodos_pago, monto, fecha_pago, esta_eliminado) 
                        VALUES ($id_venta_generada, $id_metodo, $monto_p, NOW(), 0)";
                mysqli_query($conexion, $q_vp);

                
                // SI EL MÉTODO DE PAGO ES CUENTA CORRIENTE / FIADO (ej: ID 7)
                if ($id_metodo == 7) {
                    $concepto_ctacte = "Venta a Cuenta Corriente #$id_venta_generada";
                    $q_ctacte = "INSERT INTO cobros_ctacte (id_clientes, id_usuarios, fecha_pago, monto, id_metodos_pago, concepto, id_caja, esta_eliminado) 
                                 VALUES ($id_clientes, $id_usuarios, NOW(), $monto_p, $id_metodo, '$concepto_ctacte', NULL, 0)";
                    mysqli_query($conexion, $q_ctacte);
                }

                if ($id_metodo != 7) {
                    $id_cuenta = ($id_metodo == 4) ? 2 : 1;
                    $detalle_caja = "Cobro Venta #$id_venta_generada ($nombre_m)";
                    $q_caja = "INSERT INTO caja (fecha, detalle, importe, id_cuenta, esta_eliminado) 
                               VALUES (NOW(), '$detalle_caja', $monto_p, $id_cuenta, 0)";
                    mysqli_query($conexion, $q_caja);
                }
            }
        }

            // D. Si es FACTURA, autorizar en ARCA / AFIP
            $es_factura = (strcasecmp($tipo_comprobante, 'Factura') === 0);
            $datos_afip = null;

            if ($es_factura) {
                require_once __DIR__ . '/../php/arca_service.php';
                
                // Consultamos datos del cliente para AFIP
                $q_cli = mysqli_query($conexion, "SELECT c.*, tr.codigoafip, dt.nroafip 
                                                   FROM clientes c 
                                                   LEFT JOIN tipos_responsable tr ON c.id_tiporesp = tr.id_tiporesp 
                                                   LEFT JOIN documentos_tipo dt ON c.id_documentos_tipo = dt.id_documentos_tipo 
                                                   WHERE c.id_clientes = $id_clientes LIMIT 1");
                $cli_data = mysqli_fetch_assoc($q_cli);

                $docTipo = !empty($cli_data['nroafip']) ? (int)$cli_data['nroafip'] : 99; // 99: Consumidor Final
                $docNro  = !empty($cli_data['cuit']) ? preg_replace('/[^0-9]/', '', $cli_data['cuit']) : "0";
                $condIva = !empty($cli_data['id_tiporesp']) ? (int)$cli_data['id_tiporesp'] : 3;

                $cuit_emisor = "27412878278";
                $arca = new ArcaService($cuit_emisor);
                $ptoVta = 1;

                // Emisión según letra (Factura B = 6, Factura C = 11)
                $tipo_cbte_arca = ($letra_comprobante === 'C') ? 11 : 6;
                $respAfip = $arca->emitirFacturaB($ptoVta, $total, $docNro, $docTipo);

                $cbte_nro = intval($respAfip['cbte_nro']);
                $cae      = mysqli_real_escape_string($conexion, $respAfip['cae']);
                $cae_vto  = mysqli_real_escape_string($conexion, $respAfip['cae_vto']);
                $imp_neto = round($total / 1.21, 2);

                // Insertar en ventas_facturas
                $q_vf = "INSERT INTO ventas_facturas 
                         (id_venta, pto_vta, cbte_tipo, cbte_nro, cbte_fecha, concepto, doc_tipo, doc_nro, condicion_iva_receptor_id, imp_total, imp_neto, cae, cae_vto) 
                         VALUES 
                         ($id_venta_generada, $ptoVta, $tipo_cbte_arca, $cbte_nro, NOW(), 1, $docTipo, '$docNro', $condIva, $total, $imp_neto, '$cae', '$cae_vto')";
                mysqli_query($conexion, $q_vf);

                $datos_afip = [
                    'cae' => $cae,
                    'cae_vto' => $cae_vto,
                    'cbte_nro' => $cbte_nro,
                    'letra' => $letra_comprobante
                ];
            }

            mysqli_commit($conexion);

            // Redirección con parámetros para modal de éxito o vista de impresión
            if ($es_factura && $datos_afip) {
                    // Te redirige directamente a la factura oficial con QR lista para imprimir/guardar PDF
                    echo "<script>
                        window.location.href = 'factura_imprimir.php?id=$id_venta_generada';
                    </script>";
                } else {
                    // Si eligió Recibo, va al recibo tradicional
                    echo "<script>window.location.href = 'index.php?page=ventas_recibo&id=$id_venta_generada';</script>";
                }
                exit;

        } catch (Exception $e) {
            mysqli_rollback($conexion);
            $error = "Error al procesar la venta: " . $e->getMessage();
        }
    } else {
        $error = "Verificá haber seleccionado un cliente, cargado artículos y al menos un método de pago válido.";
    }
}


// 2. Consultas para combos
$r_clientes = mysqli_query($conexion, "SELECT id_clientes, nombre, apellido FROM clientes WHERE esta_eliminado = 0 ORDER BY nombre ASC");
$r_metodos  = mysqli_query($conexion, "SELECT id_metodos_pago, nombre FROM metodos_pago WHERE esta_eliminado = 0 ORDER BY id_metodos_pago ASC");
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-3"><?php echo $error; ?></div>
<?php endif; ?>

<form id="formVenta" method="POST" action="">
    <input type="hidden" name="accion" value="guardar_venta">
    <input type="hidden" name="articulos_json" id="articulos_json" value="[]">
    <input type="hidden" name="pagos_json" id="pagos_json" value="[]">
    <input type="hidden" name="total_final" id="total_final" value="0">

    <!-- 1. CABECERA -->
    <div class="main-card p-3 mb-3 shadow-sm" style="background: #FFFFFF; border-radius: 10px;">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Fecha</label>
                <input type="text" class="form-control form-control-sm bg-light" value="<?php echo date('d/m/Y'); ?>" readonly>
            </div>
            <div class="col-md-5">
                <label class="form-label small text-muted fw-semibold">Cliente (*)</label>
                <select name="id_clientes" id="clienteSelect" class="form-select form-select-sm" required>
                    <option value="">Buscar cliente...</option>
                    <?php while ($c = mysqli_fetch_assoc($r_clientes)): ?>
                        <option value="<?php echo $c['id_clientes']; ?>" data-letra="<?php echo htmlspecialchars($c['letra_comprobante'] ?? 'B'); ?>">
                            <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted fw-semibold">Comprobante</label>
                <select name="tipo_comprobante" id="select_tipo_comprobante" class="form-select form-select-sm bg-light" onchange="actualizarTextoBotonVenta()">
                    <option value="Factura">Factura</option>
                    <option value="Presupuesto">Presupuesto</option>
                    <option value="Recibo">Devolución</option>
                    <option value="Nota de Crédito">Nota de Crédito</option>
                </select>
            </div>
            <!-- Letra -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Letra</label>
                    <select name="letra_comprobante" id="select_letra_comprobante" class="form-select">
                        <option value="B" selected>B</option>
                        <option value="C">C</option>
                        <option value="A">A</option>
                    </select>
                </div>
        </div>
    </div>

    <!-- 2. AGREGAR ARTÍCULO -->
    <div class="main-card p-3 mb-3 shadow-sm" style="background: #FFFFFF; border-radius: 10px;">
        <div class="text-uppercase fw-bold text-muted small mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Agregar Artículo</div>
        <div class="row g-2 align-items-end">
            <div class="col-md-5 position-relative">
                <label class="form-label small text-muted mb-1">Artículo / Producto (*)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" 
                           id="buscador_articulo_input" 
                           class="form-control" 
                           placeholder="Escribí para buscar (ej. jarra, espejo)..." 
                           autocomplete="off">
                    <button class="btn btn-outline-secondary d-none" type="button" id="btn_limpiar_art">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Desplegable con resultados -->
                <div id="lista_resultados_art" 
                     class="position-absolute w-100 mt-1 shadow-lg bg-white rounded-2 p-1 d-none" 
                     style="z-index: 1060; max-height: 250px; overflow-y: auto; border: 1px solid #EAE1D0; left: 0;">
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Cant. <span id="label_stock_max" class="text-muted fw-normal"></span></label>
                <input type="number" id="prod_cant" class="form-control form-control-sm" value="1" min="1">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Precio Unit.</label>
                <input type="number" id="prod_precio" class="form-control form-control-sm" value="0.00" step="0.01">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Subtotal</label>
                <input type="text" id="prod_subtotal_preview" class="form-control form-control-sm bg-light" value="$0,00" readonly>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" id="btnAgregarProd" class="btn btn-success btn-sm w-100 fw-bold" title="Agregar a la lista">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. TABLA DE ÍTEMS -->
    <div class="main-card p-3 mb-3 shadow-sm" style="background: #FFFFFF; border-radius: 10px;">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Artículo</th>
                        <th class="text-center" style="width: 12%;">Cant.</th>
                        <th class="text-end" style="width: 18%;">Precio</th>
                        <th class="text-end" style="width: 18%;">Subtotal</th>
                        <th class="text-center" style="width: 10%;">Quitar</th>
                    </tr>
                </thead>
                <tbody id="cuerpoDetalle">
                    <tr id="filaVacia">
                        <td colspan="5" class="text-center text-muted py-3">Sin ítems cargados. Seleccioná un producto arriba y tocá (+).</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. FORMAS DE PAGO Y TOTALES -->
    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <div class="main-card p-3 shadow-sm h-100" style="background: #FFFFFF; border-radius: 10px;">
                <div class="text-uppercase fw-bold text-muted small mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                    <i class="fas fa-wallet text-success me-1"></i> Desglose de Pagos / Recibo
                </div>
                
                <div class="row g-2 align-items-end mb-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Método de Pago</label>
                        <select id="pago_metodo_select" class="form-select form-select-sm">
                            <?php mysqli_data_seek($r_metodos, 0); ?>
                            <?php while ($mp = mysqli_fetch_assoc($r_metodos)): ?>
                                <option value="<?php echo $mp['id_metodos_pago']; ?>">
                                    <?php echo htmlspecialchars($mp['nombre']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Monto Asignado</label>
                        <input type="number" id="pago_monto_input" class="form-control form-control-sm" placeholder="0.00" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btnAgregarPago" class="btn btn-outline-success btn-sm w-100 fw-bold" title="Agregar método de pago">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Método</th>
                                <th class="text-end" style="width: 35%;">Monto</th>
                                <th class="text-center" style="width: 15%;">Quitar</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoPagos">
                            <tr id="filaSinPagos">
                                <td colspan="3" class="text-center text-muted py-2">No se agregaron pagos todavía.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="btnGuardarVenta" class="btn btn-success fw-bold px-4 py-2 w-100">
                    <i class="fas fa-check-circle me-1"></i> Confirmar y Guardar Venta
                </button>
            </div>
        </div>

        <div class="col-md-5">
            <div class="main-card p-3 shadow-sm h-100 d-flex flex-column justify-content-between" style="background: #FDFCF6; border: 1px solid #EAE1D0; border-radius: 10px;">
                <div>
                    <span class="text-muted text-uppercase fw-bold small d-block">Resumen de Liquidación</span>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 border-bottom pb-2">
                        <span class="text-muted">Total Venta:</span>
                        <span class="fw-bold fs-5 text-dark" id="txtTotalFinal">$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 border-bottom pb-2">
                        <span class="text-muted">Total Cubierto:</span>
                        <span class="fw-bold fs-6 text-success" id="txtTotalCubierto">$ 0,00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2 pb-1">
                        <span class="text-muted">Diferencia / Resta:</span>
                        <span class="fw-bold fs-6 text-danger" id="txtRestaCubrir">$ 0,00</span>
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 mb-0 small text-center" id="boxEstadoPago">
                    Cargá productos para calcular el cobro.
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Estilos y Librerías de Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<!-- Inclusión segura de jQuery y Select2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let carrito = [];
let listaPagos = [];
let articuloSeleccionado = null;

const inputBuscarArt     = document.getElementById('buscador_articulo_input');
const listaResultadosArt = document.getElementById('lista_resultados_art');
const btnLimpiarArt      = document.getElementById('btn_limpiar_art');
const labelStockMax      = document.getElementById('label_stock_max');

const inputCant          = document.getElementById('prod_cant');
const inputPrecio        = document.getElementById('prod_precio');
const inputSubPreview    = document.getElementById('prod_subtotal_preview');
const btnAgregarProd     = document.getElementById('btnAgregarProd');
const cuerpoTabla        = document.getElementById('cuerpoDetalle');

const pagoMetodoSelect   = document.getElementById('pago_metodo_select');
const pagoMontoInput     = document.getElementById('pago_monto_input');
const btnAgregarPago     = document.getElementById('btnAgregarPago');
const cuerpoPagos        = document.getElementById('cuerpoPagos');

const txtTotal           = document.getElementById('txtTotalFinal');
const txtCubierto        = document.getElementById('txtTotalCubierto');
const txtResta           = document.getElementById('txtRestaCubrir');
const boxEstadoPago      = document.getElementById('boxEstadoPago');

const inputTotalFinal    = document.getElementById('total_final');
const inputArticulosJson = document.getElementById('articulos_json');
const inputPagosJson     = document.getElementById('pagos_json');
const formVenta          = document.getElementById('formVenta');
const btnGuardarVenta    = document.getElementById('btnGuardarVenta');



// --- AUTO-SELECCIÓN DE LETRA Y COMPROBANTE AL ELEGIR CLIENTE ---
$(document).ready(function() {

    // 1. Detección de cliente para actualizar letra
    $('#clienteSelect').on('select2:select change', function(e) {
        const selectedOption = $(this).find('option:selected');
        const letra = selectedOption.attr('data-letra') || selectedOption.data('letra') || 'B';
        
        const $selectLetra = $('#select_letra_comprobante').length 
            ? $('#select_letra_comprobante') 
            : $('select[name="letra_comprobante"]');
            
        $selectLetra.val(letra).trigger('change');
    });

    // 2. Cambio dinámico del botón según el Comprobante elegido
    const $selectCbte = $('#select_tipo_comprobante').length 
        ? $('#select_tipo_comprobante') 
        : $('select[name="tipo_comprobante"]');

    $selectCbte.on('change select2:select', function() {
        const tipo = $(this).val();
        const $btn = $('#btnGuardarVenta');

        if (tipo === 'factura' || tipo === 'Factura') {
            $btn.html('<i class="fas fa-print me-2"></i>Guardar / Imprimir Factura');
        } else {
            $btn.html('<i class="fas fa-check-circle me-2"></i>Confirmar y Guardar Venta');
        }
    });

    // Disparar una vez al iniciar para que adopte el texto según la opción por defecto
    $selectCbte.trigger('change');

});

// ================= BÚSQUEDA ASÍNCRONA DE ARTÍCULOS =================
let timerBusqueda = null;

inputBuscarArt.addEventListener('input', function() {
    clearTimeout(timerBusqueda);
    const query = this.value.trim();

    if (query.length > 0) {
        btnLimpiarArt.classList.remove('d-none');
    } else {
        btnLimpiarArt.classList.add('d-none');
        resetArticuloSeleccionado();
    }

    if (query.length < 1) {
        listaResultadosArt.classList.add('d-none');
        listaResultadosArt.innerHTML = '';
        return;
    }

    timerBusqueda = setTimeout(() => {
        fetch('php/articulos_buscar_ajax.php?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                const items = data.results || [];
                if (items.length === 0) {
                    listaResultadosArt.innerHTML = '<div class="p-2 text-muted small text-center">Sin stock o no encontrado</div>';
                } else {
                    let html = '';
                    items.forEach(it => {
                        html += `
                            <div class="item-busqueda p-2 rounded cursor-pointer border-bottom small" 
                                 style="cursor: pointer;"
                                 data-id="${it.id}" 
                                 data-nombre="${escapeHtml(it.nombre)}" 
                                 data-precio="${it.precio_venta}" 
                                 data-stock="${it.stock}">
                                <div class="fw-bold text-dark">${it.nombre}</div>
                                <div class="text-muted d-flex justify-content-between">
                                    <span>Stock: <b>${it.stock}</b></span>
                                    <span class="text-success fw-semibold">$ ${parseFloat(it.precio_venta).toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                                </div>
                            </div>
                        `;
                    });
                    listaResultadosArt.innerHTML = html;

                    // Asignar clic a cada resultado
                    listaResultadosArt.querySelectorAll('.item-busqueda').forEach(elem => {
                        elem.addEventListener('click', function() {
                            articuloSeleccionado = {
                                id: this.getAttribute('data-id'),
                                nombre: this.getAttribute('data-nombre'),
                                precio_venta: parseFloat(this.getAttribute('data-precio')),
                                stock: parseInt(this.getAttribute('data-stock'))
                            };

                            inputBuscarArt.value = articuloSeleccionado.nombre;
                            inputPrecio.value = articuloSeleccionado.precio_venta.toFixed(2);
                            labelStockMax.textContent = `(Máx: ${articuloSeleccionado.stock})`;
                            listaResultadosArt.classList.add('d-none');
                            calcularSubtotalPreview();
                            inputCant.focus();
                        });
                    });
                }
                listaResultadosArt.classList.remove('d-none');
            })
            .catch(err => console.error('Error al buscar artículos:', err));
    }, 200);
});

// Limpiar artículo seleccionado
btnLimpiarArt.addEventListener('click', function() {
    inputBuscarArt.value = '';
    btnLimpiarArt.classList.add('d-none');
    listaResultadosArt.classList.add('d-none');
    resetArticuloSeleccionado();
    inputBuscarArt.focus();
});

function resetArticuloSeleccionado() {
    articuloSeleccionado = null;
    labelStockMax.textContent = '';
    inputPrecio.value = '0.00';
    inputSubPreview.value = '$0,00';
}

// Ocultar si hace clic afuera
document.addEventListener('click', function(e) {
    if (!inputBuscarArt.contains(e.target) && !listaResultadosArt.contains(e.target)) {
        listaResultadosArt.classList.add('d-none');
    }
});

// Cálculos de subtotal en línea
inputCant.addEventListener('input', calcularSubtotalPreview);
inputPrecio.addEventListener('input', calcularSubtotalPreview);

function calcularSubtotalPreview() {
    const c = parseInt(inputCant.value) || 0;
    const p = parseFloat(inputPrecio.value) || 0;
    inputSubPreview.value = '$' + (c * p).toLocaleString('es-AR', {minimumFractionDigits: 2});
}

function alSeleccionarCliente(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    if (!selectedOption || !selectedOption.value) return;

    // Obtener la letra guardada en el atributo data-letra
    const letraGuardada = selectedOption.getAttribute('data-letra') || 'B';
    
    const selectLetra = document.getElementById('select_letra_comprobante');
    if (selectLetra) {
        selectLetra.value = letraGuardada;
    }

    const selectCbte = document.getElementById('select_tipo_comprobante');
    if (selectCbte) {
        selectCbte.value = 'factura'; // Por defecto sugiere Factura
    }
}


function actualizarTextoBotonVenta() {
    const selectCbte = document.getElementById('select_tipo_comprobante') || document.querySelector('select[name="tipo_comprobante"]');
    const btnGuardar = document.getElementById('btnGuardarVenta');

    if (!selectCbte || !btnGuardar) return;

    const val = (selectCbte.value || '').toLowerCase();

    if (val === 'factura') {
        btnGuardar.innerHTML = '<i class="fas fa-print me-2"></i>Guardar / Imprimir Factura';
    } else {
        btnGuardar.innerHTML = '<i class="fas fa-check-circle me-2"></i>Confirmar y Guardar Venta';
    }
}

// Escuchar cambios tanto nativos como de Select2/jQuery
$(document).ready(function() {
    const $selectCbte = $('#select_tipo_comprobante, select[name="tipo_comprobante"]');

    $selectCbte.on('change select2:select', function() {
        actualizarTextoBotonVenta();
    });

    // Disparo inicial al cargar
    actualizarTextoBotonVenta();
});

// Botón (+) Agregar Artículo a la Tabla
btnAgregarProd.addEventListener('click', function() {
    if (!articuloSeleccionado) {
        Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: 'Por favor buscá y seleccioná un artículo del catálogo.',
        confirmButtonColor: '#198754'
        });
        inputBuscarArt.focus();
        return;
    }

    const id = articuloSeleccionado.id;
    const nombre = articuloSeleccionado.nombre;
    const cant = parseInt(inputCant.value) || 0;
    const precio = parseFloat(inputPrecio.value) || 0;

    if (!id || cant <= 0 || precio <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ingresá una cantidad y precio válidos mayores a cero.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    if (cant > articuloSeleccionado.stock) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'La cantidad seleccionada supera el stock actual (' + articuloSeleccionado.stock + ').',
            confirmButtonColor: '#198754'
        });
        return;
    }

    const existente = carrito.find(item => item.id == id);
    if (existente) {
        if (existente.cantidad + cant > articuloSeleccionado.stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'La cantidad acumulada superaría el stock disponible (' + articuloSeleccionado.stock + ').',
                confirmButtonColor: '#198754'
            });
            return;
        }
        existente.cantidad += cant;
    } else {
        carrito.push({ id, nombre, cantidad: cant, precio: precio });
    }

    // Resetear campo
    inputBuscarArt.value = '';
    btnLimpiarArt.classList.add('d-none');
    resetArticuloSeleccionado();
    inputCant.value = 1;

    renderTablaArticulos();
});

function eliminarItem(id) {
    carrito = carrito.filter(item => item.id != id);
    renderTablaArticulos();
}

function renderTablaArticulos() {
    if (carrito.length === 0) {
        cuerpoTabla.innerHTML = `<tr id="filaVacia"><td colspan="5" class="text-center text-muted py-3">Sin ítems cargados. Seleccioná un producto arriba y tocá (+).</td></tr>`;
        actualizarLiquidacion(0);
        return;
    }

    let html = '';
    let totalAcumulado = 0;

    carrito.forEach(item => {
        const sub = item.cantidad * item.precio;
        totalAcumulado += sub;
        html += `
            <tr>
                <td class="fw-semibold text-dark">${item.nombre}</td>
                <td class="text-center">${item.cantidad}</td>
                <td class="text-end">$${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
                <td class="text-end fw-bold text-success">$${sub.toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="eliminarItem('${item.id}')">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    cuerpoTabla.innerHTML = html;
    actualizarLiquidacion(totalAcumulado);
}

// ================= LÓGICA DE PAGOS =================
btnAgregarPago.addEventListener('click', function() {
    const id_metodo = pagoMetodoSelect.value;
    const nombre_metodo = pagoMetodoSelect.options[pagoMetodoSelect.selectedIndex].text;
    const monto = parseFloat(pagoMontoInput.value) || 0;

    if (monto <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ingresá un monto mayor a 0 para el método de pago.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    const totalVenta = parseFloat(inputTotalFinal.value) || 0;
    if (totalVenta <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Cargá primero los artículos antes de asignar los pagos.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    listaPagos.push({ id_metodo, nombre_metodo, monto });
    pagoMontoInput.value = '';
    renderTablaPagos();
});

function eliminarPago(index) {
    listaPagos.splice(index, 1);
    renderTablaPagos();
}

function renderTablaPagos() {
    if (listaPagos.length === 0) {
        cuerpoPagos.innerHTML = `<tr id="filaSinPagos"><td colspan="3" class="text-center text-muted py-2">No se agregaron pagos todavía.</td></tr>`;
    } else {
        let html = '';
        listaPagos.forEach((p, idx) => {
            html += `
                <tr>
                    <td class="fw-semibold text-dark">${p.nombre_metodo}</td>
                    <td class="text-end fw-bold text-success">$${p.monto.toLocaleString('es-AR', {minimumFractionDigits: 2})}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="eliminarPago(${idx})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        cuerpoPagos.innerHTML = html;
    }

    actualizarLiquidacion(parseFloat(inputTotalFinal.value) || 0);
}

// ================= LIQUIDACIÓN GENERAL =================
function actualizarLiquidacion(totalVenta) {
    inputTotalFinal.value = totalVenta;
    inputArticulosJson.value = JSON.stringify(carrito);
    inputPagosJson.value = JSON.stringify(listaPagos);

    const totalCubierto = listaPagos.reduce((acc, p) => acc + p.monto, 0);
    const diferencia = totalVenta - totalCubierto;

    txtTotal.textContent = '$ ' + totalVenta.toLocaleString('es-AR', {minimumFractionDigits: 2});
    txtCubierto.textContent = '$ ' + totalCubierto.toLocaleString('es-AR', {minimumFractionDigits: 2});
    txtResta.textContent = '$ ' + (diferencia > 0 ? diferencia : 0).toLocaleString('es-AR', {minimumFractionDigits: 2});

    if (diferencia > 0 && (!pagoMontoInput.value || parseFloat(pagoMontoInput.value) <= 0)) {
        pagoMontoInput.value = diferencia.toFixed(2);
    }

    if (totalVenta === 0) {
        boxEstadoPago.className = 'alert alert-info py-2 px-3 mb-0 small text-center';
        boxEstadoPago.textContent = 'Cargá productos para calcular el cobro.';
    } else if (Math.abs(diferencia) < 0.01) {
        boxEstadoPago.className = 'alert alert-success py-2 px-3 mb-0 small text-center fw-bold';
        boxEstadoPago.innerHTML = '<i class="fas fa-check-circle me-1"></i> Total cubierto exactamente.';
    } else if (diferencia > 0) {
        boxEstadoPago.className = 'alert alert-warning py-2 px-3 mb-0 small text-center';
        boxEstadoPago.textContent = 'Faltan cubrir $' + diferencia.toLocaleString('es-AR', {minimumFractionDigits: 2});
    } else {
        boxEstadoPago.className = 'alert alert-danger py-2 px-3 mb-0 small text-center';
        boxEstadoPago.textContent = 'El monto asignado supera el total por $' + Math.abs(diferencia).toLocaleString('es-AR', {minimumFractionDigits: 2});
    }
}

btnGuardarVenta.addEventListener('click', function() {
    const totalVenta = parseFloat(inputTotalFinal.value) || 0;
    const totalCubierto = listaPagos.reduce((acc, p) => acc + p.monto, 0);
    const clienteId = document.getElementById('clienteSelect').value;

    if (!clienteId) {
        Swal.fire({
    icon: 'warning',
    title: 'Atención',
    text: 'Por favor buscá y seleccioná un cliente.',
    confirmButtonColor: '#198754'
});
        return;
    }
    if (carrito.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Agregá al menos un artículo a la venta.',
            confirmButtonColor: '#198754'
        });
        return;
    }
    if (listaPagos.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Agregá al menos un método de pago con el botón (+).',
            confirmButtonColor: '#198754'
        });
        return;
    }
    if (Math.abs(totalVenta - totalCubierto) > 0.01) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'La suma de los métodos de pago ($' + totalCubierto.toFixed(2) + ') debe coincidir exactamente con el total de la venta ($' + totalVenta.toFixed(2) + ').',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    formVenta.submit();
});

function escapeHtml(texto) {
    let div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}
</script>

<style>
.item-busqueda:hover {
    background-color: #F8F5EE !important;
}
</style>