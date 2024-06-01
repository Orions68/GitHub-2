<?php
require "Influx/autoload.php";
include "includes/conn.php";
$title = "Verificador de Direcciones MAC Intrusas.";
include "includes/header.php";
include "includes/modal.html";
include "includes/nav_index.html";
include "data.php";
?>
<section class="container-fluid pt-3">
    <div class="row" id="pc">
        <div class="col-md-1" id="mobile" style="width: 2%;"></div>
            <div class="col-md-10">
                <div id="view1">
                    <!-- Formulario para cargar el fichero con las muestras (Formato CSV). -->
                    <h1>Captura de las Métricas de SAIDI en InfluxDB</h1>
                    <br><br>
                    <h3>Selecciona el Fichero de Datos con el Botón Examinar y Haz Click en el Botón Enviar para Almacenarlos en la Base de Datos</h3>
                    <br><br>
                    <form action="review.php" method="post" enctype="multipart/form-data">
                        <label><input id="file" type="file" name="data" required> Carga el Fichero CSV</label>
                        <input type="submit" name="sended" value="Enviar" class="btn btn-danger btn-lg">
                    </form>
                </div>
                <div id="view2">
                    <!-- Tabla con los datos de las muestras, paginada de a 8 resultados. -->
                    <h3>Lista de datos en InfluxDB:</h3>
                    <br><br>
                    <h4>Por Favor Selecciona Desde Cuando Quieres Ver las Métricas:</h4>
                    <br><br>
                    <form method="post">
                        <label><select name="time" required>
                            <option value="">Selecciona una Opción</option>
                            <option value="-1">Solo Hoy(1 Día)</option>
                            <option value="-7">Desde Una Semana Atrás(7 Días)</option>
                            <option value="-30">Desde el Mes Pasado(30 Días)</option>
                            <option value="-365">De Todo el Año(365 Días)</option> Cuantos Tiempo Atrás</label>
                            <input type="submit" value="Ese Tiempo" class="btn btn-secondary btn-lg separate">
                    </form>
                    <br><br>
                    <?php
                    if (isset($_POST["time"]))
                    {
                        $query = "from(bucket: \"$bucket\") |> range(start: " . $_POST['time'] . "d) |> filter(fn: (r) => r._measurement == \"aintrusa\")"; // Consulta a InfluxDB, hasta 10 días antes.
                        $tables = $client->createQueryApi()->query($query, $org); // Ejecuta la Consulta Asignado el Resutlado a la Variable $tables.
                        $records = []; // $records Contendrá todos los Resultados de la Tabla intruder de la Base de Datos MACDB.
                        $i = 0;
                        foreach ($tables as $table) // Obtiene cada Tabla de las Tablas de la Variable $tables(Solo Obtiene la Tabla intruder).
                        {
                            foreach ($table->records as $record) // De la Tabla intruder Obtiene cada Campo Almacenado en la Varaible $record.
                            {
                                $tag = ["mac" => $record->getRecordValue("mac"), "mark" => $record->getRecordValue("mark"), "oui" => $record->getRecordValue("oui"), "time" => $record->getTime()];
                                $row = key_exists($record->getTime(), $records) ? $records[$record->getTime()] : []; // Este operador ternario asigna a $row los datos en InfluxDB.
                                $records[$record->getTime()] = array_merge($row, $tag, [$record->getField() => $record->getValue()]); // Hacemos un array_merge con los datos de toda la Tupla y los Tags.
                            }
                        }

                        if (count($records) > 0) // Si hay Datos.
                        {
                            $data = [];
                            $time = array_column($records, 'time'); // Obtengo la KEY time del Array $records.

                            array_multisort($time, SORT_DESC, $records); // Ordena el Array $records por la Columna time, en Orden Descendiente.

                            $i = 0; // Índice de Todos los Datos de Todas las Tuplas.
                            $pos = 0;

                            foreach($records as $key) // Bucle para Obtener las Keys.
                            {
                                $i = 0;
                                $data[$pos] = [];
                                foreach ($key as $value) // Bucle para Obtener los Valores de cada Clave.
                                {
                                    $data[$pos][$i] = $value;
                                    next($key); // Siguiente Clave.
                                    $i++; // Siguiente Índice.
                                }
                                $pos++;
                            }

                            $obj = [];
                            for ($i = 0; $i < count($data); $i++)
                            {
                                $obj[$i] = new Data($data[$i][3] . "\n" . $data[$i][0], $data[$i][12], $data[$i][18], $data[$i][11], $data[$i][7], $data[$i][4], $data[$i][5], $data[$i][8], $data[$i][17], $data[$i][15], $data[$i][16], $data[$i][10], $data[$i][13], $data[$i][6], $data[$i][14], $data[$i][9]);
                            }

                            echo "<script>let array_data = " . json_encode($obj) . ";
                                            let array_table = " . json_encode($data) . ";
                                        makeData(array_data);
                                        makeTable(array_table);</script>";
                        }
                    }
                    ?>
                    <div id="table"></div>
                    <br>
                    <span id="pages"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="prev()" id="prev_btn" class="btn btn-secondary" style="visibility: hidden;">Anteriores Resultados</button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="next()" id="next_btn" class="btn btn-danger" style="visibility: hidden;">Siguientes Resultados</button><br>
                    <script>change(1, 8);</script>
                    <br><br><br><br>
                </div>
                <div id="view3">
                    <!-- Gráfica de AMCharts V5. -->
                    <h3>Gráfica de Nivel de Ataque de las Conexiones Intrusas.</h3>
                    <div id="chartdiv"></div>
                    <div id="buttons">
                        <button id="previ" onclick="reset(false)" style="visibility: hidden;" class="btn btn-danger btn-lg">Anterior</button>&#9;&nbsp;&#9;&nbsp;&#9;<button id="next" onclick="reset(true)" class="btn btn-danger btn-lg">Siguiente</button>&#9;&nbsp;&#9;&nbsp;&#9;<label id="stackit"><input id="stack" type="checkbox" onchange="reset(null)"> Muestra los Datos Apilados</label>
                    </div>
                    <script>show()</script>

                </div>
                <div id="view4">
                    <!-- Formulario para Exportar los datos a CSV o XLSX. -->
                    <?php
                        if (isset($data))
                        {
                            echo '<h3>Exportando los Datos a Excel o CSV</h3>
                            <br>
                            <div class="col-md-5">
                            <h4>Haz Click en Ver Informe.</h4>
                            <br>
                            <form action="export.php" method="post" target="_blank">
                                <input type="hidden" name="data" value="' . htmlspecialchars(json_encode($data)) . '">
                                <input type="submit" name="index" value="Ver Informe" class="btn btn-danger btn-lg">
                            </form>';
                        }
                    ?>
                    <br><br>
                    </div>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>



<?php
include "includes/footer.html";
?>