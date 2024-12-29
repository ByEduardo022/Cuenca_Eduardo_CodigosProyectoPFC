<?php
session_start();
require 'conexion.php'; // Archivo de conexión con PDO

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? '';
$user_id = $_SESSION['user_id'];

// Procesar el formulario para registrar comida
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO comidas (user_id, fecha, nombre_comida, calorias, proteinas, carbohidratos, grasas) 
                VALUES (:user_id, :fecha, :nombre_comida, :calorias, :proteinas, :carbohidratos, :grasas)";
        $stmt = $base->prepare($sql);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':fecha', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(':nombre_comida', trim($_POST['nombre_comida'] ?? ''), PDO::PARAM_STR);
        $stmt->bindValue(':calorias', (int) ($_POST['calorias'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':proteinas', (float) ($_POST['proteinas'] ?? 0), PDO::PARAM_STR);
        $stmt->bindValue(':carbohidratos', (float) ($_POST['carbohidratos'] ?? 0), PDO::PARAM_STR);
        $stmt->bindValue(':grasas', (float) ($_POST['grasas'] ?? 0), PDO::PARAM_STR);

        $stmt->execute();

        header("Location: nutricion.php");
        exit;
    } catch (PDOException $e) {
        $error = "Error al añadir la comida: " . htmlspecialchars($e->getMessage());
    }
}

// Obtener el resumen nutricional del día
try {
    $fecha_actual = date('Y-m-d');
    $sqlResumen = "SELECT 
                    COALESCE(SUM(calorias), 0) AS total_calorias, 
                    COALESCE(SUM(proteinas), 0) AS total_proteinas, 
                    COALESCE(SUM(carbohidratos), 0) AS total_carbohidratos, 
                    COALESCE(SUM(grasas), 0) AS total_grasas 
                   FROM comidas 
                   WHERE user_id = :user_id AND fecha = :fecha";

    $stmtResumen = $base->prepare($sqlResumen);
    $stmtResumen->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmtResumen->bindValue(':fecha', $fecha_actual, PDO::PARAM_STR);
    $stmtResumen->execute();

    $datos_resumen = $stmtResumen->fetch();
} catch (PDOException $e) {
    $error = "Error al obtener el resumen nutricional: " . htmlspecialchars($e->getMessage());
}

// Obtener historial de comidas
try {
    $sqlHistorial = "SELECT fecha, nombre_comida, calorias, proteinas, carbohidratos, grasas 
                     FROM comidas WHERE user_id = :user_id ORDER BY fecha DESC LIMIT 30";
    $stmtHistorial = $base->prepare($sqlHistorial);
    $stmtHistorial->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmtHistorial->execute();
    $historial = $stmtHistorial->fetchAll();
} catch (PDOException $e) {
    $error = "Error al obtener el historial: " . htmlspecialchars($e->getMessage());
}

// Obtener datos para el gráfico
try {
    $sqlProgreso = "SELECT DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha, SUM(calorias) AS calorias 
                    FROM comidas WHERE user_id = :user_id GROUP BY fecha ORDER BY fecha ASC LIMIT 30";
    $stmtProgreso = $base->prepare($sqlProgreso);
    $stmtProgreso->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmtProgreso->execute();
    $progreso = $stmtProgreso->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al obtener el progreso: " . htmlspecialchars($e->getMessage());
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
            <div class="max-w-7xl mx-auto space-y-8">
                <h1 class="text-3xl font-bold text-gray-800">Bienvenido al Plan de Comidas</h1>

                <!-- Formulario de registro de comida y resumen diario en diseño de dos columnas -->
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Formulario de registro de comida -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">Registrar Comida</h2>
                        <form method="POST" class="space-y-4">
                            <div>
                                <label for="nombre_comida" class="block text-sm font-medium text-gray-700 mb-1">Nombre
                                    de la Comida</label>
                                <input type="text" name="nombre_comida" id="nombre_comida" placeholder="Ej: Almuerzo"
                                    required
                                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label for="calorias"
                                    class="block text-sm font-medium text-gray-700 mb-1">Calorías (kcal)</label>
                                <input type="number" name="calorias" id="calorias" placeholder="Ej: 500" required
                                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label for="proteinas"
                                        class="block text-sm font-medium text-gray-700 mb-1">Proteínas (g)</label>
                                    <input type="number" step="0.1" name="proteinas" id="proteinas" placeholder="Ej: 30"
                                        required
                                        class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>
                                <div>
                                    <label for="carbohidratos"
                                        class="block text-sm font-medium text-gray-700 mb-1">Carbohidratos (g)</label>
                                    <input type="number" step="0.1" name="carbohidratos" id="carbohidratos"
                                        placeholder="Ej: 60" required
                                        class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>
                                <div>
                                    <label for="grasas" class="block text-sm font-medium text-gray-700 mb-1">Grasas
                                        (g)</label>
                                    <input type="number" step="0.1" name="grasas" id="grasas" placeholder="Ej: 10"
                                        required
                                        class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>
                            </div>
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-md transition-colors duration-200">Añadir</button>
                        </form>
                    </div>

                    <!-- Resumen diario -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold mb-6 text-gray-800">Resumen de Hoy</h2>
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Calorías -->
                            <div class="bg-orange-100 rounded-xl p-6 shadow-sm">
                                <p class="text-sm font-medium text-gray-600 mb-2">Calorías</p>
                                <div class="flex items-baseline">
                                    <span
                                        class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($datos_resumen['total_calorias'] ?? 0) ?></span>
                                    <span class="text-lg text-gray-600 ml-1">kcal</span>
                                </div>
                            </div>
                            <!-- Proteínas -->
                            <div class="bg-blue-100 rounded-xl p-6 shadow-sm">
                                <p class="text-sm font-medium text-gray-600 mb-2">Proteínas</p>
                                <div class="flex items-baseline">
                                    <span
                                        class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($datos_resumen['total_proteinas'] ?? 0) ?></span>
                                    <span class="text-lg text-gray-600 ml-1">g</span>
                                </div>
                            </div>
                            <!-- Carbohidratos -->
                            <div class="bg-green-100 rounded-xl p-6 shadow-sm">
                                <p class="text-sm font-medium text-gray-600 mb-2">Carbohidratos</p>
                                <div class="flex items-baseline">
                                    <span
                                        class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($datos_resumen['total_carbohidratos'] ?? 0) ?></span>
                                    <span class="text-lg text-gray-600 ml-1">g</span>
                                </div>
                            </div>
                            <!-- Grasas -->
                            <div class="bg-yellow-100 rounded-xl p-6 shadow-sm">
                                <p class="text-sm font-medium text-gray-600 mb-2">Grasas</p>
                                <div class="flex items-baseline">
                                    <span
                                        class="text-4xl font-bold text-gray-900"><?= htmlspecialchars($datos_resumen['total_grasas'] ?? 0) ?></span>
                                    <span class="text-lg text-gray-600 ml-1">g</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de consumo -->
                <div class="bg-white rounded-lg shadow-md p-6 overflow-x-auto">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Historial de Consumo</h2>
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left">Fecha</th>
                                <th class="px-4 py-2 text-left">Comida</th>
                                <th class="px-4 py-2 text-left">Calorías</th>
                                <th class="px-4 py-2 text-left">Proteínas</th>
                                <th class="px-4 py-2 text-left">Carbohidratos</th>
                                <th class="px-4 py-2 text-left">Grasas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $index => $comida): ?>
                                <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : '' ?>">
                                    <td class="px-4 py-2"><?= $comida['fecha'] ?></td>
                                    <td class="px-4 py-2"><?= $comida['nombre_comida'] ?></td>
                                    <td class="px-4 py-2"><?= $comida['calorias'] ?> kcal</td>
                                    <td class="px-4 py-2"><?= $comida['proteinas'] ?> g</td>
                                    <td class="px-4 py-2"><?= $comida['carbohidratos'] ?> g</td>
                                    <td class="px-4 py-2"><?= $comida['grasas'] ?> g</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico de progreso -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Progreso Nutricional</h2>
                    <div class="h-64">
                        <canvas id="graficoProgreso"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Código para el gráfico de progreso
        const progreso = <?= json_encode($progreso) ?>;
        const ctx = document.getElementById('graficoProgreso').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: progreso.map(p => p.fecha),
                datasets: [{
                    label: 'Calorías Diarias',
                    data: progreso.map(p => p.calorias),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    </script>
</body>

</html>