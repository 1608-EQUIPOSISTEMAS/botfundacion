<?php
$host = "crossover.proxy.rlwy.net";
$port = 16414;
$dbname = "railway";
$user = "root";
$password = "JUJHcePSZPDvZyavSSTckDJyozccyYtg";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdoInmobiliaria = new PDO($dsn, $user, $password);
    // Configurar atributos de PDO
    $pdoInmobiliaria->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    
}
?>