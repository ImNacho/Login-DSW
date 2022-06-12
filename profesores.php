<?php
session_start();
if (!isset($_SESSION['usuario_identificacion'])) {
	header("Location: index.php");
}

if ($_SESSION["usuario_tipo"] == 2) {
	header("Location: listado.php");
}

if (isset($_POST['cerrar_sesion'])) {
	session_destroy();
	header("Location: index.php");
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
			<a class="navbar-brand" href="#">DASHBOARD PROFESORES</a>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
				<button class="btn btn-danger" name="cerrar_sesion" type="submit">
					Cerrar sesion
				</button>
			</form>
		</div>
	</nav>
	<main class="d-flex gap-4 p-4">
		<div class="card" style="width: 18rem;">
			<img src="https://concepto.de/wp-content/uploads/2018/08/persona-e1533759195177-800x400.jpg" class="card-img-top" alt="...">
			<div class="card-body">
				<h5 class="card-title">Alexander Diaz Rios</h5>
				<p class="card-text">Programa de sistemas.</p>
				<a href="#" class="btn btn-primary">Ver estudiante</a>
			</div>
		</div>
		<div class="card" style="width: 18rem;">
			<img src="https://www.caritas.org.mx/wp-content/uploads/2019/02/cualidades-persona-humanitaria.jpg" class="card-img-top" alt="...">
			<div class="card-body">
				<h5 class="card-title">Carla Betancur Carrasco</h5>
				<p class="card-text">Programa de sistemas.</p>
				<a href="#" class="btn btn-primary">Ver estudiante</a>
			</div>
		</div>
		<div class="card" style="width: 18rem;">
			<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRFogB3c0aNtnWrl9WPR9VHd4RZXjx5ZAT4Dw&usqp=CAU" class="card-img-top" alt="...">
			<div class="card-body">
				<h5 class="card-title">Ingrid Patricia Arrieta Mena</h5>
				<p class="card-text">Programa de mecanica.</p>
				<a href="#" class="btn btn-primary">Ver estudiante</a>
			</div>
		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>