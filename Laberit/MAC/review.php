<?php
include "includes/conn.php";
$title = "Detección de Intrusión";
include "includes/header.php";
include "includes/modal_index.html";

if (isset($_POST["ip"]))
{
    $ip = $_POST['ip'];
    exec('rustscan -a ' . $ip . ' > data.txt', $result);

    $filename = "data.txt";
    $file = fopen($filename, "r");
    if ($file)
    {
        $mac_ok = true;
        $port_index = 0;
        $mac_index = 0;
        while (!feof($file))
        {
            $string = fgets($file);
            if (str_starts_with($string, "Open"))
            {
                $ports[$port_index] = $string;
                $port_index++;
            }
            else
            {
                if (str_starts_with($string, "MAC Address:"))
                {
                    $macs[$mac_index] = $string;
                    $mac_index++;
                }
                else
                {
                    $string = "La IP está Asignada a una MAC Virtual o Randomizada<br>Por Favor Escribe la MAC en el Formulario y Haz Click en el Botón Almacena la MAC.";
                    $mac_ok = false;
                }
            }
        }   
        fclose($file);

        if ($mac_ok)
        {
            for ($i = 0; $i < count($ports); $i++)
            {
                echo $ports[$i] . "<br>";
                $port_result = explode(" ", $ports[$i]);
            }

            for ($i = 0; $i < count($port_result); $i++)
            {
                echo $port_result[$i] . "<br>";
            }

            for ($i = 0; $i < count($macs); $i++)
            {
                echo $macs[$i] . "<br>";
                $mac_result = explode(" ", $macs[$i]);
            }

            for ($i = 0; $i < count($mac_result); $i++)
            {
                echo $port_result[$i] . "<br>";
            }
        }

        echo '<fieldset>
                <legend>Por Favor Ingresa la MAC Sospechosa</legend>
                <form action="review.php" method="post">
                <label><input type="text" name="data" value="' . $mac_result[2] . '" required> MAC Address</label>
                <br><br>
                        <input type="submit" value="Verifica">
                        </form>
                    </fieldset>';
    }
}

if (isset($_POST["data"]))
{
    $data = $_POST["data"];
    $ip = $_POST["ip"];
    $mac = explode("; ", $data);
    if (strpos($mac[0], ":") == 2)
    {
        intercalate($conn, $mac[0], $ip);
    }
    else
    {
        if (strpos($mac[0], "-") == 2)
        {
            echo $mac[0];
            $mac[0] = str_replace("-", ":", $mac[0]);
            echo $mac[0];
        }
        else
        {
            for ($i = 2; $i < strlen($mac[0]); $i+=3)
            {
                $mac[0] = substr_replace($mac[0], ":", $i, 0);
            }
        }
        intercalate($conn, $mac[0], $ip);
    }
}

function intercalate($conn, $mac, $ip)
{
    $ma_s = substr($mac, 0, 13);
    $ma_m = substr($mac, 0, 10);
    $ma_l = substr($mac, 0, 8);
    $ok = search($conn, $ma_s, $mac, $ip);
    if (!$ok)
    {
        $ok = search($conn, $ma_m, $mac, $ip);
        if (!$ok)
        {
            $ok = search($conn, $ma_l, $mac, $ip);
            if (!$ok)
            {
                echo "<script>toast(2, 'CUIDADO:', 'La MAC Detectada no es Valida, puede tratarse de una MAC Virtual o Randomizada, Android, IOS o Virtual.');</script>";
                date_default_timezone_set('Europe/London');
                $date = date('Y/m/d H:i:s A', time());
                $sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :private, :type, :up_date, :date, :attacks);";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':oui' => $ma_l, ':mac' => $mac, ':ip' => $ip, ':mark' => "Android, IOS, Virtual", ':private' => 1, ':type' => "MA_L", ':up_date' => "1970-01-01", ':date' => $date, ':attacks' => 1]);
            }
        }
    }
}

function search($conn, $oui, $mac, $ip)
{
    $sql = "SELECT * FROM mac WHERE macPrefix='$oui'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $oui = $row->macPrefix;
        $sql = "SELECT oui FROM intruder WHERE oui='$oui' AND ip='$ip';";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0)
        {
            $sql = "UPDATE intruder SET attacks = attacks + 1, date = NOW() WHERE oui='$oui';";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            echo "<script>toast(1, 'ALERTA:', 'Se Ha Detectado un Ataque de una MAC ya Registrada.<br>Tomen las Precauciones Necesarias.');</script>";
        }
        else
        {
            $result = $row->macPrefix . " - " . $row->vendorName . " - " . $row->private . " - " . $row->blockType . " - " . $row->lastUpdate;
            echo "<script>toast(0, 'Resultado:', 'Se ha Encontrado la MAC en la Base de Datos.<br>Estos son los datos de la MAC:<br>$result<br><br>Se Han Agregado los Datos a la Base de Datos.');</script>";
            date_default_timezone_set('Europe/London');
            $date = date('Y/m/d H:i:s A', time());
            $sql = "INSERT INTO intruder VALUES(:oui, :mac, :ip, :mark, :private, :type, :up_date, :date, :attacks);";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':oui' => $row->macPrefix, ':mac' => $mac, ':ip' => $ip, ':mark' => $row->vendorName, ':private' => $row->private, ':type' => $row->blockType, ':up_date' => $row->lastUpdate, ':date' => $date, ':attacks' => 1]);
        }
        return true;
    }
    else
    {
        return false;
    }
}
?>