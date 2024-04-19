<?php
require "vendor/autoload.php";

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
// use InfluxDB2\Point;

# You can generate an API token from the "API Tokens Tab" in the UI
// $token = $_ENV["INFLUX_TOKEN"];
$token = getenv('INFLUX_TOKEN');

// echo $_ENV["INFLUX_TOKEN"];

$org = 'FP';
$bucket = 'MACDB';

$client = new Client([
    "url" => "http://localhost:8086",
    "token" => "26Sh0bfFnGUiXGRpCy6w_oeHRqoKdH28Hxa8VQVkjCQPCQzt5uNuJTsDkPo070kIq6kv6NHTdxnRmCus93riAQ==",
    // "token" => $token,
]);

// print_r ($client);

// $dato = $_POST["data"];

// $writeApi = $client->createWriteApi();

// $data = "people2,nombre=CesarMatelat edad=56,altura=1.65";

// $writeApi->write($data, WritePrecision::S, $bucket, $org);

// $point = Point::measurement('mem')
//   ->addTag('host', 'host1')
//   ->addField('used_percent', 23.43234543)
//   ->time(microtime(true));

// $writeApi->write($point, WritePrecision::S, $bucket, $org);

// $dataArray = ['name' => 'cpu',
//   'tags' => ['host' => 'server_nl', 'region' => 'us'],
//   'fields' => ['internal' => 5, 'external' => 6],
//   'time' => microtime(true)];

// $writeApi->write($dataArray, WritePrecision::S, $bucket, $org);





$query = "from(bucket: \"MACDB\") |> range(start: -2h) |> filter(fn: (r) => r._measurement == \"persona\")";
$tables = $client->createQueryApi()->query($query, $org);

$i = 0;
foreach ($tables as $table) {
    // echo "<pre>";
    // var_dump($table);
    // echo "</pre>";
        foreach ($table->records as $record) {
        $time[$i] = $record->getTime();
        for ($j = 0; $j < $i; $j++)
        {
            if ($time[$j] == $time[$i])
            {
                $measurement = $record->getMeasurement();
                $field = $record->getField();
                $value = $record->getValue();
                print "<pre>$time $measurement: tiene: $field=$value</pre><br>";
            }
        }
        $i++;
        // $time = $record->getTime();
        // var_export($table->records);
    }
}

// print_r ($tables);

$client->close();
?>