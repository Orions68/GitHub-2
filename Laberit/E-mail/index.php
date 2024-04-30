<?php
include "includes/conn.php";
$title = "Monitor de E-mails.";
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
                    <?php
                        echo "<h1>Verifica los Mensajes IMAP del Servidor de Correos</h1><br>";
                        $yes_email = [];
                        $index_ok = 0;
                        $no_email = [];

                        $mailbox = imap_open("{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX", "matelat@gmail.com", $_ENV["Gmail-matelat"]);

                        $check = imap_check($mailbox); // Función que Verifica los Correos en la Bandeja de Entrada IMAP, Asigna el resultado a la variable $check.
                        $today = ""; // El día de Hoy.
                        $date = $check->Date; // Fecha de la Consulta.
                        $qtty = $check->Nmsgs; // Cantidad Total de Mansajes en la Bandeja de Entrada.
                        $temp = explode(" ", $date); // Exploto la fecha en el Array $temp.
                        for ($i = 0; $i < 4; $i++) // Bucle hasta 4 para obtener los 4 primeros datos del Array $temp, Nombre del Día, Día, Mes, Año.
                        {
                            $today .= $temp[$i]; // Concateno la Fecha en la Variable $today.
                        }

                        while ($qtty > 0) // Mientras la Cantiad de Mensajes Sea Mayor que 0.
                        {
                            $mail_today = ""; // Fechade los Mensajes.
                            $header = imap_headerinfo($mailbox, $qtty); // Obtiene la Información de Todos los Mensajes.
                            $address[$index_ok] = $header->from[0]->mailbox . "@" . $header->from[0]->host; // Asigna a $address la Dirección de Correo Eletrónico Recibida.
                            $mail_date = $header->Date; // Fecha de los Mensajes.
                            $mail_temp = explode(" ", $mail_date); // Exploto la fecha en el Array $mail_temp.
                            for ($i = 0; $i < 4; $i++) // Bucle hasta 4 para obtener los 4 primeros datos del Array $temp, Nombre del Día, Día, Mes, Año.
                            {
                                $mail_today .= $mail_temp[$i]; // Concateno la Fecha en la Variable $mail_today.
                            }
                            if ($today == $mail_today) // Si los Mensajes son de Hoy, $today = a los Mensajes del Día.
                            {
                                $sql = "SELECT email FROM email WHERE email='" . $address[$index_ok] . "';"; // Selecciono las Direcciones de la Base de Datos de E-mails.
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                if ($stmt->rowCount() > 0)
                                {
                                    $row = $stmt->fetch(PDO::FETCH_OBJ); // Asigno el Resultado a $row.
                                    $yes_email[$index_ok] = $row->email; // Pongo en el Array $yes_email las Direcciones de los Correos Que Llegaron, que Están en la Base de Datos.
                                    $subject[$index_ok] = $header->subject;
                                    $index_ok++; // Incremento el Índice del Array de E-mails a Monitorear.
                                }
                            }
                            else // Si el Mensaje es antiguo.
                            {
                                $qtty = 1; // Igualo $qtty a 1 Para Salir del Bucle.
                            }
                            $qtty--; // Decremento la Cantidad de Mensajes.
                        }
                        imap_close($mailbox); // Función que Cierra la Conexión.
                        $sql = "SELECT COUNT(email) FROM email;"; // Cuento la Cantidad de E-mails en la Base de Datos.
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $number = $stmt->fetchColumn(); // Obtengo la Cantidad de Direcciones.
                        if (count($yes_email) < $number)
                        {
                            $i = 0; // Índice de las Direcciones de E-mail que Llegaron.
                            $j = 0; // Índice de las Direcciones que no Llegaron.
                            $sql = "SELECT * FROM email"; // Busco Todos los E-mails.
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) // Mientras Haya Resultados.
                            {
                                while ($i < count($yes_email) && $yes_email[$i] != $row->email) // Mientras no Llegue al Final del Array de Correos que Llegaron y los E-mail que Llegaron no Esten en la Base de Datos.
                                {
                                    $i++; // Incrementa $i.
                                }
                                if ($i == count($yes_email)) // Si al Salir del Bucle $i es Igual a la cantidad de E-mails que Llegaron, Se Encontró una Dirección que no Ha Llegado.
                                {
                                    $no_email[$j] = $row->email; // Se Agrega a la Lista (Array $no_email) de E-mails que no Han Llegado.
                                    $j++; // Incremento el Índice del Array de Direcciones que no Han Llegado.
                                    $i = 0; // Pongo el Índice de las Direcciones que Han Llegado a 0, Para Verificar la Próxima Dirección.
                                }
                                else // Si la Dirección que ha Llegado está en la Base de Datos.
                                {
                                    $i = 0; // Pongo el Índice de las Direcciones que Han Llegado a 0, Para Verificar la Próxima Dirección.
                                }
                            }
                        }
                        if (count($no_email) > 0) // Si la Cantidad de Direcciones en el Array de E-mail NO Recibidos es Mayor que 0.
                        {
                            echo "<h3>No Ha Llegado Nada de Estas Direcciónes:</h3>"; // No ha Llegado el Mensaje de Esas Direcciones.
                            echo "<script>var no_email = [];</script>";
                            for ($i = 0; $i < count($no_email); $i++)
                            {
                                echo "<script>no_email[" . $i . "] = '" . $no_email[$i] . "';</script>";
                            }
                        }
                    ?>
                    <div id="table1"></div>
                    <br>
                    <span id="pages1"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="prev1()" id="prev_btn1" class="btn btn-danger" style="visibility: hidden;">Anteriores Resultados</button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="next1()" id="next_btn1" class="btn btn-primary" style="visibility: hidden;">Siguientes Resultados</button><br>
                    <script>change1(1, 8);</script>
                    <br><br><br><br>
                </div>
                <div id="view2">
                <br><br><br><br>
                <h3>Mensajes Recibidos:</h3>
                <?php
                    echo "<script>var address = [];
                        var subject = [];</script>";
                        for ($i = 0; $i < count($yes_email); $i++)
                        {
                            echo "<script>address[" . $i . "] = '" . $address[$i] . "';
                            subject[" . $i . "] = '" . $subject[$i] . "';</script>";
                        }
                    ?>
                    <div id="table2"></div>
                    <br>
                    <span id="pages2"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="prev2()" id="prev_btn2" class="btn btn-danger" style="visibility: hidden;">Anteriores Resultados</button>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button onclick="next2()" id="next_btn2" class="btn btn-primary" style="visibility: hidden;">Siguientes Resultados</button><br>
                    <script>change2(1, 8);</script>
                    <br><br><br><br>
                </div>
                <div id="view3">
                    <br><br><br><br>
                    <h3>Agregar Direcciones de E-mail</h3>
                    <br><br>
                    <form action="addemail.php" method="post">
                        <fieldset>
                            <legend>Agrega una Dirección</legend>
                            <label><input type="text" name="email"> E-mail</label>
                            <br><br>
                            <input type="submit" value="Almacena">
                        </fieldset>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>