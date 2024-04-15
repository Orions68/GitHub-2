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
                        <legend>Por Favor Ingresa la IP</legend>
                        <form action="review.php" method="post">
                        <label><input type="text" name="ip" required> IP Address</label>
                        <br><br>
                        <input type="submit" onclick="wait();" value="Verifica">
                        </form>
                    </fieldset>
                </div>
                <div id="view2">
                    <br><br><br><br>
                    <?php
                    $i = 0; // Índice para los Array.
                    $ok = false; // Booleano para saber si hay datos.
                    $sql = "SELECT * FROM intruder ORDER BY date DESC, attacks DESC;"; // Busca Dispositivos sospechosos.
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) // Si hay resultados.
                    {
                        $ok = true; // $ok a true, mostrará la lista de MAC sospechosas.
                        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) // Mientras lea datos.
                        {
                            $oui[$i] = $row->oui; // Asigna al array $oui la columna oui.
                            $mac[$i] = $row->mac;
                            $mark[$i] = $row->mark;
                            $ip[$i] = $row->ip;
                            $device[$i] = $row->device;
                            $port[$i] = $row->open_ports;
                            $private[$i] = $row->private;
                            $type[$i] = $row->type;
                            $update[$i] = $row->up_date;
                            $attacks[$i] = $row->attacks;
                            $date[$i] = $row->date;
                            $i++; // Incrementa el índice.
                        }
                    }
                    else // Si no hay datos
                    {
                        echo "<script>toast(0, 'No se Ha Producido Ninguna Incidencia Aun:', 'Esperamos que Siga así por Mucho Tiempo<br>En Caso de Ataque, Verás Una Tabla con las Direcciones MAC y Datos de los Dispositivos que Itentan Vulnerar la Red.')</script>"; // Muestra el Mensaje que no hay Incidencias.
                    }
                    if ($ok) // Si $ok se puso a true.
                    {
                        echo "<script>var oui = [];
                            var mac = [];
                            var mark = [];
                            var ip = [];
                            var device = [];
                            var port = [];
                            var private = [];
                            var type = [];
                            var update = [];
                            var attacks = [];
                            var date = [];</script>"; // Crea todas las variables de los datos para javascript.
                        for ($i = 0; $i < count($oui); $i++) // Bucle a la cantidad de datos encontrados.
                        {
                            echo "<script>oui[" . $i . "] = '" . $oui[$i] . "';
                                mac[" . $i . "] = '" . $mac[$i] . "';
                                mark[" . $i . "] = '" . $mark[$i] . "';
                                ip[" . $i . "] = '" . $ip[$i] . "';
                                device[" . $i . "] = '" . $device[$i] . "';
                                port[" . $i . "] = '" . $port[$i] . "';
                                private[" . $i . "] = '" . $private[$i] . "';
                                type[" . $i . "] = '" . $type[$i] . "';
                                update[" . $i . "] = '" . $update[$i] . "';
                                attacks[" . $i . "] = '" . $attacks[$i] . "';
                                date[" . $i . "] = '" . $date[$i] . "';</script>"; // Asigna todos los datos de las MAC sospechosas a las Variables de javascript.
                        }
                    }
                ?>
                <!-- El div con ID table contendrá la tabla con todos los datos de los Dispositivos Sospechosos. El span con ID pages muestra el número de página, los botones Anteriores Resultados y Siguientes Resultados cambiaran a las páginas de resultados. Los resultados se muestran desde la página 1 y se paginan de a 8. -->
                <div id="table"></div>
                <br>
                <span id="pages"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                <button onclick="prev()" id="prev_btn" class="btn btn-danger" style="visibility: hidden;">Anteriores Resultados</button>&nbsp;&nbsp;&nbsp;&nbsp;
                <button onclick="next()" id="next_btn" class="btn btn-primary" style="visibility: hidden;">Siguientes Resultados</button><br>
                <script>change(1, 8);</script>
                <br><br><br><br>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>