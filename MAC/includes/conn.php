<?php // Conexión con la base de datos en PDO.
session_start(); // Incluyo el session_start() ya que se usará en casi todos los scripts.
try // Intenta la conexión
{
	$conn = new PDO('mysql:host=localhost;dbname=macs', "root", $_ENV["MySQL"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) // En caso de error
{
	echo 'Error: ' . $e->getMessage(); // Muestra el error.
}

use InfluxDB2\Client;
// use InfluxDB2\Model\WritePrecision;

$org = 'FP';
$bucket = 'MACDB';

$client = new Client([
    "url" => "http://localhost:8086",
    "token" => $_ENV["INFLUX_TOKEN"],
]);
?>