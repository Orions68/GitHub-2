<?php
include "includes/conn.php";
$title = "Detección de Intrusión";
include "includes/header.php";
include "includes/modal_index.html";

if (isset($_POST["ip"])) // Recibe la IP desde el script index.php por POST.
{
    $ip = $_POST['ip']; // Se la Asigna a $ip.

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

    echo '<fieldset>
            <legend>Datos de la MAC Sospechosa y su IP</legend>
            <form action="review.php" method="post">
            <label><input type="text" name="mac" value="' . $mac_result[0] . '" required> MAC Address</label>
            <br><br>
            <label><input type="text" name="device" value="' . $mac_result[1] . '" required> Device Name</label>
            <br><br>
            <label><input type="text" name="port" value="' . $mac_result[2] . '"> Open Ports</label>
            <br><br>
            <label><input type="text" name="ip2" value="' . $ip . '" required> IP Address</label>
            <br><br>
            <input type="submit" value="Almacena la MAC y la IP">
            </form>
        </fieldset>'; // Muestra el Formulario HTML para enviar los datos a la Base de Datos: la Mac, en Nombre del Dispositivo, los Puertos Abiertos y la IP.
}

if (isset($_POST["mac"])) // Entra Aquí Después de Hacer Click en el Botón Almacena la MAC y la IP de Este Mismo Script.
{
    $mac = $_POST["mac"]; // Asigna a $mac la MAC recibida por POST.
    $ip = $_POST["ip2"]; // Asigna a $ip la IP recibida por POST.
    $device = $_POST["device"]; // Asigna a $device en nombre de Dispositivo recibido por POST.
    $port = $_POST["port"]; // Asigna a $port los Puertos recibidos por POST.

    getOui($conn, $mac, $ip, $device, $port); // Llama a la Función getOui(), Pasándole la conexión con la base de datos, la MAC y la IP, el Dispositivo y los Puertos.
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
        $port_number = ""; // Asigna un valor vacio a la variable $port_number.
        $device_name = explode(" ", $device); // Explota la cadena $device en la variable Array $device_name y obtiene en la posición 4 del array el nombre del dispositivo.
        for ($i = 0; $i < $port_index; $i++) // Bucle al tamaño del Array $ports.
        {
            $port_number .= $ports[$i]; // Concatena en la variable $port_number los puertos en el Array $ports.
        }
        $mac[2] .= ";" . $device_name[4] . ";" . $port_number; // Concatena a la posición 2 del Array $mac, que contiene la dirección MAC, el contenido del Array $device_name en la posición 4 que es el nombre del dispositivo y la string $port_number que contiene los puertos abiertos en el dispositivo.
    }
    return $mac[2]; // Retorna la posición 2 variable Array $mac.
}

function getOui($conn, $mac, $ip, $device, $port) // Verfifica si la MAC es Pequeña, Mediana o Grande.
{
    if ($port == "") // Si la variable que se pasa por parámetro $port está vacia, el dipositivo no tiene ningún peurto abierto.
    {
        $port = null; // Se pone la variable $port a null.
    }
    $ma_s = substr($mac, 0, 13); // Parte la Cadena $mac y Obtiene la OUI de una MAC Pequeña.
    $ma_m = substr($mac, 0, 10); // Parte la Cadena $mac y Obtiene la OUI de una MAC Mediana.
    $ma_l = substr($mac, 0, 8); // Parte la Cadena $mac y Obtiene la OUI de una MAC Grande.
    $ok = search($conn, $ma_s, $mac, $ip, $device, $port); // Verifica si es una MAC Pequeña.
    if (!$ok) // Si Devuelve false.
    {
        $ok = search($conn, $ma_m, $mac, $ip, $device, $port); // Verifica si es una MAC Mediana.
        if (!$ok) // Si Devuelve false.
        {
            $ok = search($conn, $ma_l, $mac, $ip, $device, $port); // Verifica si es una MAC Grande.
            if (!$ok) // Si se Devuelve false, Igual se Almacena la MAC, la IP y el nombre del dispositivo en la Base de Datos, Pero se Avisa que Puede ser una MAC Aleatoria o Virtual.
            {
                echo "<script>toast(2, 'CUIDADO:', 'La MAC Detectada no es Valida, puede tratarse de una MAC Virtual o Randomizada, Android, IOS o Virtual.');</script>";
                date_default_timezone_set('Europe/London');
                $date = date('Y/m/d H:i:s A', time());
                $sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :device, :open_ports, :private, :type, :up_date, :date, :attacks);";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':oui' => $ma_l, ':mac' => $mac, ':ip' => $ip, ':mark' => "Android, IOS, Virtual", ':device' => $device, ':open_ports' => $port, ':private' => 1, ':type' => "MA_L", ':up_date' => "1970-01-01", ':date' => $date, ':attacks' => 1]);
            }
        }
    }
}

function search($conn, $oui, $mac, $ip, $device, $port) // Función para comprobar si la Dirección MAC pertenece a una MAC Registrada por el IEEE.
{
    date_default_timezone_set('Europe/London'); // Zona Horario Europa Londres.
    $date = date('Y/m/d H:i:s A', time()); // Formato de Fecha para MariaDB.
    if ($port == "") // Si la variable que se pasa por parámetro $port está vacia, el dipositivo no tiene ningún peurto abierto.
    {
        $port = null; // Se pone la variable $port a null.
    }
    // $sql = "SELECT * FROM mac WHERE macPrefix='$oui'"; // Se obtienen todos los datos de las OUI (Organizationally Unique Identifier) que coicidan con la OUI pasada como parámetro.
    $sql = "CALL SP_Get_All('$oui')"; // Reemplazo el Query SQL por un Storage Procedure.
    $stmt = $conn->prepare($sql); // Se prepara la Consulta.
    $stmt->execute(); // Se Ejecuta.
    if ($stmt->rowCount() > 0) // Si se Obtienen Resultados.
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ); // Se asigna la tupla a la variable $row.
        // $sql = "SELECT oui FROM intruder WHERE mac='$mac' AND ip='$ip';"; // Sentencia SQL para Verificar si la MAC y la IP del dispositivo ya habían intentado un ataque.
        $sql = "CALL SP_Reincident('$mac', '$ip')"; // Reemplazo el Query SQL por un Storage Procedure.
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) // Si ya había un ataque de esa MAC con esa IP.
        {
            $sql = "UPDATE intruder SET attacks = attacks + 1, date = NOW() WHERE oui='$oui';"; // Se actualiza la tabla de Ataques incrementando el campo attacks y Actualizando la fecha y hora.
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            echo "<script>toast(1, 'ALERTA:', 'Se Ha Detectado un Ataque de una MAC con una IP ya Registradas.<br>Tomen las Precauciones Necesarias.');</script>"; // Se muestra una alerta que se repite un ataque de un dispositvo que ya había atacado en el pasado.
        }
        else // Si no hay coicidencia.
        {
            // $sql = "SELECT oui FROM intruder WHERE ip='$ip';"; // Se verifica si la IP ya está en la Base de Datos de Ataques.
            $sql = "CALL SP_Same_IP('$ip')"; // Reemplazo el Query SQL por un Storage Procedure.
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            if ($stmt->rowCount() > 0) // Si hay resultados.
            {
                // $sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :device, :open_ports, :private, :type, :up_date, :date, :attacks);"; // Se inserta como un nuevo dispositivo.
                // $sql = "CALL SP_Insert_All('$row->macPrefix', '$mac', '$ip', '$row->vedorName', '$device', '$port', '$row->private', '$row->blockType', '$row->lastUpdate', '$date', 1)";
                // $stmt = $conn->prepare($sql);
                // // $stmt->execute([':oui' => $row->macPrefix, ':mac' => $mac, ':ip' => $ip, ':mark' => $row->vendorName, ':device' => $device, ':open_ports' => $port, ':private' => $row->private, ':type' => $row->blockType, ':up_date' => $row->lastUpdate, ':date' => $date, ':attacks' => 1]);
                // $stmt->execute();
                echo "<script>toast(1, 'ALERTA:', 'Se Ha Detectado un Ataque de una IP ya Registrada pero asignada a otra MAC.<br>Tomen las Precauciones Necesarias.');</script>"; // Se avisa que el ataque se produjo de una IP que ya estaba registrada.
            }
            else // Si No.
            {
                $result = $row->macPrefix . " - " . $row->vendorName . " - " . $row->private . " - " . $row->blockType . " - " . $row->lastUpdate;
                echo "<script>toast(0, 'Resultado:', 'Se ha Encontrado la MAC en la Base de Datos.<br>Estos son los datos de la MAC:<br>$result<br><br>Se Han Agregado los Datos a la Base de Datos.');</script>"; // Se Muestra que la MAC está en la Base de Datos de Fabricantes conocidos y se agregan todos los datos.
                // $sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :device, :open_ports, :private, :type, :up_date, :date, :attacks);";
                // $stmt = $conn->prepare($sql);
                // $stmt->execute([':oui' => $row->macPrefix, ':mac' => $mac, ':ip' => $ip, ':mark' => $row->vendorName, ':device' => $device, ':open_ports' => $port, ':private' => $row->private, ':type' => $row->blockType, ':up_date' => $row->lastUpdate, ':date' => $date, ':attacks' => 1]);
            }
            $sql = "CALL SP_Insert_All('$row->macPrefix', '$mac', '$ip', '$row->vendorName', '$device', '$port', '$row->private', '$row->blockType', '$row->lastUpdate', '$date', 1)";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        }
        return true; // Devuelve true si almecenó en la Base de Datos los datos Buscados.
    }
    else // Si No.
    {
        return false; // Devuelve False, se ha detectado una MAC Aleatoria o Virtual.
    }
}
?>