<?php
session_start();
require_once 'conexion.php'; // Importar el archivo de conexión

function handlePDOError($e) {
    error_log("Error de PDO: " . $e->getMessage());
    echo "Ocurrió un error al procesar su solicitud. Por favor, inténtelo de nuevo más tarde.";
    exit;
}

// Obtener los datos de la publicación para mostrar en el formulario
$publicacion = null;
if (isset($_GET['id'])) {
    try {
        $sql = "SELECT Titulo, Contenido FROM paginas_usuarios WHERE id = :id AND Usuario = :usuario";

        $resultado = $base->prepare($sql);
        $resultado->bindValue(":id", $_GET['id'], PDO::PARAM_INT);
        $resultado->bindValue(":usuario", $_SESSION['nombre'] ?? '', PDO::PARAM_STR);

        $resultado->execute();

        $publicacion = $resultado->fetch();

        if (!$publicacion) {
            echo "Publicación no encontrada o no tienes permiso para modificarla.";
            exit;
        }
    } catch (PDOException $e) {
        handlePDOError($e);
    }
}

// Código para modificar una publicación
if (isset($_POST['modificar'])) {
    try {
        $sql = "UPDATE paginas_usuarios SET Titulo = :titulo, Contenido = :contenido WHERE id = :id AND Usuario = :usuario";

        $resultado = $base->prepare($sql);

        $resultado->bindValue(":titulo", $_POST['titulo'] ?? '', PDO::PARAM_STR);
        $resultado->bindValue(":contenido", $_POST['contenido'] ?? '', PDO::PARAM_STR);
        $resultado->bindValue(":id", $_POST['id'] ?? 0, PDO::PARAM_INT);
        $resultado->bindValue(":usuario", $_SESSION['nombre'] ?? '', PDO::PARAM_STR);

        $resultado->execute();

        header("Location: home.php");
        exit;
    } catch (PDOException $e) {
        handlePDOError($e);
    }
}
?>

<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Publicación</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-r from-blue-500 to-purple-600 flex flex-col items-center">

    <!-- Botón de volver -->
    <a href="comunidad.php"
        class="self-start m-4 text-sm font-medium text-gray-800 bg-gray-200 px-4 py-2 rounded-full shadow-md hover:bg-gray-300 transition duration-300 flex items-center">
        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>

    <div class="w-full max-w-4xl">
        <div class="bg-white shadow-lg rounded-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Modificar Publicación</h1>

            <!-- Formulario para modificar -->
            <form method="post" class="space-y-6">
                <input type="hidden" name="id" value="<?php echo $_GET['id'] ?? ''; ?>">
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($publicacion['Titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div>
                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                    <div id="editor" class="h-48 border border-gray-300 rounded-md"><?php echo $publicacion['Contenido'] ?? ''; ?></div>
                    <input type="hidden" name="contenido" id="contenido">
                </div>
                <div class="flex items-center justify-between pt-4">
                    <button type="submit" name="modificar" class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">Modificar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // Inicialización de Quill
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'header': [1, 2, 3, false] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }]
                ]
            },
            placeholder: 'Escribe tu contenido aquí...'
        });

        // Establecer contenido inicial en el editor
        quill.root.innerHTML = `<?php echo $publicacion['Contenido'] ?? ''; ?>`;

        // Sincronizar contenido del editor con un campo oculto antes de enviar el formulario
        var form = document.querySelector('form');
        form.onsubmit = function(event) {
            var contenido = document.querySelector('#contenido');
            contenido.value = quill.root.innerHTML;

            // Validación: Asegurarse de que no se envíe contenido vacío
            if (!contenido.value.trim()) {
                event.preventDefault();
                alert('El contenido no puede estar vacío.');
            }
        };
    </script>
</body>
</html>
