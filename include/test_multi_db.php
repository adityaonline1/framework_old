<?php
$con = new PDO('mysql:host=localhost', "root", "", 
      array(PDO::ATTR_PERSISTENT => true));


$con->exec("USE apsdma");

$a=$con->query("SHOW TABLES");
$a=$a->fetchAll(PDO::FETCH_ASSOC);
print_r($a);

echo "<br><br>";
sleep(20);
$con->exec("USE bio_equipment");

$a=$con->query("SHOW TABLES");
$a=$a->fetchAll(PDO::FETCH_ASSOC);

print_r($a);


?>