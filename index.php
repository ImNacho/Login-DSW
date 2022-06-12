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

$username = "";
$password = "";

$errores = [];

if (isset($_POST["submit"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$usuario = new Usuario();
	$login = $usuario->login($username, $password);

	if ($login["status"] == "failValidacion") {
		$errores = $login["errors"];
	} else if ($login["status"] == "failConfirmacion") {
		$errores = ["username" => ["No ha confirmado su cuenta"]];
	} else if ($login["status"] == "failLogin") {
		$errores = ["username" => ["Usuario y/o contraseña incorrectos"]];
	} else {
		if ($_SESSION["usuario_tipo"] == 1) {
			header("Location: profesores.php");
		} else {
			header("Location: listado.php");
		}
	}
}

?>

<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
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
					<form class="card bg-dark text-white" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="border-radius: 1rem;">
						<div class="card-body p-5 text-center">

							<div class="mt-md-4">

								<h2 class="fw-bold mb-2 text-uppercase">Login</h2>
								<p class="text-white-50 mb-3">Por favor ingrese su informacion de sesion!</p>

								<div class="form-outline form-white mb-3">
									<label class="form-label" for="username">Usuario</label>
									<input value="<?php echo $username; ?>" type="text" id="username" name="username" class="form-control form-control-lg" />
									<?php
									if (isset($errores["username"])) {
										foreach ($errores["username"] as $error) {
											echo "<small class='text-danger'>$error</small>";
										}
									}
									?>
								</div>

								<div class="form-outline form-white mb-3">
									<label class="form-label" for="password">Contraseña</label>
									<input value="<?php echo $password; ?>" type="password" id="password" name="password" class="form-control form-control-lg" />
									<?php
									if (isset($errores["password"])) {
										foreach ($errores["password"] as $error) {
											echo "<small class='text-danger'>$error</small>";
										}
									}
									?>
								</div>

								<a class="small text-white-50" href="restablecer.php">¿Olvidaste tu contraseña?</a>

								<button class="btn btn-outline-light btn-lg px-5 mt-4" type="submit" name="submit">Iniciar Sesion</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>