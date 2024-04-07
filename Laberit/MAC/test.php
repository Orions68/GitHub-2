<?php
$string = "8C1F64FF3";


for ($i = 2; $i < strlen($string); $i+=3)
{
    $string = substr_replace($string, ":", $i, 0);
}
echo $string;

$str = "aa-bb-cc-dd-ee-ff";

$str = str_replace("-", ":", $str);

echo $str;


$date_time = "11-Dec-13 8:05:44 AM";
$new_date = date("Y-m-d", strtotime($date_time));

echo $new_date;
?>