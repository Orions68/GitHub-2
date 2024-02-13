<?php
if (isset($_POST["name"]))
{
    $name = $_POST["name"];
    echo "<h1>Hola $name</h1>";
}