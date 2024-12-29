<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre'])) {
    header('Location: login.php');
    exit();
}

// Incluye el archivo de conexión
require_once 'conexion.php';

// Verifica si el formulario fue enviado y si se recibió un ID válido
if (isset($_POST['pagina_id']) && is_numeric($_POST['pagina_id'])) {
    $paginaId = $_POST['pagina_id'];

    // Consulta para eliminar la página
    $sql = "DELETE FROM paginas_usuarios WHERE ID = :paginaId AND Usuario = :nombre";
    $resultado = $base->prepare($sql);
    $resultado->bindValue(':paginaId', $paginaId, PDO::PARAM_INT);
    $resultado->bindValue(':nombre', $_SESSION['nombre'], PDO::PARAM_STR);

    // Ejecuta la consulta
    if ($resultado->execute()) {
        // Redirige de vuelta a la página de publicaciones con un mensaje de éxito
        header('Location: ver_paginas.php?mensaje=eliminado');
        exit();
    } else {
        // En caso de error, redirige con un mensaje de error
        header('Location: ver_paginas.php?mensaje=error');
        exit();
    }
} else {
    // Si no se recibió un ID válido, redirige con un mensaje de error
    header('Location: ver_paginas.php?mensaje=error');
    exit();
}
?>
