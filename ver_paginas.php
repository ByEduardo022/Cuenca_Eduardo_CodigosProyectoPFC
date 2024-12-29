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

// Nombre del usuario actual
$nombreUsuario = $_SESSION['nombre'];

// Consulta para obtener las publicaciones del usuario activo
$sql = "SELECT * FROM paginas_usuarios WHERE Usuario = :nombre ORDER BY fecha ASC";
$resultado = $base->prepare($sql);
$resultado->bindValue(":nombre", $nombreUsuario, PDO::PARAM_STR);
$resultado->execute();

// Obtiene los datos de la consulta
$paginas = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Función para generar el color de degradado aleatorio
function generarColorDegradado()
{
    $coloresBase = [
        ['#ADD8E6', '#87CEFA'],
        ['#FFDAB9', '#FFA07A'],
        ['#90EE90', '#98FB98'],
        ['#FFB6C1', '#FF69B4'],
        ['#4AC6B7', '#1DE9B6'],
        ['#5CBBF6', '#42A5F5'],
        ['#FF7E67', '#FF6B6B'],
        ['#66BB6A', '#81C784'],
        ['#7986CB', '#5C6BC0'],
        ['#FF8A65', '#FF7043'],
        ['#4DB6AC', '#26A69A'],
        ['#64B5F6', '#42A5F5'],
        ['#F06292', '#EC407A'],
        ['#4FC3F7', '#29B6F6'],
        ['#FF9800', '#FFA726'],
        ['#26C6DA', '#00BCD4'],
        ['#AB47BC', '#9C27B0'],
        ['#FFA000', '#FFB300'],
        ['#66BB6A', '#4CAF50'],
        ['#5C6BC0', '#3F51B5'],
        ['#FF7043', '#FF5722'],
        ['#EC407A', '#E91E63'],
        ['#7E57C2', '#673AB7'],
        ['#FFB74D', '#FFA726'],
        ['#4DD0E1', '#26C6DA'],
        ['#81C784', '#66BB6A'],
        ['#FF8A65', '#FF7043'],
        ['#9575CD', '#7E57C2'],
        ['#4DB6AC', '#26A69A'],
        ['#A1887F', '#8D6E63'],
        ['#90A4AE', '#78909C'],
        ['#AED581', '#9CCC65'],
        ['#DCE775', '#D4E157'],
        ['#FFD740', '#FFC400'],
        ['#FFAB40', '#FF9100'],
        ['#F06292', '#EC407A'],
        ['#BA68C8', '#AB47BC'],
        ['#4DD0E1', '#00BCD4'],
        ['#81D4FA', '#4FC3F7'],
        ['#80CBC4', '#4DB6AC'],
        ['#FFF176', '#FFEE58'],
        ['#FFB74D', '#FFA726'],
        ['#FF8A65', '#FF7043']
    ];

    return $coloresBase[array_rand($coloresBase)];
}

// Asignar el color de degradado a cada página
$colorDegradado = generarColorDegradado();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Publicaciones</title>
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

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
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
        <main class="ml-64 flex-1 overflow-auto p-6">
            <div class="container mx-auto">
                <h1 class="text-4xl font-bold text-center mb-8 bg-clip-text text-transparent bg-black">
                Bienvenido al Apartado de Gestion de Publicaciones
                </h1><br>

                <!-- Publicaciones -->
                <?php if (count($paginas) > 0): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($paginas as $pagina): ?>
                            <article
                                class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative min-h-[200px]">
                                <div class="p-4"
                                    style="background: linear-gradient(to right, <?= $colorDegradado[0] ?>, <?= $colorDegradado[1] ?>);">
                                    <h2 class="text-2xl font-bold text-white mb-2">
                                        <?= htmlspecialchars($pagina['Titulo']) ?>
                                    </h2>
                                </div>
                                <div class="p-6 pb-16">
                                    <div class="prose text-gray-600">
                                        <?= htmlspecialchars_decode($pagina['Contenido']) ?>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="absolute bottom-0 right-0 left-0 p-4 bg-gray-50 border-t border-gray-100">
                                    <div class="flex justify-end space-x-4">
                                        <a href="modificar_pagina.php?id=<?= $pagina['ID'] ?>"
                                            class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded-full transition-colors duration-200"
                                            title="Modificar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" onclick="openModal(<?= $pagina['ID'] ?>)"
                                            class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors duration-200"
                                            title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center">No tienes publicaciones.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white w-full max-w-md p-8 rounded-lg shadow-xl">
            <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Confirmar Eliminación</h2>
            <p class="text-center text-gray-600 mb-6">¿Estás seguro de que deseas eliminar esta publicación? Esta acción
                no se puede deshacer.</p>

            <!-- Formulario de eliminación -->
            <form id="deleteForm" method="POST" action="borrar_pagina.php">
                <input type="hidden" id="paginaId" name="pagina_id">

                <!-- Botones de Confirmación -->
                <div class="flex justify-center gap-6">
                    <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-300">
                        Eliminar
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="w-full sm:w-auto px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors duration-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Abre el modal y establece el ID de la página a eliminar
        function openModal(id) {
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('paginaId').value = id; // Asignamos el ID al campo oculto
        }

        // Cierra el modal
        function closeModal() {
            document.getElementById('confirmModal').classList.add('hidden');
        }
    </script>

</body>

</html>