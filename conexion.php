<?php
// Datos de conexión a la base de datos
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'fit';
$port = 3306;

try {
    // Crear conexión PDO
    $base = new PDO("mysql:host=$hostname;dbname=$database;port=$port", $username, $password);
    $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $base->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Manejo de errores
    echo 'Conexión fallida: ' . $e->getMessage();
    exit;
}
?>
