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
                    <?php
 
function fibonacci($n)
{
    $fibonacci  = [0, 1];
    echo $fibonacci[0] ."<br>" . $fibonacci[1] . "<br>";
 
  for($i = 2; $i <= $n; $i++)
    {
        $fibonacci[] = $fibonacci[$i - 1] + $fibonacci[$i - 2];
        echo $fibonacci[$i] . "<br>";
        // echo decbin($fibonacci[$i]) . "<br>";
        // echo dechex($fibonacci[$i]) . "<br>";
    }
}
 
fibonacci(15);
?>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>