<?php
// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $nombre = htmlentities(addslashes(trim($_POST['name'])));
    $email = htmlentities(addslashes(trim($_POST['email'])));
    $password = htmlentities(addslashes($_POST['password']));
    $password_confirmation = htmlentities(addslashes($_POST['password_confirmation']));

    // Validar que las contraseñas coincidan
    if ($password !== $password_confirmation) {
        header("location: contraseña_distinta.php");
        exit;
    }

    // Verificar si se aceptaron los términos y condiciones
    if (!isset($_POST['terms'])) {
        header("location: terms_no_aceptados.php");
        exit;
    }

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Conectar a la base de datos
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $base->exec("SET CHARACTER SET utf8");

        // Verificar si el nombre de usuario ya existe
        $sql_nombre = "SELECT * FROM usuarios WHERE nombre = :nombre";
        $resultado_nombre = $base->prepare($sql_nombre);
        $resultado_nombre->bindValue(":nombre", $nombre, PDO::PARAM_STR);
        $resultado_nombre->execute();

        if ($resultado_nombre->rowCount() > 0) {
            // Si el nombre de usuario ya existe, redirigir a una página de error
            header("location: usuario_ya_existe.php");
            exit;
        }

        // Verificar si el email ya existe
        $sql_email = "SELECT * FROM usuarios WHERE email = :email";
        $resultado_email = $base->prepare($sql_email);
        $resultado_email->bindValue(":email", $email, PDO::PARAM_STR);
        $resultado_email->execute();

        if ($resultado_email->rowCount() > 0) {
            // Si el email ya existe, redirigir a una página de error
            header("location: email_ya_existe.php");
            exit;
        }

        // Si ni el nombre de usuario ni el email existen, inserta el nuevo usuario
        $sql_insert = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)";
        $resultado = $base->prepare($sql_insert);
        $resultado->bindValue(":nombre", $nombre, PDO::PARAM_STR);
        $resultado->bindValue(":email", $email, PDO::PARAM_STR);
        $resultado->bindValue(":password", $hashed_password, PDO::PARAM_STR);
        $resultado->execute();

        // Mostrar mensaje de usuario registrado correctamente
        $_SESSION['mensaje'] = "Usuario registrado correctamente";
        header("location: index.php");
        exit;
    } catch (Exception $e) {
        // Mostrar un mensaje de error
        echo "Error: " . $e->getMessage();
    }
}
?>
