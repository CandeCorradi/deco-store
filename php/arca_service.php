<?php
// php/arca_service.php

class ArcaService {
    private $cuit;
    private $cert;
    private $key;
    private $dirCert;
    
    // Rutas a los WSDL locales
    private $wsdlWsaa;
    private $wsdlWsfe;

    public function __construct($cuit) {
        $this->cuit = $cuit;
        $this->dirCert = realpath(__DIR__ . '/../certificados') . DIRECTORY_SEPARATOR;
        $this->cert = $this->dirCert . 'cert_candela.crt';
        $this->key  = $this->dirCert . 'key_candela.key';

        $this->wsdlWsaa = $this->dirCert . 'wsaa.wsdl';
        $this->wsdlWsfe = $this->dirCert . 'wsfe.wsdl';

        if (!file_exists($this->cert) || !file_exists($this->key)) {
            throw new Exception("No se encontraron los certificados en: {$this->dirCert}");
        }

        if (!file_exists($this->wsdlWsaa) || !file_exists($this->wsdlWsfe)) {
            throw new Exception("Faltan descargar los archivos WSDL (wsaa.wsdl y/wsfe.wsdl) en {$this->dirCert}");
        }
    }

    /**
     * Opciones para SoapClient con SSL permissive en testing
     */
    private function getSoapOptions($urlLocation = null) {
        $opts = [
            'soap_version' => SOAP_1_2,
            'cache_wsdl'   => WSDL_CACHE_NONE,
            'trace'        => 1,
            'exceptions'   => true,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ])
        ];

        if ($urlLocation !== null) {
            $opts['location'] = $urlLocation;
        }

        return $opts;
    }

    /**
     * Obtiene Token y Sign válidos (cachea en TA.xml por 12 hs)
     */
    /**
     * Obtiene Token y Sign válidos (cachea en TA.xml por 12 hs)
     */
    private function obtenerTokenSign() {
        $taFile = $this->dirCert . 'TA.xml';

        // Si ya existe y no venció, lo usamos directamente
        if (file_exists($taFile)) {
            $taXml = simplexml_load_file($taFile);
            $expiracion = strtotime($taXml->header->expirationTime);
            if (time() < ($expiracion - 600)) {
                return [
                    'token' => (string)$taXml->credentials->token,
                    'sign'  => (string)$taXml->credentials->sign
                ];
            }
        }

        // Crear TRA
        $tra = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">' .
            '<header><uniqueId>' . time() . '</uniqueId>' .
            '<generationTime>' . date('c', date('U') - 120) . '</generationTime>' .
            '<expirationTime>' . date('c', date('U') + 600) . '</expirationTime></header>' .
            '<service>wsfe</service>' .
            '</loginTicketRequest>'
        );

        $traXmlFile = $this->dirCert . 'TRA.xml';
        $traTmpFile = $this->dirCert . 'TRA.tmp';
        $tra->asXML($traXmlFile);

        // Firmar con OpenSSL
        $status = openssl_pkcs7_sign(
            $traXmlFile,
            $traTmpFile,
            "file://" . $this->cert,
            ["file://" . $this->key, ""],
            [],
            !PKCS7_DETACHED
        );

        if (!$status) {
            throw new Exception("Error al firmar el TRA con OpenSSL: " . openssl_error_string());
        }

        // Extraer el CMS en Base64 limpiando cabeceras MIME y saltos de línea
        $contenidoTmp = file_get_contents($traTmpFile);
        $partes = explode("\n\n", str_replace("\r", "", $contenidoTmp));
        
        // El contenido Base64 se encuentra a partir de la cabecera MIME
        $cms = "";
        if (isset($partes[1])) {
            // Unir si hubiese más fragmentos y eliminar saltos de línea
            array_shift($partes);
            $cms = implode("", array_map('trim', $partes));
        } else {
            // Fallback si no tiene doble salto
            $cms = trim($contenidoTmp);
        }

        @unlink($traXmlFile);
        @unlink($traTmpFile);

        // Conectar a WSAA
        $client = new SoapClient(
            $this->wsdlWsaa, 
            $this->getSoapOptions('https://wsaahomo.afip.gov.ar/ws/services/LoginCms')
        );

        $loginCms = $client->loginCms(['in0' => $cms]);
        file_put_contents($taFile, $loginCms->loginCmsReturn);

        $taXml = simplexml_load_string($loginCms->loginCmsReturn);
        return [
            'token' => (string)$taXml->credentials->token,
            'sign'  => (string)$taXml->credentials->sign
        ];
    }

    /**
     * Consulta el último número de comprobante emitido
     */
    public function getUltimoComprobante($ptoVta, $cbteTipo) {
    $auth = $this->obtenerTokenSign();

    $client = new SoapClient(
        $this->wsdlWsfe, 
        $this->getSoapOptions('https://wswhomo.afip.gov.ar/wsfev1/service.asmx')
    );

    $params = [
        'Auth' => [
            'Token' => $auth['token'],
            'Sign'  => $auth['sign'],
            'Cuit'  => $this->cuit
        ],
        'PtoVta'   => (int)$ptoVta,
        'CbteTipo' => (int)$cbteTipo
    ];

    $resp = $client->FECompUltimoAutorizado($params);
    $res = $resp->FECompUltimoAutorizadoResult;

    if (isset($res->Errors)) {
        $err = $res->Errors->Err;
        $msg = is_array($err) ? $err[0]->Msg : $err->Msg;
        throw new Exception("AFIP Error al consultar último comprobante: " . $msg);
    }

    return (int)$res->CbteNro;
}

    /**
     * Envía la solicitud de autorización (CAE)
     */
    public function solicitarCAE($feCAEReq) {
        $auth = $this->obtenerTokenSign();
        $client = new SoapClient(
            $this->wsdlWsfe, 
            $this->getSoapOptions('https://wswhomo.afip.gov.ar/wsfev1/service.asmx')
        );

        $params = [
            'Auth' => [
                'Token' => $auth['token'],
                'Sign'  => $auth['sign'],
                'Cuit'  => $this->cuit
            ],
            'FeCAEReq' => $feCAEReq
        ];

        return $client->FECAESolicitar($params);
    }

    /**
     * Emite una Factura B a Consumidor Final y retorna CAE y Vencimiento
     */
    public function emitirFacturaB($ptoVta, $importeTotal, $docNro = "0", $docTipo = 99) {
        $auth = $this->obtenerTokenSign();
        
        // Siguiente número a emitir
        $ultimoNro = $this->getUltimoComprobante($ptoVta, 6);
        $cbteNro = $ultimoNro + 1;

        // Desglose IVA 21%
        $impNeto = round($importeTotal / 1.21, 2);
        $impIVA  = round($importeTotal - $impNeto, 2);
        $fechaCbte = date('Ymd');
            if ($fechaCbte < '20261012') {
                $fechaCbte = '20261012';
            }

        $req = [
            'FeCabReq' => [
                'CantReg'  => 1,
                'PtoVta'   => (int)$ptoVta,
                'CbteTipo' => 6 // Factura B
            ],
            'FeDetReq' => [
                'FECAEDetRequest' => [
                    'Concepto'     => 1, // Productos
                    'DocTipo'      => (int)$docTipo, // 99 = Consumidor Final
                    'DocNro'       => $docNro,
                    'CbteDesde'    => $cbteNro,
                    'CbteHasta'    => $cbteNro,
                    'CbteFch'      => $fechaCbte,
                    'ImpTotal'     => (float)$importeTotal,
                    'ImpTotConc'   => 0,
                    'ImpNeto'      => (float)$impNeto,
                    'ImpOpEx'      => 0,
                    'ImpTrib'      => 0,
                    'ImpIVA'       => (float)$impIVA,
                    'MonId'        => 'PES',
                    'MonCotiz'     => 1,
                    'CondicionIVAReceptorId' => 5, // Consumidor Final
                    'Iva' => [
                        'AlicIva' => [
                            [
                                'Id'      => 5, // 21%
                                'BaseImp' => (float)$impNeto,
                                'Importe' => (float)$impIVA
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $client = new SoapClient(
            $this->wsdlWsfe, 
            $this->getSoapOptions('https://wswhomo.afip.gov.ar/wsfev1/service.asmx')
        );

        $params = [
            'Auth' => [
                'Token' => $auth['token'],
                'Sign'  => $auth['sign'],
                'Cuit'  => $this->cuit
            ],
            'FeCAEReq' => $req
        ];

        $resp = $client->FECAESolicitar($params);
        $resultado = $resp->FECAESolicitarResult;

        if (isset($resultado->Errors)) {
            $err = $resultado->Errors->Err;
            $msg = is_array($err) ? $err[0]->Msg : $err->Msg;
            throw new Exception("AFIP Error: " . $msg);
        }

        $det = $resultado->FeDetResp->FECAEDetResponse;
        if (is_array($det)) $det = $det[0];

        if ($det->Resultado !== 'A') {
            $obsMsg = "Comprobante Rechazado.";
            if (isset($det->Observaciones)) {
                $obs = $det->Observaciones->Obs;
                $obsMsg .= " " . (is_array($obs) ? $obs[0]->Msg : $obs->Msg);
            }
            throw new Exception($obsMsg);
        }

        return [
            'resultado'   => $det->Resultado,
            'cae'         => (string)$det->CAE,
            'cae_vto'     => (string)$det->CAEFchVto, // AAAAMMDD
            'pto_vta'     => $ptoVta,
            'cbte_nro'    => $cbteNro,
            'imp_total'   => $importeTotal,
            'fecha'       => $fechaCbte
        ];
    }
}