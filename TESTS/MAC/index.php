<?php
require "vendor/autoload.php";
include "includes/conn.php";
$title = "Verificador de Direcciones MAC Intrusas.";
include "includes/header.php";
include "includes/modal.html";
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
                        <label><input type="text" name="mac" required> MAC Address</label>
                        <br><br>
                        <label><input type="text" name="local_port" required> Puerto Local</label>
                        <br><br>
                        <label><input type="text" name="remote_port" required> Puerto Remoto</label>
                        <br><br>
                        <label><input type="text" name="protocol" required> Protocolo de Conexión</label>
                        <br><br>
                        <label><input type="number" name="packet" required> Tamaño del Paquete</label>
                        <br><br>
                        <input type="submit" onclick="wait();" value="Verifica" class="btn btn-primary">
                        </form>
                    </fieldset>
                </div>
                <div id="view2">
                    <br><br><br><br>
                    <h3>Lista de datos de InfluxDB:</h3>
                    <br><br>
                    <?php
                    $query = "from(bucket: \"MACDB\") |> range(start: -6h) |> filter(fn: (r) => r._measurement == \"intruder\")"; // Consulta a Influx los datos, 6 horas antes.
                    $tables = $client->createQueryApi()->query($query, $org);
                    $time = [];
                    $records = [];
                    foreach ($tables as $table)
                    {
                        foreach ($table->records as $record)
                        {
                            $tag = ["ip" => $record->getRecordValue("ip"), "mac" => $record->getRecordValue("mac"), "l_port" => $record->getRecordValue("localPort"), "r_port" => $record->getRecordValue("remotePort"), "protocol" => $record->getRecordValue("protocol"), "oui" => $record->getRecordValue("oui")]; // En la Varible de tipo array $tag, pusimos todos los tags y sus valores.
                            $row = key_exists($record->getTime(), $records) ? $records[$record->getTime()] : []; // Este operador ternario asigna a $row el tag _time, la marca de tiempo que pone InfluxDB.
                            $records[$record->getTime()] = array_merge($row, $tag, [$record->getField() => $record->getValue()]); // Hacemos un array_merge con los datos de toda la tupla.
                        }
                    }

                    if (count($records) > 0) // Si hay Datos.
                    {
                        $i = 0; //Índeice de los Valores y las Tags.
                        $z = false; // Controla que las Tags se Almacenen Solo una Vez.
                        echo "<script>var array_key = [];
                                    var array_value = [];</script>";
                        foreach($records as $key) // Bucle para obtener las keys.
                        {
                            echo "<h5>"; // Formato del texto.
                            foreach ($key as $value) // Bucle para obtener los valores.
                            {
                                if (!$z)
                                {
                                    echo "<script>array_key[" . $i . "] = '" . key($key) . "';</script>"; // Alamcena la tag en el Array array_key.
                                }
                                echo "<script>array_value[" . $i . "] = '" . $value . "';</script>"; // Alamacena el valor en el Array array_value.
                                next($key); // Siguiente Clave.
                                $i++; // Incrementa el Índice.
                            }
                            $z = true;
                            echo "</h5>";
                        }
                    }
                    else
                    {
                        echo "<script>toast(0, 'Sin Datos Aun', 'No Hay Datos de la Última Hora.');</script>";
                    }
                    // for ($j = 0; $j < $i; $j++)
                    // {
                    //     echo "<script>console.log('Los Datos Son: ' + array_key[" . $j . "]);</script>";
                    //     echo "<script>console.log('Los Datos Son: ' + array_value[" . $j . "]);</script>";
                    // }
                    ?>
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