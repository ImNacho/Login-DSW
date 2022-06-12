<?php
require __DIR__ . "/vendor/autoload.php";

require "conexion.php";
require "correo.php";

use Valitron\Validator as Validate;

Validate::langDir(__DIR__ . "/vendor/vlucas/valitron/lang");
Validate::lang('es');

class Usuario extends Conexion
{
	private const UNIQUE_SALT = '5&nL*dF4';

	public function validarDataRegistro($data)
	{
		$this->conectar();
		$maximo_id_tipo = $this->sql("SELECT MAX(id) as max_id FROM tipos")[0]["max_id"];
		$maximo_id_dependencia = $this->sql("SELECT MAX(id) as max_id FROM dependencias")[0]["max_id"];
		$this->cerrar();

		$validate = new Validate($data);

		// Identificacion
		$validate->rule("required", "identificacion");
		$validate->rule("numeric", "identificacion");
		$validate->rule("lengthMin", "identificacion", 6);
		$validate->rule("lengthMax", "identificacion", 20);

		// Nombres
		$validate->rule("required", "nombres");
		$validate->rule("regex", "nombres", "/^[a-zA-Z\s]{0,100}$/");
		$validate->rule("lengthMin", "nombres", 3);
		$validate->rule("lengthMax", "nombres", 100);

		// Apellidos
		$validate->rule("required", "apellidos");
		$validate->rule("regex", "apellidos", "/^[a-zA-Z\s]{0,100}$/");
		$validate->rule("lengthMin", "apellidos", 3);
		$validate->rule("lengthMax", "apellidos", 100);

		// Email
		$validate->rule("required", "email");
		$validate->rule("email", "email");
		$validate->rule("lengthMin", "email", 15);
		$validate->rule("lengthMax", "email", 150);

		// Tipo
		$validate->rule("required", "tipo");
		$validate->rule("numeric", "tipo");
		$validate->rule("min", "tipo", 1);
		$validate->rule("max", "tipo", intval($maximo_id_tipo));

		// Username
		$validate->rule("required", "username");
		$validate->rule("regex", "username", "/^[a-zA-Z0-9]{0,20}$/");
		$validate->rule("lengthMin", "username", 10);
		$validate->rule("lengthMax", "username", 20);

		// Password
		$validate->rule("required", "password");
		$validate->rule("regex", "password", "/^[a-zA-Z0-9\.\_\-]{0,20}$/");
		$validate->rule("lengthMin", "password", 6);
		$validate->rule("lengthMax", "password", 20);

		// Dependencia
		$validate->rule("required", "dependencia");
		$validate->rule("numeric", "dependencia");
		$validate->rule("min", "dependencia", 1);
		$validate->rule("max", "dependencia", intval($maximo_id_dependencia));

		$errores = $validate->validate() ? [] : $validate->errors();

		if (count($errores) > 0) {
			return ["status" => "failValidacion", "errors" => $errores];
		}

		$data["email"] = strtolower($data["email"]);
		$data["username"] = strtolower($data["username"]);

		$this->conectar();

		$sql_cant_usuarios_igual = "SELECT COUNT(*) as cant FROM usuarios 
			WHERE identificacion = $data[identificacion] OR 
			LOWER(email) = '$data[email]' OR 
			LOWER(username) = '$data[username]'
		;";

		$cant_usuarios_igual = $this->sql($sql_cant_usuarios_igual)[0]["cant"];

		$this->cerrar();

		if ($cant_usuarios_igual > 0) {
			return [
				"status" => "failValidacion",
				"errors" => [
					"identificacion" => ["Ya existe un usuario con esta informacion"],
				]
			];
		}

		return ["status" => "success"];
	}

	public function registrar($data)
	{
		$validar = $this->validarDataRegistro($data);

		if ($validar["status"] == "failValidacion") {
			return $validar;
		}

		$data["password"] = hash('sha1', self::UNIQUE_SALT . $data["password"]);
		$data["email"] = strtolower($data["email"]);
		$data["token"] = hash('sha256', self::UNIQUE_SALT . date("Y-m-d H:i:s"));

		$sql_insertar = "INSERT INTO usuarios 
			(
				identificacion, 
				nombres, 
				apellidos, 
				email, 
				tipo_id, 
				username, 
				password, 
				dependencia_id,
				token,
				estado
			) VALUES (
				$data[identificacion], 
				'$data[nombres]', 
				'$data[apellidos]', 
				'$data[email]', 
				$data[tipo], 
				'$data[username]', 
				'$data[password]', 
				$data[dependencia],
				'$data[token]',
				0
			)
		;";

		$this->conectar();
		$insertado = $this->sql($sql_insertar);
		$this->cerrar();

		if ($insertado) {

			$correo = Correo::enviarCorreoConfirmarEmail(
				($data["nombres"] . " " . $data["apellidos"]),
				$data["email"],
				$data["token"]
			);

			if ($correo["status"] == "failCorreo") {
				return $correo;
			}
			
			return ["status" => "success"];
		} else {
			return ["status" => "failInsertar"];
		}
	}

	public function login($username, $password)
	{
		$validate = new Validate([
			"username" => $username,
			"password" => $password
		]);

		$validate->rule("required", "username");
		$validate->rule("required", "password");

		$errores = $validate->validate() ? [] : $validate->errors();

		if (count($errores) > 0) {
			return ["status" => "failValidacion", "errors" => $errores];
		}

		$username = strtolower($username);
		$password = hash('sha1', self::UNIQUE_SALT . $password);

		$this->conectar();

		$sql_buscar_usuario = "SELECT * FROM usuarios 
			WHERE 
				LOWER(username) = '$username' AND 
				password = '$password'
		;";

		$buscar_usuario = $this->sql($sql_buscar_usuario);

		$this->cerrar();

		if (count($buscar_usuario) > 0) {

			if (intval($buscar_usuario[0]["estado"]) == 0) {
				return ["status" => "failConfirmacion"];
			}

			$_SESSION["usuario_identificacion"] = $buscar_usuario[0]["identificacion"];
			$_SESSION["usuario_tipo"] = $buscar_usuario[0]["tipo_id"];
			return ["status" => "success"];
		} else {
			if (isset($_SESSION["usuario_identificacion"])) {
				session_destroy();
			}
			return ["status" => "failLogin"];
		}
	}

	public function restablecerLogin($usernameOEmail)
	{
		$validate = new Validate([
			"username_email" => $usernameOEmail
		]);

		$validate->rule("required", "username_email");

		$errores = $validate->validate() ? [] : $validate->errors();

		if (count($errores) > 0) {
			return ["status" => "failValidacion", "errors" => $errores];
		}

		$usernameOEmail = strtolower($usernameOEmail);

		$this->conectar();

		$sql_buscar_usuario = "SELECT * FROM usuarios 
			WHERE 
				LOWER(username) = '$usernameOEmail' OR 
				LOWER(email) = '$usernameOEmail'
		;";

		$buscar_usuario = $this->sql($sql_buscar_usuario);

		if (count($buscar_usuario) > 0) {

			$usuario = $buscar_usuario[0];

			if (intval($usuario["estado"]) == 0) {
				$this->cerrar();
				return ["status" => "failConfirmacion"];
			}

			$caracteres = '0123456789abcdefghijklmnopqrstuvwxyz';
			$codigo = substr(str_shuffle($caracteres), 0, 6);
			$codigoHash = hash('sha256', self::UNIQUE_SALT . $codigo);

			$actualizar_usuario = $this->sql("UPDATE usuarios SET token = '$codigoHash' WHERE id = '$usuario[id]'");
			
			$this->cerrar();

			if(!$actualizar_usuario){
				return ["status" => "failActualizar"];
			}

			$correo = Correo::enviarCorreoRestablecerLogin(
				($usuario["nombres"] . " " . $usuario["apellidos"]),
				$usuario["email"],
				$codigo,
				$codigoHash
			);

			if ($correo["status"] == "failCorreo") {
				return $correo;
			}
			
			return ["status" => "success"];
		} else {
			$this->cerrar();
			return ["status" => "failNoExiste"];
		}
	}

	public function restablecerPassword($codigo, $nuevaPassword)
	{
		$validate = new Validate([
			"codigo" => $codigo,
			"password" => $nuevaPassword
		]);

		// Codigo
		$validate->rule("required", "codigo");
		$validate->rule("length", "codigo", 6);

		// Password
		$validate->rule("required", "password");
		$validate->rule("regex", "password", "/^[a-zA-Z0-9\.\_\-]{0,20}$/");
		$validate->rule("lengthMin", "password", 6);
		$validate->rule("lengthMax", "password", 20);

		$errores = $validate->validate() ? [] : $validate->errors();

		if (count($errores) > 0) {
			return ["status" => "failValidacion", "errors" => $errores];
		}

		$codigoHash = hash('sha256', self::UNIQUE_SALT . $codigo);

		$this->conectar();

		$sql_buscar_usuario = "SELECT * FROM usuarios WHERE token = '$codigoHash';";

		$buscar_usuario = $this->sql($sql_buscar_usuario);

		if (count($buscar_usuario) > 0) {

			$usuario = $buscar_usuario[0];

			if (intval($usuario["estado"]) == 0) {
				$this->cerrar();
				return ["status" => "failConfirmacion"];
			}

			$nuevaPassword = hash('sha1', self::UNIQUE_SALT . $nuevaPassword);

			$actualizar_usuario = $this->sql("UPDATE usuarios SET password = '$nuevaPassword', token = NULL WHERE id = '$usuario[id]'");
			
			$this->cerrar();

			if(!$actualizar_usuario){
				return ["status" => "failActualizar"];
			}

			$_SESSION["usuario_identificacion"] = $usuario["identificacion"];
			$_SESSION["usuario_tipo"] = $usuario["tipo_id"];
			
			return ["status" => "success"];
		} else {
			$this->cerrar();
			return ["status" => "failCodigo"];
		}
	}
}
