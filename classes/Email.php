<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email,$nombre,$token) {
        
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;

        
    }

    public function enviarConfirmacion() {

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ed1849356dd6a6';
        $mail->Password = '81fbc18a05c954';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','uptask.com');
        $mail->Subject = 'Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . " </strong>has creado una cuenta en uptask, solo debes confirmar
        en el siguiente enlace<p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:8080/confirmar?token=" . $this->token ."'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //enviar el email

        $mail->send();

    }

    public function enviarInstrucciones() {

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ed1849356dd6a6';
        $mail->Password = '81fbc18a05c954';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','uptask.com');
        $mail->Subject = 'Restablece tu password';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . " </strong>Has olvidado tu password?, presiona
        en el siguiente enlace para restablecer tu password<p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:8080/restablecer?token=" . $this->token ."'>Restablecer password</a></p>";
        $contenido .= "<p>Si tu no solicitaste restalcer tu password, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        //enviar el email

        $mail->send();

    }
}