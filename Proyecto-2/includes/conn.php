<?php
session_start();
try
{
    $conn = new PDO("mysql:host=localhost;dbname=project1", 'root', 'Anubis68');
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
?>