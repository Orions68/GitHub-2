<?php // Conexión con la base de datos en PDO.
session_start(); // Incluyo el session_start() ya que se usará en casi todos los scripts.
try // Intenta la conexión
{
	$conn = new PDO('mysql:host=localhost;dbname=macs', "root", "Anubis@68");
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) // En caso de error
{
	echo 'Error: ' . $e->getMessage(); // Muestra el error.
}

use InfluxDB2\Client;
// use InfluxDB2\Model\WritePrecision;

$org = 'laberit';
$bucket = 'MACDB';

$client = new Client([
    "url" => "http://localhost:8086",
    "token" => "VgWawXJKf2oSbVzJF7yfkQWzCL1RMWQx1Kj__hRm_8xS41GiSm6y0yvs0xWtkIDllq8EZNsybMWIE3T55LtLtg==",
]);
?>