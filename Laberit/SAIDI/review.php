<?php
require "Influx/autoload.php"; // Incluye la API de InfluxDB
include "includes/conn.php"; // Incluya las conexiones con los sistemas gestores de base de datos.
$title = "Detección de Intrusión"; // Título  de la página.
include "includes/header.php"; // Incluye el header.php.
include "includes/modal_index.html"; // Incluye el Diálogo modal_index.html.
include "data.php";

use InfluxDB2\Model\WritePrecision; // Usa una clase de la API de InfluxDB para PHP.

if (isset($_POST["sended"])) // Recibe el Fichero CSV desde el script index.php por POST.
{
    $file = htmlspecialchars($_FILES["data"]["name"]);
    $tmp = htmlspecialchars($_FILES["data"]["tmp_name"]);

    if (!file_exists("Data")) // Si no existe el directorio Data
    {
        mkdir("Data", 0777, true); // Lo crea con  permisos totales.
    }
    $path = "Data/" . basename($file); // Asigno a la variable $path la ruta del fichero CSV.
    move_uploaded_file($tmp, $path); // Lo muevo del directorio temporal del servidor a la ruta creada anteriormente.

    $line = []; // Cada linea del fichero CSV.
    $data = []; // Contandrá todos los datos del CSV.
    $tmp = []; // Explota cada linea por el separador
    $mac = ""; // Por si en la muestra viene alguna MAC distinta a la detectada.
    $i = 0; // Índice de los arrays.
    $datos = fopen($path, "r") or die("Unable to open file!"); // Abre en la variable $datos el fichero para lectura.
    while(!feof($datos)) // Mientras no llegue al final de fichero.
    {
        $line[$i] = fgets($datos); // Asigna a $line en el Índice $i la primera línea del fichero.
        $line[$i] = trim($line[$i]); // Hace un trim de toda la línea.
        if ($i == 0) // Si es el primer Índice.
        {
            $data[0] = explode(";", $line[$i]); // Explota en $data en la posición 0 los datos en $line por el ;, en la primera posición en $data[0][0] queda la MAC.
            $data[0][0] = implode(':', str_split($data[0][0], 2)); // Intercala los : Cada 2 Caracteres en la MAC.
        }
        else // Si el Índice ya no es el primero.
        {
            $mac = explode(";", $line[$i]); // Explota la línea en $line en $mac port el ;, Obtiene la siguiente MAC.
            $mac[0] = implode(':', str_split($mac[0], 2)); // Intercala los : Cada 2 Caracteres en la MAC.
            if ($mac[0] == $data[0][0]) // La compara con la MAC anterior, si es la misma.
            {
                $tmp[0] = explode(";", $line[$i]); // explota en $tmp[0] por el ; los datos en $line en el Índice $i.
                for ($j = 1; $j < count($tmp[0]); $j++) // Hace un bucle a la cantidad de datos.
                    $data[0][$j] += $tmp[0][$j]; // Suma los valores en $tmp[0] a partir del Índice 1 a los datos en $data[0] también a partir del Índice 1.
            }
        }
        $i++; // incrementa el Índice.
    }
    fclose($datos); // Cierra el Fichero abierto para lectura.

    $oui = getDevice($conn, $data[0][0]); // Llama a la Función getDevice($conn, $mac), Pasándole la conexión con la base de datos y la MAC.

    if ($oui != null) // Si $oui no es null.
    {
        $sql = "SELECT vendorName FROM mac WHERE macPrefix='$oui'"; // Obtenemos la Marca del Dispositivo de la Base de Datos MariaDB.
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) // Si la Encuntra en la Base de Datos.
        {
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            $mark = $row->vendorName;
            $owner = preg_replace('/[^a-z0-9]/i', '_', $mark); // Se pasa por una expreción regular para elimar los espacios de la cadena que contiene la marca de la MAC.
            $private = false; // No es una MAC privada.
        }
    }
    else // Si es null.
    {
        $mark = "Android,_IOS,_Virtual"; // Es ua MAC randomizada.
        $oui = $data[0][0]; // Se asigna a $data[0][0] la MAC completa.
        $private = true; // Es una MAC privada.
    }
    $writeApi = $client->createWriteApi(); // Se asigna a la variable $writeApi la inserción de datos en InfluxDB.

    $save = 'aintrusa,mac=' . $data[0][0] . ',mark=' . $owner . ',oui=' . $oui . ' qtty=' . $data[0][1] . ',uni=' . $data[0][2] . ',multi=' . $data[0][3] . ',broad=' . $data[0][4] . ',arp=' . $data[0][5] . ',traffic=' . $data[0][6] . ',icmp=' . $data[0][7] . ',udp=' . $data[0][8] . ',tcp=' . $data[0][9] . ',resto=' . $data[0][10] . ',ipv6=' . $data[0][11] . ',arp46=' . $data[0][12] . ',badip=' . $data[0][13] . ',ssdp=' . $data[0][14] . ',icmp6=' . $data[0][15]; // $save contiene todos los datos a almacenar en InfluxDB. Los Tags en Influx no pueden tener espacios.

    $writeApi->write($save, WritePrecision::MS, $bucket, $org); // Lo escribe en InfluxDB con una precisión de milisegundos.
    $client->close(); // Cierra la conexión con la Base de Datos.

    echo "<script>toast(0, 'Datos Agregados', 'Se Han Agregado Datos a InfluxDB.');</script>"; // Muestra un Diálogo que se han alamcenado los datos.
}

function getDevice($conn, $mac) // Esta función busca en la Base de dtos MariaDB si la MAC que prooduce la incidencia está registrada por el IEEE.
{
    $oui = null;
    $ma_s = substr($mac, 0, 13); // Parte la Cadena $mac y Obtiene la OUI de una MAC Pequeña.
    $ma_m = substr($mac, 0, 10); // Parte la Cadena $mac y Obtiene la OUI de una MAC Mediana.
    $ma_l = substr($mac, 0, 8); // Parte la Cadena $mac y Obtiene la OUI de una MAC Grande.
    
    $sql = "SELECT * FROM mac WHERE macPrefix='$ma_s' UNION SELECT * FROM mac WHERE macPrefix='$ma_m' UNION SELECT * FROM mac WHERE macPrefix='$ma_l' LIMIT 1;"; // Query que obtiene la OUI.
    $stmt = $conn->prepare($sql); // Se prepara la Consulta.
    $stmt->execute(); // Se Ejecuta.
    if ($stmt->rowCount() > 0) // Si se Obtienen Resultados.
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $oui = $row->macPrefix; // La OUI de la MAC.
    }
    return $oui; // Retorna $oui, puede ser null o la OUI de la MAC.
}
?>