<?php
session_start();
if (!isset($_SESSION['usuario_identificacion'])) {
	header("Location: index.php");
}

if ($_SESSION["usuario_tipo"] == 1) {
	header("Location: profesores.php");
}

if (isset($_POST['cerrar_sesion'])) {
	session_destroy();
	header("Location: index.php");
}

require "usuario.php";

$conexion = new Conexion();
$conexion->conectar();
$tipos = $conexion->sql("SELECT * FROM tipos");
$dependencias = $conexion->sql("SELECT * FROM dependencias");
$conexion->cerrar();

$identificacion = "";
$nombres = "";
$apellidos = "";
$email = "";
$username = "";
$password = "";
$tipo = "";
$dependencia = "";

$errores = [];
$error_plataforma = "";

if (isset($_POST['submit'])) {

	$identificacion = $_POST['identificacion'];
	$nombres = $_POST['nombres'];
	$apellidos = $_POST['apellidos'];
	$email = $_POST['email'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$tipo = $_POST['tipo'];
	$dependencia = $_POST['dependencia'];

	$usuario = new Usuario();
	$registrar = $usuario->registrar($_POST);
	
	if ($registrar["status"] == "failValidacion") {
		$errores = $registrar["errors"];
	} else if ($registrar["status"] == "failInsertar") {
		$error_plataforma = "Error al registrar usuario";
	} else if ($registrar["status"] == "failCorreo") {
		$error_plataforma = "Error al enviar el correo de confirmacion";
	} else {
		header("Location: listado.php");
	}

}
?>

<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
</head>

<body>
	<nav class="navbar navbar-expand-lg bg-light">
		<div class="container-fluid">
			<a class="navbar-brand" href="listado.php">REGISTRO DE USUARIOS</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="listado.php">Listado</a>
					</li>
				</ul>
			</div>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
				<button class="btn btn-danger" name="cerrar_sesion" type="submit">
					Cerrar sesion
				</button>
			</form>
		</div>
	</nav>
	<main>
		<div class="p-4">
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
				<div class="mb-3">
					<label for="identificacion" class="form-label">Identificacion</label>
					<input type="number" value="<?php echo $identificacion; ?>" name="identificacion" id="identificacion" class="form-control">
					<?php
					if (isset($errores["identificacion"])) {
						foreach ($errores["identificacion"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="nombres" class="form-label">Nombres</label>
					<input type="text" value="<?php echo $nombres; ?>" name="nombres" id="nombres" class="form-control">
					<?php
					if (isset($errores["nombres"])) {
						foreach ($errores["nombres"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="apellidos" class="form-label">Apellidos</label>
					<input type="text" value="<?php echo $apellidos; ?>" name="apellidos" id="apellidos" class="form-control">
					<?php
					if (isset($errores["apellidos"])) {
						foreach ($errores["apellidos"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="email" class="form-label">Email</label>
					<input type="email" value="<?php echo $email; ?>" name="email" id="email" class="form-control">
					<?php
					if (isset($errores["email"])) {
						foreach ($errores["email"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="username" class="form-label">Usuario</label>
					<input type="text" value="<?php echo $username; ?>" name="username" id="username" class="form-control">
					<?php
					if (isset($errores["username"])) {
						foreach ($errores["username"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Contraseña</label>
					<input type="password" value="<?php echo $password; ?>" name="password" id="password" class="form-control">
					<?php
					if (isset($errores["password"])) {
						foreach ($errores["password"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="tipo" class="form-label">Tipo</label>
					<select id="tipo" name="tipo" class="form-select">
						<?php
						foreach ($tipos as $tipoBd) {
							echo "<option " . ($tipo == $tipoBd['id'] ? 'selected' : '') . " value='" . $tipoBd['id'] . "'>" . $tipoBd['nombre'] . "</option>";
						}
						?>
					</select>
					<?php
					if (isset($errores["tipo"])) {
						foreach ($errores["tipo"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<div class="mb-3">
					<label for="dependencia" class="form-label">Dependencia</label>
					<select id="dependencia" name="dependencia" class="form-select">
						<?php
						foreach ($dependencias as $dep) {
							echo "<option " . ($dependencia == $dep['id'] ? 'selected' : '') . " value='" . $dep['id'] . "'>" . $dep['nombre'] . "</option>";
						}
						?>
					</select>
					<?php
					if (isset($errores["dependencia"])) {
						foreach ($errores["dependencia"] as $error) {
							echo "<small class='text-danger'>$error</small><br>";
						}
					}
					?>
				</div>
				<button type="submit" name="submit" class="btn btn-primary">Registrar</button>
			</form>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<?php
	if ($error_plataforma != "") {
		echo "<script> Swal.fire('', '$error_plataforma','error');</script>";
	}
	?>
</body>

</html>