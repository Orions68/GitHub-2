<?php
include "includes/conn.php";
$title = "Detección de Intrusión";
include "includes/header.php";
include "includes/modal_index.html";

if (isset($_POST["data"]))
{
    $data = $_POST["data"];
    $mac = explode("; ", $data);
    if (strpos($mac[0], ":") == 2)
    {
        intercalate($conn, $mac[0]);
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
        intercalate($conn, $mac[0]);
    }
}

function intercalate($conn, $mac)
{
    $ma_s = substr($mac, 0, 13);
    $ma_m = substr($mac, 0, 10);
    $ma_l = substr($mac, 0, 8);
    $ok = search($conn, $ma_s, $mac);
    if (!$ok)
    {
        $ok = search($conn, $ma_m, $mac);
        if (!$ok)
        {
            $ok = search($conn, $ma_l, $mac);
            if (!$ok)
            {
                echo "<script>toast(2, 'CUIDADO:', 'La MAC Detectada no es Valida, puede tratarse de una MAC Virtual o Randomizada, Android, IOS o Virtual.');</script>";
                date_default_timezone_set('Europe/London');
                $date = date('Y/m/d H:i:s A', time());
                $sql = "INSERT INTO intruder VALUES(:oui, :mac, :mark, :private, :type, :up_date, :date, :attacks);";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':oui' => $oui, ':mac' => $mac, ':mark' => "Android, IOS, Virtual", ':private' => 1, ':type' => "MA_L", ':up_date' => "1970-01-01", ':date' => $date, ':attacks' => 1]);
            }
        }
    }
}

function search($conn, $oui, $mac)
{
    $sql = "SELECT * FROM mac WHERE macPrefix='$oui'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($stmt->rowCount() > 0)
    {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $oui = $row->macPrefix;
        $sql = "SELECT oui FROM intruder WHERE oui='$oui';";
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
            $sql = "INSERT INTO intruder VALUES(:oui, :mac, :mark, :private, :type, :up_date, :date, :attacks);";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':oui' => $row->macPrefix, ':mac' => $mac, ':mark' => $row->vendorName, ':private' => $row->private, ':type' => $row->blockType, ':up_date' => $row->lastUpdate, ':date' => $date, ':attacks' => 1]);
        }
        return true;
    }
    else
    {
        return false;
    }
}
?>