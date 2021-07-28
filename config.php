<?php

ob_start(); //turns on output buffering - saves output of any data until it's done executing the rest of the code 

try {
  $conn = new PDO("mysql:dbname=legoog;host=localhost", "root", "");
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 
}
catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage(); 
}

?>