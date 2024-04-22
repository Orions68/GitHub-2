<?php
include "includes/conn.php";
$title = "Aquí va el Título de la Página";
include "includes/header.php";
?>
<section class="container-fluid pt-3">
    <div class="row" id="pc">
        <div class="col-md-1" id="mobile"></div>
            <div class="col-md-10">
                <div id="view1">
                    <br><br><br><br>
                    <h1>Título de la APP</h1>
                    <h3>Solo Voy a Poner un Par de Cositas Para ver los Estilos de Bootstrap Funcionando.</h3>
                    <table>
                        <tr><th>Este es un Título</th></tr>
                            <tr><td>Esta es una Columna</td></tr>
                    </table>
                    <br><br><br><br>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";