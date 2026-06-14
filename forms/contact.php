<?php
// --- Requiere PHPMailer ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ajustar las rutas a donde está PHPMailer
require '../assets/vendor/phpmailer/src/Exception.php';
require '../assets/vendor/phpmailer/src/PHPMailer.php';
require '../assets/vendor/phpmailer/src/SMTP.php';

// --- CONFIGURACIÓN DEL CORREO ---
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP (Gmail)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'grupoghi.housing@gmail.com';  // Tu Gmail
    $mail->Password   = 'bcgc vukv bcnr bbue';          // Contraseña de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // --- REMITENTE Y DESTINATARIOS ---
    $mail->setFrom('contacto@ghi-housing.com', 'GHI Housing');
    $mail->addAddress('grupoghi.housing@gmail.com', 'Equipo GHI Housing');
    $mail->addReplyTo($_POST['email'], $_POST['nombre']);

    // --- CONTENIDO DEL MENSAJE ---
    $mail->isHTML(true);
    $mail->Subject = 'Nuevo mensaje desde formulario web - GHI Housing';

    // Sanitizar entradas
    $nombre  = htmlspecialchars($_POST['nombre'] ?? '');
    $email   = htmlspecialchars($_POST['email'] ?? '');
    $mensaje = htmlspecialchars($_POST['mensaje'] ?? '');

    // Cuerpo del correo
    $mail->Body = "
        <h2>Nuevo mensaje desde la web</h2>
        <p><strong>Nombre:</strong> {$nombre}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Mensaje:</strong></p>
        <p>{$mensaje}</p>
        <hr>
        <small>Enviado desde el formulario de contacto en <a href='https://www.ghi-housing.com'>ghi-housing.com</a></small>
    ";

    // --- ENVÍO ---
    $mail->send();
    echo "<p style='color:green;'>✅ Mensaje enviado correctamente. Gracias por contactarnos.</p>";

} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error al enviar el mensaje: {$mail->ErrorInfo}</p>";
}
?>
