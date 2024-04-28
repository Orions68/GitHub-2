<?php
session_start(); // Incluyo el session_start() ya que se usará en casi todos los scripts.
try // Intenta la conexión
{
	$conn = new PDO('mysql:host=localhost;dbname=mails', "root", $_ENV["MySQL"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) // En caso de error
{
	echo 'Error: ' . $e->getMessage(); // Muestra el error.
}
?>