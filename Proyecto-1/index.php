<?php
include "includes/conn.php";
$title = "Índice de la WEB";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br><br><br>
                    <h1>Hola Mostro!</h1>
                    <br><br>
                    <form action="index.php" method="post">
                        <label><input type="number" name="number"> Ingresa el Número Hasta el que Quieras Ver la Serie</label>
                        <br><br>
                        <input type="submit" value="Muestrame estos Valores">
                    </form>
                    <?php
 if (isset($_POST["number"]))
{
    $n = $_POST["number"];
    $fibonacci  = [0, 1];
    echo "<h3>$fibonacci[0]</h3><h3>$fibonacci[1]</h3>";
 
    for($i = 2; $i <= $n; $i++)
    {
        $fibonacci[] = $fibonacci[$i - 1] + $fibonacci[$i - 2];
        echo "<h3>$fibonacci[$i]</h3>";
    }
    echo "<br><br><br><br>";
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