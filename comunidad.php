<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'];

include('conexion.php');

try {
    $stmt = $base->prepare("SELECT id, Usuario, Titulo, Contenido, Fecha, likes FROM paginas_usuarios ORDER BY fecha DESC");
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al realizar la consulta: " . $e->getMessage();
    exit();
}

$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

if (!empty($searchTerm)) {
    $sql = "SELECT id, Usuario, Titulo, Contenido, Fecha, likes FROM paginas_usuarios WHERE Titulo LIKE :searchTerm ORDER BY fecha DESC";
    $stmt = $base->prepare($sql);
    $stmt->execute([':searchTerm' => "%$searchTerm%"]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generarColorDegradado()
{
    $coloresBase = [
        ['#ADD8E6', '#87CEFA'],
        ['#FFDAB9', '#FFA07A'],
        ['#90EE90', '#98FB98'],
        ['#FFB6C1', '#FF69B4'],
        ['#4AC6B7', '#1DE9B6'], // Turquesa vibrante
        ['#5CBBF6', '#42A5F5'], // Azul brillante
        ['#FF7E67', '#FF6B6B'], // Coral vibrante
        ['#66BB6A', '#81C784'], // Verde primavera
        ['#7986CB', '#5C6BC0'], // Índigo medio
        ['#FF8A65', '#FF7043'], // Naranja coral
        ['#4DB6AC', '#26A69A'], // Verde azulado
        ['#64B5F6', '#42A5F5'], // Azul medio brillante
        ['#F06292', '#EC407A'], // Rosa medio
        ['#4FC3F7', '#29B6F6'], // Azul cielo brillante
        ['#FF9800', '#FFA726'], // Naranja ámbar
        ['#26C6DA', '#00BCD4'], // Cian brillante
        ['#AB47BC', '#9C27B0'], // Púrpura medio
        ['#FFA000', '#FFB300'], // Ámbar dorado
        ['#66BB6A', '#4CAF50'], // Verde lima
        ['#5C6BC0', '#3F51B5'], // Índigo real
        ['#FF7043', '#FF5722'], // Naranja profundo
        ['#EC407A', '#E91E63'], // Rosa fuerte
        ['#7E57C2', '#673AB7'], // Violeta medio
        ['#FFB74D', '#FFA726'], // Naranja claro
        ['#4DD0E1', '#26C6DA'], // Cian claro
        ['#81C784', '#66BB6A'], // Verde claro
        ['#FF8A65', '#FF7043'], // Naranja coral claro
        ['#9575CD', '#7E57C2'], // Púrpura medio claro
        ['#4DB6AC', '#26A69A'], // Verde azulado medio
        ['#A1887F', '#8D6E63'], // Marrón medio
        ['#90A4AE', '#78909C'], // Azul grisáceo
        ['#AED581', '#9CCC65'], // Lima claro
        ['#DCE775', '#D4E157'], // Lima limón
        ['#FFD740', '#FFC400'], // Ámbar intenso
        ['#FFAB40', '#FF9100'], // Naranja intenso
        ['#F06292', '#EC407A'], // Rosa medio intenso
        ['#BA68C8', '#AB47BC'], // Púrpura medio intenso
        ['#4DD0E1', '#00BCD4'], // Cian intenso
        ['#81D4FA', '#4FC3F7'], // Azul claro intenso
        ['#80CBC4', '#4DB6AC'], // Verde azulado claro
        ['#FFF176', '#FFEE58'], // Amarillo claro
        ['#FFB74D', '#FFA726'], // Naranja claro intenso
        ['#FF8A65', '#FF7043']  // Coral intenso
    ];

    return $coloresBase[array_rand($coloresBase)];
}

function obtenerComentarios($base, $pagina_id)
{
    $stmt = $base->prepare("SELECT id, usuario, comentario, fecha FROM comentarios WHERE pagina_id = :pagina_id ORDER BY fecha DESC");
    $stmt->execute([':pagina_id' => $pagina_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_comentario'])) {
    $pagina_id = $_POST['pagina_id'];
    $comentario = $_POST['comentario'];
    $stmt = $base->prepare("INSERT INTO comentarios (pagina_id, usuario, comentario, fecha) VALUES (:pagina_id, :usuario, :comentario, NOW())");
    $stmt->execute([
        ':pagina_id' => $pagina_id,
        ':usuario' => $nombreUsuario,
        ':comentario' => $comentario
    ]);
    header("Location: " . $_SERVER['PHP_SELF'] . "#publicacion-" . $pagina_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrición</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        @media (min-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: 16rem;
                /* w-64 */
                overflow-y: auto;
            }

            .main-content {
                margin-left: 16rem;
                /* w-64 */
            }
        }
    </style>
</head>

<body class="bg-gradient-to-b from-blue-200 via-purple-200 to-blue-300 min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Barra lateral -->
        <aside class="sidebar bg-gradient-to-b from-blue-600 to-blue-800 text-white flex-shrink-0 w-full md:w-64">
            <div class="p-6">
                <div class="w-24 h-24 rounded-full bg-white mx-auto mb-4 flex items-center justify-center shadow-lg">
                    <i class="fas fa-user text-4xl text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-center"><?php echo htmlspecialchars($nombreUsuario); ?></h2>
            </div>
            <nav class="mt-6 space-y-2">
                <a href="home.php"
                    class="flex items-center py-3 px-6 text-white hover:bg-white/20 transition-colors duration-200 rounded-lg">
                    <i class="fas fa-home mr-3"></i> Panel Principal
                </a>
                <a href="entrenamientos.php"
                    class="flex items-center py-3 px-6 text-white hover:bg-white/20 transition-colors duration-200 rounded-lg">
                    <i class="fas fa-dumbbell mr-3"></i> Entrenamientos
                </a>
                <a href="nutricion.php"
                    class="flex items-center py-3 px-6 text-white hover:bg-white/20 transition-colors duration-200 rounded-lg">
                    <i class="fas fa-utensils mr-3"></i> Nutrición
                </a>
                <a href="progreso.php"
                    class="flex items-center py-3 px-6 text-white hover:bg-white/20 transition-colors duration-200 rounded-lg">
                    <i class="fas fa-chart-line mr-3"></i> Progreso
                </a>
                <a href="comunidad.php"
                    class="flex items-center py-3 px-6 text-white hover:bg-white/20 transition-colors duration-200 rounded-lg">
                    <i class="fas fa-users mr-3"></i> Comunidad
                </a>
            </nav><br><br><br><br>
            <div class="p-6 mt-auto">
                <a href="cerrar_sesion.php"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Contenido principal -->
         <main class="main-content flex-1 p-8 overflow-y-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Bienvenido de nuevo al Apartado de Comunidad</h1>

            <div class="container mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Sistema de Búsqueda -->
                    <div class="lg:col-span-2 rounded-lg p-6"><br><br>
                        <form method="post" action=""><br>
                            <div class="flex">
                           
                                <input
                                    class="flex-1 border border-gray-300 p-2 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    name="searchTerm" type="text" placeholder="Introduce el término de búsqueda..."
                                    value="<?php echo htmlspecialchars($searchTerm); ?>" />
                                <button
                                    class="bg-gradient-to-r from-purple-500 to-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition duration-300">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- Botón para crear publicación -->
                    <div class="rounded-lg p-6 text-center">
                        <a href="crear_pagina.php"
                            class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 ease-in-out">
                            Crear Publicación
                        </a><br>
                        <a href="ver_paginas.php"
                            class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 ease-in-out">
                            Ver Mis Publicaciones
                        </a>
                    </div>

                </div>

                <!-- Mostrar publicaciones -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($resultados as $fila): ?>
                        <?php list($color1, $color2) = generarColorDegradado(); ?>
                        <div id="publicacion-<?php echo $fila['id']; ?>"
                            class="flex flex-col bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition duration-300">
                            <div class="w-full p-4"
                                style="background: linear-gradient(135deg, <?php echo $color1; ?>, <?php echo $color2; ?>);">
                                <div class="flex items-center">
                                    <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mr-4">
                                        <i class="fas fa-user text-2xl text-gray-700"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($fila['Usuario']); ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 flex-1">
                                <h3 class="text-lg font-bold text-gray-800 mb-2">
                                    <?php echo htmlspecialchars($fila['Titulo']); ?>
                                </h3>
                                <div class="text-gray-700 text-sm mb-4">
                                    <?php echo htmlspecialchars_decode($fila['Contenido']); ?>
                                </div>
                            </div>
                            <div class="bg-gray-100 p-4 flex justify-between items-center">
                                <button onclick="openModal(<?php echo $fila['id']; ?>)"
                                    class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg shadow-md hover:shadow-lg hover:from-blue-600 hover:to-purple-700 transition duration-300 ease-in-out flex items-center space-x-2">
                                    <i class="fas fa-comment"></i>
                                </button>

                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($fila['Fecha']); ?></p>
                            </div>
                        </div>

                        <!-- Modal de comentarios -->
                        <div id="modal-<?php echo $fila['id']; ?>"
                            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white w-full max-w-2xl mx-auto rounded-lg shadow-lg p-6 relative">
                                <button onclick="closeModal(<?php echo $fila['id']; ?>)"
                                    class="absolute top-4 right-4 text-red-500 hover:text-red-700 transition-colors duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <h4 class="text-2xl text-center font-bold mb-6 text-gray-800 border-b pb-2">Comentarios</h4>
                                <div
                                    class="space-y-4 mb-6 overflow-y-auto max-h-64 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                    <?php
                                    $comentarios = obtenerComentarios($base, $fila['id']);
                                    foreach ($comentarios as $comentario):
                                        ?>
                                        <div class="bg-gray-50 p-4 rounded-lg shadow">
                                            <p class="text-sm mb-1">
                                                <strong
                                                    class="text-blue-600"><?php echo htmlspecialchars($comentario['usuario']); ?>:</strong>
                                                <span
                                                    class="text-gray-700"><?php echo htmlspecialchars($comentario['comentario']); ?></span>
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                <?php echo htmlspecialchars($comentario['fecha']); ?>
                                            </p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <form method="POST" action="" class="space-y-4">
                                    <input type="hidden" name="pagina_id" value="<?php echo $fila['id']; ?>">
                                    <textarea name="comentario"
                                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                        placeholder="Escribe tu comentario..." rows="4"></textarea>
                                    <button type="submit" name="nuevo_comentario"
                                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                                        Enviar comentario
                                    </button>
                                </form>
                            </div>
                        </div>


                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>

<script>
    function openModal(id) {
        const modal = document.getElementById(`modal-${id}`);
        modal.classList.remove('hidden');
    }

    function closeModal(id) {
        const modal = document.getElementById(`modal-${id}`);
        modal.classList.add('hidden');
    }
</script>

</html>