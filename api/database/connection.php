<?php
  define('CONFIG_INCLUDED', true);
  require_once __DIR__ . '/../../../config/config.php';
  $host_name = DB_HOST;
  $database = DB_DATABASE;
  $user_name = DB_USERNAME;
  $password = DB_PASSWORD;
  $pdo = null;

  try {
    $pdo = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
  } catch (PDOException $e) {
    echo "Erreur!:" . $e->getMessage() . "<br/>";
    die();
  }
?>
