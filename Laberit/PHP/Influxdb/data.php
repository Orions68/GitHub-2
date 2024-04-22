<?php
require "vendor/autoload.php";

include "includes/conn.php";

use InfluxDB2\Model\WritePrecision;

// use InfluxDB2\Point; // Se Usa para el Segundo caso de Inserción de Datos en la Base de Datos.

if (isset($_POST["ip"]))
{
    $ip = $_POST["ip"];

    exec('nmap ' . $ip . ' > data.txt'); // Ejecuta la aplicación nmap pasándole la IP y redirecciona la salida al fichero data.txt.
    $mac = loadFile($ip); // Asgina a $mac el Resultado que Devuelve la Llamada a la Función loadFile Pasándole la IP.
    if ($mac != null)
    {
        $mac_result = explode(";", $mac); // Se Explota la Cadena $mac en la Variable Array $mac_result y en la Posición 0 se Obtiene la MAC, en la 1 el nombre del dispositivo y en la 2 los puertos abiertos si lo hay.
    }
    else // Si $mac es NULL, caso casi improbable.
    {
        echo "<script>toast(2, 'ERROR:', 'No se Han Podido Obtener los Datos Verifica que el Disco no esté Lleno y si Tienes Permisos Para Escribir en la Carpeta del Proyecto.');</script>";
    }

    $writeApi = $client->createWriteApi();
    $data = "intruder,nombre=attack ip=\"$ip\",mac=\"$mac_result[0]\"";
    $writeApi->write($data, WritePrecision::S, $bucket, $org);
}

function loadFile($ip) // Carga el Fichero data.txt en Memoria y Obtiene los Datos.
{
    $mac = null;
    $filename = "data.txt"; // Asigna a $filename el valor data.txt, el nombre del fichero con los datos.
    $file = fopen($filename, "r"); // Abre pata lectura el fichero data.txt.
    if ($file) // Si se leyo.
    {
        $port_index = 0; // Índice para los Puertos.
        while (!feof($file)) // Mientras lea del fichero.
        {
            $string = fgets($file); // Asigna a $string la línea de texto leida desde el fichero.
            if (str_starts_with($string, "Nmap scan")) // Si la cadena obtenida en $string empieza por la frase 'Nmap scan'.
            {
                $device = $string; // Asigna la cadena a la variable $device, la cadena contiene el nombre del dispositivo.
            }
            else if (str_starts_with($string, "PORT")) // Si la Línea contiene la palabra PORT, es que hay al menos un puerto abierto.
            {
                $port = true; // Variable booleana $port, para comprobar cuando no hay más puertos abiertos.
                $ports[$port_index] = fgets($file); // Se asigna a $ports en el índice $port_index la lectura de la siguiente fila del Fichero data.txt, contiene un puerto abierto.
                while ($port) // Mientras $port sea true
                {
                    $port_index++; // Incrementa el Índice $port_index.
                    $ports[$port_index] = fgets($file); // Se asigna a $ports en el índice $port_index la lectura de la siguiente fila del Fichero data.txt.
                    if(str_starts_with($ports[$port_index], "MAC Address:")) // Si el contenido del Array $ports en el índice $port_index empieza por la frase 'MAC Address:'.
                    {
                        $port = false; // Ya no hay más puertos abiertos, pongo $port a false, al volver al while como $port es false sale del bucle.
                    }
                }
                $mac = explode(" ", $ports[$port_index]); // Asigna el contenido de $ports en el índice $port_index a la variable Array $mac explotándola por el espacio, obteniendo en la posición 2 del array la dirección MAC.
            }
            else if (str_starts_with($string, "MAC Address:")) // Si la string Contiene la Frase MAC Address:
            {
                $mac = explode(" ", $string); // Asigna $string a la Variable Array $mac explotándola por el espacio, obteniendo en la posición 2 del array la dirección MAC.
            }
        }   
        fclose($file); // Cierra el Fichero.
        echo "<h3>$ip</h3>";
        $port_number = ""; // Asigna un valor vacio a la variable $port_number.
        $device_name = explode(" ", $device); // Explota la cadena $device en la variable Array $device_name y obtiene en la posición 4 del array el nombre del dispositivo.
        for ($i = 0; $i < $port_index; $i++) // Bucle al tamaño del Array $ports.
        {
            $port_number .= $ports[$i]; // Concatena en la variable $port_number los puertos en el Array $ports.
        }
        $mac[2] .= ";" . $device_name[4] . ";" . $port_number; // Concatena a la posición 2 del Array $mac, que contiene la dirección MAC, el contenido del Array $device_name en la posición 4 que es el nombre del dispositivo y la string $port_number que contiene los puertos abiertos en el dispositivo.
    }
    return $mac[2]; // Retorna la posición 2 del Array $mac.
}

/* Para Escribir en InfluxDB usando la clase Point, es necesario incluir la ruta con USE de Composer. */
// $point = Point::measurement('mem')
//   ->addTag('host', 'host1')
//   ->addField('used_percent', 23.43234543)
//   ->time(microtime(true));

// $writeApi->write($point, WritePrecision::S, $bucket, $org);

/* Para Escribir en InfluxDB usando un Array. */
// $dataArray = ['name' => 'cpu',
//   'tags' => ['host' => 'server_nl', 'region' => 'us'],
//   'fields' => ['internal' => 5, 'external' => 6],
//   'time' => microtime(true)];

// $writeApi->write($dataArray, WritePrecision::S, $bucket, $org);

$query = "from(bucket: \"MACDB\") |> range(start: -1h) |> filter(fn: (r) => r._measurement == \"intruder\")";
$tables = $client->createQueryApi()->query($query, $org);
$time = [];

$i = 0;
foreach ($tables as $table)
{
    foreach ($table->records as $record)
    {
        $time[$i] = $record->getTime();
        $measurement[$i] = $record->getMeasurement();
        $field[$i] = $record->getField();
        $value[$i] = $record->getValue();
        $i++;
    }
}

if (count($time) > 0)
{
    for ($i = 0; $i < count($time); $i++)
    {
        for ($j = 0; $j < count($time) / 2; $j++)
        {
            if ($j != $i)
            {
                if ($time[$j] == $time[$i])
                {
                    print "<pre>$time[$i] $measurement[$i]: tiene: $field[$i]=$value[$i] $field[$j] = $value[$j]</pre><br>";
                }
            }
        }
    }
}
else
{
    echo "<script>alert ('No Hay Datos de la Última Hora.');</script>";
}

$client->close();
?>