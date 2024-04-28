<?php
include "includes/conn.php";
$title = "Monitor de E-mails.";
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
                    <?php
                        echo "<h1>Verifica los Mensajes IMAP de Gmail</h1>";
                        $yes_email = [];
                        $index_ok = 0;
                        $no_email = [];

                        $mailbox = imap_open("{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX", "matelat@gmail.com", $_ENV["Gmail-matelat"]);

                        $check = imap_check($mailbox);
                        $today = "";
                        $date = $check->Date; // Fecha del Últmo Mensaje.
                        $qtty = $check->Nmsgs; // Cantidad Total de Mansajes en la Bandeja de Entrada.
                        $temp = explode(" ", $date);
                        for ($i = 0; $i < 4; $i++) // Bucle hasta 4 para obtener los 4 primeros datos del Array $temp, Nombre del Día, Día, Mes, Año.
                        {
                            $today .= $temp[$i]; // Concateno la Fecha en la Variable $today.
                        }
                        // echo "<h3>$today</h3>
                        // <pre>
                        // Fecha de los Mensajes Más Recientes : $check->Date
                        // <br></pre>";

                        while ($qtty > 0) // Mientras la Cantiad de Mensajes Sea Mayor que 0.
                        {
                            $mail_today = "";
                            $header = imap_headerinfo($mailbox, $qtty); // Obtiene la Información de Todos los Mensajes.
                            $address = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                            $mail_date = $header->Date; // Fecha de los Mensajes.
                            $mail_temp = explode(" ", $mail_date);
                            for ($i = 0; $i < 4; $i++)
                            {
                                $mail_today .= $mail_temp[$i];
                            }
                            if ($today == $mail_today) // Si los Mensajes son de Hoy, $today = a los Mensajes del Día.
                            {
                                $sql = "SELECT email FROM email WHERE email='$address';";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                if ($stmt->rowCount() > 0)
                                {
                                    $row = $stmt->fetch(PDO::FETCH_OBJ);
                                    $yes_email[$index_ok] = $row->email;
                                    echo "<h3>Fecha del Mansaje : $header->Date<br>
                                    Asunto del Mensaje : $header->Subject<br></h3>"; // Muestro la Fecha y el Asunto del Mensaje.
                                    echo "<h3>Dirección de Donde Proviene: $address</h3>";
                                    $index_ok++;
                                }
                            }
                            else
                            {
                                $qtty = 1;
                            }
                            $qtty--; // Decremento la Cantidad de Mensajes.
                        }

                        imap_close($mailbox);
                        $sql = "SELECT COUNT(email) FROM email;";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $number = $stmt->fetchColumn();
                        if (count($yes_email) < $number)
                        {
                            $i = 0;
                            $j = 0;
                            $sql = "SELECT * FROM email";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_OBJ))
                            {
                                while ($i < count($yes_email) && $yes_email[$i] != $row->email)
                                {
                                    $i++;
                                }
                                if ($i == count($yes_email))
                                {
                                    $no_email[$j] = $row->email;
                                    $j++;
                                    $i = 0;
                                }
                                else
                                {
                                    $i = 0;
                                }
                            }
                        }

                        if (count($no_email) > 0)
                        {
                            echo "<h3>No Ha Llegado Nada de Estas Direcciónes:</h3>";
                            for ($i = 0; $i < count($no_email); $i++)
                            {
                                echo "<h5>$no_email[$i]</h5>";
                            }
                        }
                    ?>
                    <br><br><br><br>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>