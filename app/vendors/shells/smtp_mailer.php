<?php
// /usr/bin/php /home/adrian/trabajo/www/sigemv2/cake/console/cake.php smtp_mailer -app /home/adrian/trabajo/www/sigemv2/app/
App::import('Vendor','SMTPMailer',array('file' => 'SMTPMailer.php'));
App::import('Vendor', 'PHPMailer/src/SMTP');
App::import('Vendor', 'PHPMailer/src/Exception');
App::import('Vendor', 'SMTPMailer');

class SmtpMailerShell extends Shell {

    var $uses = array();

    function main() {
        $to = 'm.adrian.torres@gmail.com'; // ← Reemplazá por tu correo

        try {
            $mailer = new SMTPMailer();
            $mailer->isHTML(true);
            $mailer->Subject = "🧪 Test desde CakePHP 1.2 por línea de comandos";
            $mailer->Body = "<h3>Correo de prueba</h3><p>Este mensaje fue enviado desde un Shell en CakePHP 1.2 usando PHPMailer.</p>";
            $mailer->addAddress($to);

            if ($mailer->send()) {
                $this->out("✅ Correo enviado correctamente a $to");
            } else {
                $this->out("❌ Falló el envío.");
            }

        } catch (Exception $e) {
            $this->out("❌ Error al enviar: " . $e->getMessage());
        }
    }
}
