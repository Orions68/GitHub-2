<?php
include "includes/conn.php";
$title = "Verificador de Direcciones MAC Intrusas.";
include "includes/header.php";
include "includes/modal_index.html";
include "includes/nav_index.html";
?>
<section class="container-fluid pt-3">
    <div class="row" id="pc">
        <div class="col-md-1" id="mobile"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br><br><br>
                    <h1>Verificador de MACS</h1>
                    <fieldset>
                        <legend>Por Favor Ingresa la MAC Sospechosa</legend>
                        <form action="review.php" method="post">
                        <label><input type="text" name="data" required> MAC Address</label>
                        <br><br>
                        <input type="submit" value="Verifica">
                        </form>
                    </fieldset>
                </div>
                <div id="view2">
                    <br><br><br><br>
                    <?php
                    $sql = "SELECT * FROM intruder ORDER BY date DESC, attacks DESC;";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0)
                    {
                        echo "<table><tr><th>OUI</th><th>Dirección</th><th>Fabricante</th><th>Privada</th><th>Tipo</th><th>Actualizada</th><th>Ataques</th><th>Fecha</th></tr><tr>";
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                        {
                            $date = date("Y-m-d", strtotime($row->date));
                                echo "<td id='col' onclick='givemeData(\"$row->oui\")'>$row->oui</td>
                                <td>$row->mac</td>
                                <td>$row->mark</td>
                                <td>$row->private</td>
                                <td>$row->type</td>
                                <td>$row->up_date</td>";
                            if ($date == date("Y-m-d"))
                            {
                                echo "<td class='red'>$row->attacks</td>
                                <td>$row->date</td></tr>";
                            }
                            else
                            {
                                echo "<td>$row->attacks</td>
                                <td>$row->date</td></tr>";
                            }
                        }
                        echo "</table>";
                    }
                    else
                    {
                        echo "<h1>Aun sin Datos.</h1>";
                    }
                    ?>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>