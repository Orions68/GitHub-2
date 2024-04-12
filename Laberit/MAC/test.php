<?php
exec('rustscan -a 192.168.83.1 > data.txt', $result);
print_r ($result);

?>