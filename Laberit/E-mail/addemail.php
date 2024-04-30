<?php
include "includes/conn.php";
if (isset($_POST["email"]))
{
    $email = $_POST["email"];
    $sql = "INSERT INTO email VALUES('$email');";
    $stmt = $conn->prepare($sql)->execute();
    echo "<script>window.open('index.php');</script>";
}
?>