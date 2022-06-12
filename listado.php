<?php
session_start();
if(!isset($_SESSION['usuario_identificacion'])){
	header("Location: index.php");
}

if ($_SESSION["usuario_tipo"] == 1) {
	header("Location: profesores.php");
}

if (isset($_POST['cerrar_sesion'])) {
	session_destroy();
	header("Location: index.php");
}

require "conexion.php";
$conexion = new Conexion();
$conexion->conectar();
$sql_usuarios = "SELECT u.*, t.nombre as tipo_nombre, d.nombre as dependencia_nombre FROM usuarios u
	INNER JOIN tipos t ON u.tipo_id = t.id
	INNER JOIN dependencias d ON u.dependencia_id = d.id
	WHERE u.identificacion != $_SESSION[usuario_identificacion];
";

$usuarios = $conexion->sql($sql_usuarios);
$conexion->cerrar();
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
			<a class="navbar-brand" href="#">LISTADO DE USUARIOS</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="registrar.php">Registrar</a>
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
			<table class="table">
				<thead>
					<tr>
						<th scope="col">#</th>
						<th scope="col">Identificacion</th>
						<th scope="col">Nombre</th>
						<th scope="col">Email</th>
						<th scope="col">Tipo</th>
						<th scope="col">Dependencia</th>
						<th scope="col">Usuario</th>
						<th scope="col">Estado</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($usuarios as $usuario) {
						
						$estado = intval($usuario['estado']);

						if ($estado == 0) {
							$estado = "Por confirmar email";
						} else {
							$estado = "Activo";
						} 

						echo "<tr>";
						echo "<td scope='row'>" . $usuario['id'] . "</td>";
						echo "<td>" . $usuario['identificacion'] . "</td>";
						echo "<td>" . $usuario['nombres'] . " " . $usuario['apellidos'] . "</td>";
						echo "<td>" . $usuario['email'] . "</td>";
						echo "<td>" . $usuario['tipo_nombre'] . "</td>";
						echo "<td>" . $usuario['dependencia_nombre'] . "</td>";
						echo "<td>" . $usuario['username'] . "</td>";
						echo "<td>" . $estado . "</td>";
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>