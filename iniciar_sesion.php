<?php
session_start();

// Incluir archivo de conexión
require_once 'conexion.php'; // Asumiendo que tu archivo de conexión se llama config.php

// Verificar si se enviaron datos desde el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['username'];  // Se espera que coincida con el nombre en la base de datos
    $password = $_POST['password'];

    try {
        // Preparar y ejecutar la consulta para buscar el usuario por el campo 'nombre'
        $stmt = $base->prepare("SELECT id, password FROM usuarios WHERE nombre = :nombre");
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->execute();

        // Verificar si se encontró el usuario
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();

            // Verificar la contraseña (asumiendo que está hashada con password_hash)
            if (password_verify($password, $row['password'])) {
                // Iniciar sesión y almacenar datos en la sesión
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nombre'] = $nombre;

                // Redirigir al usuario a la página de inicio
                header("Location: home.php");
                exit();
            } else {
                // Contraseña incorrecta
                header("Location: error_login.php");
                exit();
            }
        } else {
            // Usuario no encontrado
            header("Location: error_login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Manejo de errores
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
