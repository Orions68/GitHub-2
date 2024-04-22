<?php
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
                        <form action="data.php" method="post">
                        <label><input type="text" name="ip" required> Dirección IP</label>
                        <br><br>
                        <input type="submit" onclick="wait();" value="Verifica">
                        </form>
                    </fieldset>
                </div>
            </div>
        <div class="col-md-1"></div>
    </div>
</section>
<?php
include "includes/footer.html";
?>