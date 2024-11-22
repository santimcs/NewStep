<?php
   $hostName = "127.0.0.1:3307"; // Example if using port 3307  
   $databaseName = "stock";
   $username = "root";
   $password = "";

   try {
      // Establish a connection using PDO
      $conn = new PDO("mysql:host=$hostName;dbname=$databaseName", $username, $password);
      
      // Set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   } catch(PDOException $e) {
      // Error handling function using PDO
      die("Connection failed: " . $e->getMessage());
   }
?>