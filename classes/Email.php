<?php 
namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email,$nombre,$token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '00b7e2e9cf6fe4';
        $mail->Password = '747d6435f5075a';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','uptask.com');
        $mail->Subject = 'Confirma tu cuenta';
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola ". $this->nombre."</strong> Has creado tu cuenta en Uptask, solo debes confirmarla en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/confirmar?token=". $this->token."'>Confrimar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body=$contenido;

        //Enviar email
        $mail->send();
    }

    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '00b7e2e9cf6fe4';
        $mail->Password = '747d6435f5075a';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com','uptask.com');
        $mail->Subject = 'Reestablece tu Password';
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = '<html>';
        $contenido .= "<p><strong>Hola ". $this->nombre."</strong> Has clic en el enlace para reestablecer tu password.</p>";
        $contenido .= "<p>Presiona aqui: <a href='http://localhost:3000/restablecer?token=". $this->token."'>Reestablecer Password</a></p>";
        $contenido .= "<p>Si no solicitaste este servicio, por favor ignora este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body=$contenido;

        //Enviar email
        $mail->send();
    }
}

?>