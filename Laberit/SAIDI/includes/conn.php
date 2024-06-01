<?php // Conexión con la base de datos en PDO.
try // Intenta la conexión
{
	// $conn = new PDO('mysql:host=localhost;dbname=macs', "root", $_ENV["MySQL"]);
	$conn = new PDO('mysql:host=localhost;dbname=macs', "root", "");
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) // En caso de error
{
	echo 'Error: ' . $e->getMessage(); // Muestra el error.
}

use InfluxDB2\Client;

$org = 'laberit';
$bucket = 'SAIDI_DATA';

// $org = 'Laberit';
// $bucket = 'MACDB';

$client = new Client([
    "url" => "http://localhost:8086",
    "token" => $_ENV["Influx-Token"],
]);
?>