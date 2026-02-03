<?php 
require __DIR__ . '/../src/Config/Database.php';

$db = new Database();
$db->connect();
echo "DB läuft";

?>