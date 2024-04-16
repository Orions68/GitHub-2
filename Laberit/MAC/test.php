<?php
// exec('nmap 192.168.0.87 > data2.txt');

// $sql = "CALL SP_Test(null, 'César', 'Matelat', null, '622222222', '2@2.com', '1')";
// $stmt = $conn->prepare($sql);
// $stmt->execute();

// echo "<script>alert('Datos Agregados Correctamente.');</script>";

exec ('nslookup 192.168.0.221 > device.txt');

$mac = null;
$filename = "device.txt"; // Asigna a $filename el valor data.txt, el nombre del fichero con los datos.
$file = fopen($filename, "r"); // Abre pata lectura el fichero data.txt.
if ($file) // Si se leyo.
{
    $port_index = 0; // Índice para los Puertos.
    while (!feof($file)) // Mientras lea del fichero.
    {
        $string = fgets($file); // Asigna a $string la línea de texto leida desde el fichero.
        if (str_starts_with($string, "Nombre:")) // Si la cadena obtenida en $string empieza por la frase 'Nmap scan'.
        {
            $device = $string; // Asigna la cadena a la variable $device, la cadena contiene el nombre del dispositivo.
        }
    }
}

$result = explode("  ", $device);

echo $result[1];
?>