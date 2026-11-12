<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// factura_imprimir.php
require_once __DIR__ . '/php/conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_venta <= 0) {
    die("ID de comprobante no válido.");
}

// 1. Obtener datos de la venta y de la factura emitida
$q_factura = mysqli_query($conexion, "
    SELECT vf.*, v.fecha_hora, v.total,
           c.nombre AS cli_nombre, c.apellido AS cli_apellido, c.telefono AS cli_telefono, 
           c.email AS cli_email, c.id_tiporesp, 'B' AS letra_comprobante,
           tr.tiporesp AS condicion_iva_nombre,
           mp.nombre AS metodo_pago_nombre
    FROM ventas_facturas vf
    INNER JOIN ventas v ON vf.id_venta = v.id_ventas
    LEFT JOIN clientes c ON v.id_clientes = c.id_clientes
    LEFT JOIN tipos_responsable tr ON c.id_tiporesp = tr.id_tiporesp
    LEFT JOIN metodos_pago mp ON v.id_metodos_pago = mp.id_metodos_pago
    WHERE vf.id_venta = $id_venta 
    ORDER BY vf.id_factura DESC LIMIT 1
");

$factura = mysqli_fetch_assoc($q_factura);

if (!$factura) {
    die("No se encontraron datos fiscales asociados a esta venta.");
}

// 2. Obtener los ítems de la venta
//$q_items = mysqli_query($conexion, "
//    SELECT vd.*, a.nombre AS art_nombre, a.id_articulos AS art_codigo
//    FROM ventas_detalle vd
//    INNER JOIN articulos a ON vd.id_articulos = a.id_articulos
//    WHERE vd.id_ventas = $id_venta
//");



// --- PREPARACIÓN DEL QR OFICIAL AFIP / ARCA ---
$datosQr = [
    "ver"         => 1,
    "fecha"       => date('Y-m-d', strtotime($factura['cbte_fecha'])),
    "cuit"        => 27412878278, // Tu CUIT emisor numérico
    "ptoVta"      => intval($factura['pto_vta']),
    "tipoCmp"     => intval($factura['cbte_tipo']),
    "nroCmp"      => intval($factura['cbte_nro']),
    "importe"     => floatval($factura['total']),
    "moneda"      => "PES",
    "ctz"         => 1,
    "tipoDocRec"  => !empty($factura['doc_tipo']) ? intval($factura['doc_tipo']) : 99,
    "nroDocRec"   => !empty($factura['doc_nro']) && $factura['doc_nro'] !== '0' ? intval($factura['doc_nro']) : 0,
    "tipoCodAut"  => "E",
    "codAut"      => intval($factura['cae'])
];

$jsonQr = json_encode($datosQr);
$base64Qr = base64_encode($jsonQr);
$urlValidacionAfip = "https://www.afip.gob.ar/fe/qr/?p=" . $base64Qr;

// Generador de imagen QR directa (sin librerías locales)
$urlImagenQr = "https://api.qrserver.com/v1/create-qr-code/?size=115x115&data=" . urlencode($urlValidacionAfip);

// Formateos visuales
$letra = !empty($factura['cbte_tipo']) && $factura['cbte_tipo'] == 11 ? 'C' : 'B';
$ptoVta = str_pad($factura['pto_vta'], 4, '0', STR_PAD_LEFT);
$nroComp = str_pad($factura['cbte_nro'], 8, '0', STR_PAD_LEFT);
$codCbteAfip = ($letra === 'C') ? '11' : '06';

$fechaCbte = date('d/m/Y', strtotime($factura['cbte_fecha']));
$vtoCae = !empty($factura['cae_vto']) ? date('d/m/Y', strtotime($factura['cae_vto'])) : '';
$clienteNombre = trim(($factura['cli_nombre'] ?? '') . ' ' . ($factura['cli_apellido'] ?? ''));
if (empty($clienteNombre)) $clienteNombre = "CONSUMIDOR FINAL";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?php echo "$letra-$ptoVta-$nroComp"; ?></title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f0f0f0;
            padding: 20px;
            font-size: 11px;
            color: #111;
        }
        .factura-box {
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #222;
            padding: 12px;
            min-height: 1050px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* CABECERA */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #222;
            position: relative;
        }
        .header-table td {
            vertical-align: top;
            padding: 8px;
        }
        .emisor-col {
            width: 48%;
        }
        .receptor-col {
            width: 48%;
        }
        .letra-box-container {
            width: 4%;
            text-align: center;
            position: relative;
        }
        .letra-box {
            width: 42px;
            height: 40px;
            border: 2px solid #222;
            background: #fff;
            margin: 0 auto;
            font-size: 24px;
            font-weight: bold;
            line-height: 38px;
            text-align: center;
        }
        .codigo-letra {
            font-size: 8px;
            font-weight: bold;
            margin-top: 2px;
        }

        .nombre-comercial {
            font-size: 20px;
            font-weight: 900;
            color: #0b6623;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .datos-emisor p, .datos-factura p {
            line-height: 1.35;
            margin: 0;
            font-size: 10.5px;
        }
        .titulo-factura {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        /* CLIENTE / RECEPTOR */
        .receptor-section {
            border-bottom: 2px solid #222;
            padding: 8px 4px;
            line-height: 1.45;
        }
        .tabla-info {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-info td {
            padding: 2px 4px;
            font-size: 10.5px;
        }

        /* TABLA ARTÍCULOS */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: auto;
        }
        .items-table th {
            background-color: #0b6623;
            color: #fff;
            padding: 6px;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            font-size: 10.5px;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }

        /* PIE FISCAL */
        .footer-fiscal {
            border-top: 2px solid #222;
            padding-top: 8px;
            margin-top: 20px;
        }
        .totales-box {
            width: 100%;
            margin-bottom: 15px;
        }
        .totales-box td {
            padding: 3px 6px;
            font-size: 12px;
        }
        .arca-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px dashed #444;
            padding-top: 10px;
        }
        .arca-logo {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #111;
        }
        .cae-info {
            text-align: right;
            font-size: 11px;
            font-weight: bold;
        }

        /* ESTILOS DE IMPRESIÓN (A4) */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .factura-box {
                border: 1px solid #000;
                width: 100%;
                max-width: 100%;
                min-height: 98vh;
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 10px auto; display: flex; justify-content: flex-end; gap: 8px;">
        <button onclick="descargarFacturaPDF()" style="padding: 8px 18px; font-weight: bold; background: #0b6623; color: white; border: none; border-radius: 4px; cursor: pointer;">
            📥 Descargar PDF
        </button>
        <button onclick="window.print()" style="padding: 8px 18px; font-weight: bold; background: #198754; color: white; border: none; border-radius: 4px; cursor: pointer;">
            🖨️ Imprimir
        </button>
        <button onclick="enviarFacturaPorEmail()" style="padding: 8px 18px; font-weight: bold; background: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer;">
            ✉️ Enviar por Email
        </button>
        <button onclick="window.location.href='index.php?page=ventas'" style="padding: 8px 18px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer; text-decoration: none; color: #333;">
            Cerrar
        </button>
    </div>

    <div class="factura-box">
        <div>
            <!-- CABECERA -->
            <table class="header-table">
                <tr>
                    <!-- EMISOR -->
                    <td class="emisor-col">
                        <div class="nombre-comercial">DECOSTORE</div>
                        <div class="datos-emisor">
                            <p><strong>Razón Social:</strong> DECOSTORE HOME & DECO</p>
                            <p><strong>Domicilio Comercial:</strong> San Justo - Santa Fe</p>
                            <p><strong>Condición frente al IVA:</strong> RESPONSABLE MONOTRIBUTO</p>
                        </div>
                    </td>

                    <!-- LETRA FISCAL -->
                    <td class="letra-box-container">
                        <div class="letra-box"><?php echo $letra; ?></div>
                        <div class="codigo-letra">COD. <?php echo $codCbteAfip; ?></div>
                    </td>

                    <!-- DATOS COMPROBANTE -->
                    <td class="receptor-col text-end">
                        <div class="titulo-factura">FACTURA "<?php echo $letra; ?>"</div>
                        <div class="datos-factura">
                            <p><strong>Punto de Venta:</strong> <?php echo $ptoVta; ?> &nbsp;&nbsp; <strong>Comp. Nro:</strong> <?php echo $nroComp; ?></p>
                            <p><strong>Fecha de Emisión:</strong> <?php echo $fechaCbte; ?></p>
                            <p><strong>CUIT:</strong> 27412878278</p>
                            <p><strong>Ingresos Brutos:</strong> Convenio Multilateral / Local</p>
                            <p><strong>Fecha de Inicio de Actividades:</strong> 01/01/2024</p>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- DATOS DEL CLIENTE -->
            <div class="receptor-section">
                <table class="tabla-info">
                    <tr>
                        <td style="width: 60%;"><strong>Apellido y Nombre / Razón Social:</strong> <?php echo htmlspecialchars($clienteNombre); ?></td>
                        <td style="width: 40%;"><strong>CUIT / DNI:</strong> <?php echo !empty($factura['doc_nro']) && $factura['doc_nro'] !== '0' ? $factura['doc_nro'] : 'Consumidor Final'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Condición frente al IVA:</strong> <?php echo htmlspecialchars($factura['condicion_iva_nombre'] ?? 'Consumidor Final'); ?></td>
                        <td><strong>Condición de Venta:</strong> <?php echo htmlspecialchars($factura['metodo_pago_nombre'] ?? 'Contado'); ?></td>
                    </tr>
                </table>
            </div>

            <!-- DETALLE PRODUCTOS -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Cant.</th>
                        <th style="width: 15%;">Código</th>
                        <th style="width: 45%;">Descripción</th>
                        <th style="width: 14%;" class="text-end">Precio Unit.</th>
                        <th style="width: 14%;" class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1,00</td>
                        <td>-</td>
                        <td>Artículos de Decoración / Varios</td>
                        <td class="text-end">$<?php echo number_format($factura['total'], 2, ',', '.'); ?></td>
                        <td class="text-end">$<?php echo number_format($factura['total'], 2, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PIE FISCAL CON CAE -->
        <div class="footer-fiscal">
            <table class="totales-box">
                <tr>
                    <td style="width: 70%;"></td>
                    <td style="width: 15%; text-align: right;"><strong>Subtotal:</strong></td>
                    <td style="width: 15%; text-align: right;">$<?php echo number_format($factura['total'], 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: right; font-size: 14px; font-weight: bold; border-top: 1px solid #222;">TOTAL:</td>
                    <td style="text-align: right; font-size: 14px; font-weight: bold; border-top: 1px solid #222;">$<?php echo number_format($factura['total'], 2, ',', '.'); ?></td>
                </tr>
            </table>

            <div class="arca-box">
                <!-- CÓDIGO QR OFICIAL + LOGO ARCA -->
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="<?php echo $urlImagenQr; ?>" alt="QR AFIP" style="width: 85px; height: 85px; border: 1px solid #ddd; padding: 2px; background: #fff;">
                    <div>
                        <div class="arca-logo">ARCA</div>
                        <span style="font-size: 9px; font-weight: bold; letter-spacing: 0.5px; color: #555;">Comprobante Autorizado ORIGINAL</span>
                    </div>
                </div>

                <!-- CAE Y VENCIMIENTO -->
                <div class="cae-info">
                    <p><strong>CAE N°:</strong> <?php echo htmlspecialchars($factura['cae']); ?></p>
                    <p><strong>Fecha de Vto. de CAE:</strong> <?php echo $vtoCae; ?></p>
                </div>
            </div> <!-- Cierra .arca-box -->

        </div> <!-- Cierra .footer-fiscal -->
    </div> <!-- Cierra .factura-box -->

    <!-- Librería html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- SweetAlert2 CSS y JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function descargarFacturaPDF() {
            const elemento = document.querySelector('.factura-box');
            const opt = {
                margin:       [5, 5, 5, 5],
                filename:     'Factura_<?php echo "$letra-$ptoVta-$nroComp"; ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(elemento).save();
        }

        function enviarFacturaPorEmail() {
    // Alerta de confirmación moderna (reemplaza al confirm() clásico)
    Swal.fire({
        title: '¿Enviar Factura por Email?',
        text: "¿Deseas enviar este comprobante al correo electrónico del cliente?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754', // Verde institucional
        cancelButtonColor: '#6c757d',  // Gris secundario
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Obtenemos el id de la venta actual de los parámetros de la URL
            const urlParams = new URLSearchParams(window.location.search);
            const idVenta = urlParams.get('id');

            if (!idVenta) {
                Swal.fire('Error', 'No se encontró el ID de la venta en la URL.', 'error');
                return;
            }

            // Indicador de carga animado
            Swal.fire({
                title: 'Enviando correo...',
                text: 'Por favor espere unos segundos.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Preparamos los datos por POST
            const formData = new FormData();
            formData.append('id_venta', idVenta);

            // Llamada AJAX al backend
            fetch('php/enviar_factura_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.success) {
                    Swal.fire({
                        title: '¡Enviado con éxito!',
                        text: 'La factura ha sido enviada al correo del cliente correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#198754'
                    });
                } else {
                    Swal.fire({
                        title: 'Error al enviar',
                        text: data.mensaje || data.message || 'Ocurrió un error inesperado.',
                        icon: 'error',
                        confirmButtonColor: '#198754'
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    title: 'Error de conexión',
                    text: 'Ocurrió un error al intentar conectar con el servidor.',
                    icon: 'error',
                    confirmButtonColor: '#198754'
                });
            });
        }
    });
}
    </script>
</body>
</html>