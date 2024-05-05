<?php
require "vendor/autoload.php";
include "includes/conn.php";
$title = "Detección de Intrusión";
include "includes/header.php";
include "includes/modal_index.html";

use InfluxDB2\Model\WritePrecision;

if (isset($_POST["ip"])) // Recibe la IP desde el script index.php por POST.
{
    $ip = $_POST['ip']; // Se la Asigna a $ip.
    $mac = $_POST["mac"];
    $local_port = $_POST["local_port"];
    $remote_port = $_POST["remote_port"];
    $protocol = $_POST["protocol"];
    $length = $_POST["packet"];

    $oui = get_device($conn, $mac); // Llama a la Función get_device($conn, $mac), Pasándole la conexión y la MAC.
    $sql = "SELECT vendorName FROM mac WHERE macPrefix='$oui'"; // Solo Recupero la Marca de Vendedor.
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0) // Si $oui no era null.
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $mark = $row->vendorName; // Obtengo en $mark la Marca del Fabricante del Dispositivo.
        $private = false; // La MAC no es Privada.
    }
    else // Si no Hay Datos es que $oui es null.
    {
        $mark = "Android, IOS, Virtual"; // La MAC es de un Dispositivo Android, IOS o Virtual Modificado a Mano.
        $oui = $mac; // Asigno a $oui a MAC Completa.
        $private = true; // Es una MAC Privada.
    }
    $writeApi = $client->createWriteApi(); // Se Prepara para Escribir en InfluxDB.
    $data = "intruder,ip=$ip,mac=$mac,protocol=$protocol,localPort=$local_port,remotePort=$remote_port,oui=$oui mark=\"$mark\",length=$length"; // Los Tags en Influx no pueden tener espacios.
    $writeApi->write($data, WritePrecision::S, $bucket, $org); // Escribe en la Base de Datos, Pasándole los Datos a Almacenar, la Presicion en Segundos, en este caso, El Nombre de la Base de Datos y la Organización.

    $client->close(); // Cierra la Conexión con la Base de Datos.

    echo "<script>toast(0, 'Datos Agregados', 'Se Han Agregado Datos a InfluxDB.');</script>"; // Aviso se Han Agregado los Datos.
}

function get_device($conn, $mac) // Obtiene la OUI de la MAC pasada como parámetro, si es pequeña se obtendrá primero la OUI pequeña de 13 y la Grande de 8 caracteres, con el filtro LIMIT 1 solo obtenemos la pequeña, si no es pequeña se obtiene la Mediana de 10 y la grande de 8 caracteres, con el filtro LIMIT 1 se obtiene solo la mediana si no es mediana, se obtiene solo la grande y so no está en la base de datos retorna null.
{
    $ma_s = substr($mac, 0, 13); // Parte la Cadena $mac y Obtiene la OUI de una MAC Pequeña.
    $ma_m = substr($mac, 0, 10); // Parte la Cadena $mac y Obtiene la OUI de una MAC Mediana.
    $ma_l = substr($mac, 0, 8); // Parte la Cadena $mac y Obtiene la OUI de una MAC Grande.

    date_default_timezone_set('Europe/London'); // Zona Horario Europa Londres.
    $date = date('Y/m/d H:i:s A', time()); // Formato de Fecha para MariaDB.
    
    $sql = "SELECT * FROM mac WHERE macPrefix='$ma_s' UNION SELECT * FROM mac WHERE macPrefix='$ma_m' UNION SELECT * FROM mac WHERE macPrefix='$ma_l' LIMIT 1;";
    $stmt = $conn->prepare($sql); // Se prepara la Consulta.
    $stmt->execute(); // Se Ejecuta.
    if ($stmt->rowCount() > 0) // Si se Obtienen Resultados.
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $oui = $row->macPrefix;
        return $oui; // Retorna la OUI Encontrada.
    }
    else
    {
        return null; // Si no Está en la Base de Datos Retorna null.
    }
}
?>