<?php

date_default_timezone_set('America/Bogota');

require __DIR__ . "/vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Correo
{
	public static function enviarCorreo($emialDestinatario, $asunto, $mensajeHtml)
	{
		$host = "smtp.gmail.com";
		$email = "corprugm@gmail.com";
		$pass = "pszuqefofcgifwun";
		$puerto = 465;

		$mail = new PHPMailer(true);

		try {
			$mail->SMTPDebug = 0;
			$mail->isSMTP();
			$mail->Host = $host;
			$mail->SMTPAuth = true;
			$mail->Username = $email;
			$mail->Password = $pass;
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = $puerto;

			//Recipients
			$mail->setFrom($email, 'SISTEMA DE USUARIOS');
			$mail->addAddress($emialDestinatario);

			$mail->isHTML(true);
			$mail->Subject = $asunto;
			$mail->Body = $mensajeHtml;

			$mail->CharSet = 'UTF-8';
			$mail->send();
			return ["status" => "success"];
		} catch (Exception $e) {
			return ["status" => "failCorreo", "error" => $mail->ErrorInfo];
		}
	}

	public static function enviarCorreoConfirmarEmail($destinatario, $email_destinatario, $token)
	{
		$url = $_SERVER['HTTP_HOST'] . "/" . basename(__DIR__) . "/activar.php?token=" . $token;

		$body_html = "
			Buenas $destinatario.<br><br>
			Para confirmar tu correo electrónico, por favor haz click en el siguiente enlace:<br><br>
			<a href='$url'>Confirmar correo</a>
		";

		return self::enviarCorreo($email_destinatario, 'CONFIRMACIÓN DE CORREO', $body_html);
	}

	public static function enviarCorreoRestablecerLogin($destinatario, $email_destinatario, $codigo, $codigoHash)
	{
		$url = $_SERVER['HTTP_HOST'] . "/" . basename(__DIR__) . "/restablecer_password.php?token=" . $codigoHash;

		$body_html = "
			Buenas $destinatario.<br><br>
			Con el siguiente codigo podras recuperar tu correo: <b>$codigo</b><br><br>
			Por favor oprime el siguiente enlace:<br><br>
			<a href='$url'>Recuperar contraseña</a>
		";

		return self::enviarCorreo($email_destinatario, 'RESTABLECER CONTRASEÑA', $body_html);
	}
}
