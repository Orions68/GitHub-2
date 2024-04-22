<?php
use InfluxDB2\Client;
// use InfluxDB2\Model\WritePrecision;

$org = 'FP';
$bucket = 'MACDB';

$client = new Client([
    "url" => "http://localhost:8086",
    "token" => $_ENV["INFLUX_TOKEN"],
]);
?>