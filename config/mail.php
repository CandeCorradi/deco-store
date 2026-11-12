<?php
// config/mail.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

function enviarCorreoComprobante($destinatarioEmail, $destinatarioNombre, $numeroVenta, $cuerpoHtml)
{
    $mail = new PHPMailer(true);

    try {
        // 1. Configuración del Servidor SMTP (cPanel d26.com.ar)
        //$mail->isSMTP();
        //$mail->Host       = 'mail.d26.com.ar';
        //$mail->SMTPAuth   = true;
        //$mail->Username   = 'candecorradi01@d26.com.ar';
        //$mail->Password   = 'Isp203040';
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        //$mail->Port       = 465;
        //$mail->Timeout    = 30;
        $mail->CharSet    = 'UTF-8';

        // 1. Configuración del Servidor SMTP (en la nube)
        $mail->isSMTP();
        $mail->Host       = 'localhost'; // El servidor de correo de la misma máquina en la nube
        $mail->SMTPAuth   = true;
        $mail->Username   = 'candecorradi01@d26.com.ar';
        $mail->Password   = 'Isp203040';
        $mail->Port       = 587; // Puerto local estándar de cPanel
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
        $mail->Timeout    = 10;
        $mail->CharSet    = 'UTF-8';

        // Opciones SSL para evitar bloqueos de certificados en entorno local (WAMP/Windows)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        // 2. Remitente y Destinatario
        $mail->setFrom('candecorradi01@d26.com.ar', 'DecoStore - Mobiliario & Interiores');
        $mail->addAddress($destinatarioEmail, $destinatarioNombre);
        $mail->addReplyTo('candecorradi01@d26.com.ar', 'DecoStore');

        // 3. Contenido
        $mail->isHTML(true);
        $mail->Subject = "Comprobante de Venta #{$numeroVenta} - DecoStore";
        $mail->Body    = $cuerpoHtml;
        $mail->AltBody = "Estimado/a {$destinatarioNombre},\n\nLe enviamos el detalle correspondiente a su comprobante de venta #{$numeroVenta}.\n\nGracias por confiar en DecoStore.";

        $mail->send();
        return ['exito' => true, 'error' => null];

    } catch (Exception $e) {
        return ['exito' => false, 'error' => $mail->ErrorInfo];
    }
}