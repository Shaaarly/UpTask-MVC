<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;
    

    public function __construct($nombre, $email, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }   

    public function enviarConfirmacion() {
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd488a2ae9d0fda';
        $mail->Password = '85e2b1550530ee';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com'); 
        $mail->Subject = 'Confirma tu Cuenta';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>! Has Creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;
    
        // Enviar Email
        $mail->send();
    }


    public function enviarInstrucciones() {
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'd488a2ae9d0fda';
        $mail->Password = '85e2b1550530ee';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com'); 
        $mail->Subject = 'Reestablece tu password';

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>! Parece que has olvidado tu password, sigue el siguiente enlace parta recuperarla</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/reestablecer?token=" . $this->token . "'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si tu no solicitaste este cambio, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;
    
        // Enviar Email
        $mail->send();
    }
}