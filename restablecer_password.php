<?php

require "usuario.php";

session_start();
if (isset($_SESSION['usuario_identificacion'])) {
	if ($_SESSION["usuario_tipo"] == 1) {
		header("Location: profesores.php");
	} else {
		header("Location: listado.php");
	}
}

$codigo = "";
$password = "";

$error_plataforma = "";
$errores = [];

$token = $_GET['token'];

if(!$token){
	$error_plataforma = "No se ha recibido el token";
}

$conexion = new Conexion();
$conexion->conectar();

$existe = $conexion->sql("SELECT * FROM usuarios WHERE token = '$token'");

$conexion->cerrar();

if(count($existe) == 0){
	$error_plataforma = "El token no es valido";
} 

if (isset($_POST["submit"])) {
	$codigo = $_POST["codigo"];
	$password = $_POST["password"];

	$usuario = new Usuario();
	$restablecer = $usuario->restablecerPassword($codigo, $password);

	if ($restablecer["status"] == "failValidacion") {
		$errores = $restablecer["errors"];
	} else if ($restablecer["status"] == "failConfirmacion") {
		$errores = ["codigo" => ["No ha confirmado su cuenta"]];
	} else if ($restablecer["status"] == "failCodigo") {
		$errores = ["codigo" => ["El codigo es incorrecto"]];
	} else if ($restablecer["status"] == "failActualizar") {
		$error_plataforma = "Error al actualizar la contraseña";
	} else {
		header("Location: index.php");
	}
}

?>

<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Restablecer</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
	<style>
		.gradient-custom {
			min-height: 100vh;
			/* fallback for old browsers */
			background: #6a11cb;

			/* Chrome 10-25, Safari 5.1-6 */
			background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));

			/* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
			background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1))
		}
	</style>
</head>

<body>
	<section class="gradient-custom">
		<div class="container py-5">
			<div class="row d-flex justify-content-center align-items-center">
				<div class="col-12 col-md-8 col-lg-6 col-xl-5">
					<form class="card bg-dark text-white" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?token=" . $token ?>" style="border-radius: 1rem;">
						<div class="card-body p-5 text-center">

							<div class="mt-md-4">

								<h2 class="fw-bold mb-2 text-uppercase">Nueva Contraseña</h2>
								<p class="text-white-50 mb-3">Por favor ingrese la informacion del correo!</p>

								<div class="form-outline form-white mb-3">
									<label class="form-label" for="codigo">Codigo</label>
									<input value="<?php echo $codigo; ?>" type="text" id="codigo" name="codigo" class="form-control form-control-lg" />
									<?php
									if (isset($errores["codigo"])) {
										foreach ($errores["codigo"] as $error) {
											echo "<small class='text-danger'>$error</small><br>";
										}
									}
									?>
								</div>

								<div class="form-outline form-white mb-3">
									<label class="form-label" for="password">Nueva Contraseña</label>
									<input value="<?php echo $password; ?>" type="password" id="password" name="password" class="form-control form-control-lg" />
									<?php
									if (isset($errores["password"])) {
										foreach ($errores["password"] as $error) {
											echo "<small class='text-danger'>$error</small><br>";
										}
									}
									?>
								</div>

								<button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Guardar</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<?php
	if ($error_plataforma != "") {
		echo "<script> Swal.fire('', '$error_plataforma','error');</script>";
	}
	?>
</body>

</html>