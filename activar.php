<?php

require "conexion.php";

$token = $_GET['token'];

if(!$token){
	echo "No se ha recibido el token";
}

$conexion = new Conexion();
$conexion->conectar();

$existe = $conexion->sql("SELECT * FROM usuarios WHERE token = '$token'");
if(count($existe) > 0){
	$conexion->sql("UPDATE usuarios SET estado = 1, token = NULL WHERE token = '$token'");
	$conexion->cerrar();
	session_start();
	$_SESSION['usuario_identificacion'] = $existe[0]['identificacion'];
	$_SESSION['usuario_tipo'] = $existe[0]['tipo_id'];
	header("Location: index.php");
} else {
	$conexion->cerrar();
	echo "El token no es valido";
}