<?php

date_default_timezone_set('America/Bogota');

class Conexion
{
	private $conexion;
	private $host;
	private $bd;
	private $usuario;
	private $pass;
	
	public function conectar()
	{
		$config = file_get_contents(__DIR__ . "/config/conexion.json");
		$config = json_decode($config, false);

		$this->host = $config->host;
		$this->bd = $config->bd;
		$this->usuario = $config->usuario;
		$this->pass = $config->pass;

		$this->conexion = new mysqli($this->host, $this->usuario, $this->pass, $this->bd);
		if ($this->conexion->connect_errno) echo 'Falla al conectar con MySQL: ' . $this->conexion->connect_error;
	}

	public function sql($consulta)
	{
		$consulta = strtolower($consulta);
		
		$this->conexion->set_charset("utf8");
		
		$resultado = $this->conexion->query($consulta);
		
		if (strpos($consulta, 'select') !== false) {
			$resultado_array = [];
			while ($fila = $resultado->fetch_assoc()) {
				$resultado_array[] = $fila;
			}
			return $resultado_array;
		} else {
			return $resultado;
		}
	}

	public function cerrar()
	{
		$this->conexion->close();
	}
}


