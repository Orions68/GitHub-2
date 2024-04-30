<?php
include "vendor/autoload.php";

$filename = "test.cap"; // Asigna a $filename el valor data.txt, el nombre del fichero con los datos.
$file = fopen($filename, "r"); // Abre pata lectura el fichero data.txt.
if ($file) // Si se leyo.
{
    $head = getHead($file);
}
fclose($file);
echo $head;
?>