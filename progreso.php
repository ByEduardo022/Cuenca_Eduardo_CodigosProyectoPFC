<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'] ?? '';
$user_id = $_SESSION['user_id'];

// Procesar el formulario para agregar o actualizar registros
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $peso = $_POST['peso'] ?? null;
    $altura = $_POST['altura'] ?? null;
    $porcentaje_grasa = $_POST['porcentaje_grasa'] ?? null;
    $fecha_registro = date('Y-m-d'); // Guardar en formato YYYY-MM-DD para compatibilidad con la base de datos

    if ($peso && $altura && $porcentaje_grasa) {
        try {
            $sql = "INSERT INTO progreso (usuario_id, peso, altura, porcentaje_grasa, fecha_registro) 
                    VALUES (:usuario_id, :peso, :altura, :porcentaje_grasa, :fecha_registro)";
            $stmt = $base->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $user_id,
                ':peso' => $peso,
                ':altura' => $altura,
                ':porcentaje_grasa' => $porcentaje_grasa,
                ':fecha_registro' => $fecha_registro,
            ]);
        } catch (PDOException $e) {
            echo "Error al guardar los datos: " . $e->getMessage();
        }
    }
}

// Obtener todos los registros del usuario
$registros = [];
try {
    $sql = "SELECT * FROM progreso WHERE usuario_id = :usuario_id ORDER BY fecha_registro ASC";
    $stmt = $base->prepare($sql);
    $stmt->execute([':usuario_id' => $user_id]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener los datos: " . $e->getMessage();
}

// Preparar datos para el gráfico y el resumen
$labels = [];
$weights = [];
$heights = [];
$fatPercentages = [];

foreach ($registros as $registro) {
    $labels[] = date('d-m-Y', strtotime($registro['fecha_registro'])); // Convertir formato a dd-mm-aaaa
    $weights[] = $registro['peso'];
    $heights[] = $registro['altura'];
    $fatPercentages[] = $registro['porcentaje_grasa'];
}

// Obtener el registro más reciente para el resumen




$ultimo_registro = end($registros);
$peso_actual = $ultimo_registro['peso'] ?? 'N/A';
$altura_actual = $ultimo_registro['altura'] ?? 'N/A';
$grasa_actual = $ultimo_registro['porcentaje_grasa'] ?? 'N/A';

$peso_actual = floatval($ultimo_registro['peso'] ?? 0);
$altura_actual = floatval($ultimo_registro['altura'] ?? 0);

$imc_actual = $altura_actual ? round($peso_actual / (($altura_actual / 100) ** 2), 1) : 'N/A';

// Calcular cambios
$penultimo_registro = prev($registros);
$cambio_peso = $penultimo_registro ? round($peso_actual - $penultimo_registro['peso'], 1) : 0;
$cambio_grasa = $penultimo_registro ? round($grasa_actual - $penultimo_registro['porcentaje_grasa'], 1) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso - FitTrack Pro</title>
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

        <main class="main-content flex-1 p-8 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-8 text-gray-800">Bienvenido a tu Apartado de Progreso</h1>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Registrar Nuevo Progreso</h2>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label for="peso" class="block text-sm font-medium text-gray-700 mb-1">Peso (kg):</label>
                            <input type="number" step="0.01" id="peso" name="peso" class="w-full p-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label for="altura" class="block text-sm font-medium text-gray-700 mb-1">Altura (cm):</label>
                            <input type="number" step="0.01" id="altura" name="altura" class="w-full p-2 border border-gray-300 rounded-md" required>
                        </div>
                        <div>
                            <label for="porcentaje_grasa" class="block text-sm font-medium text-gray-700 mb-1">Porcentaje de Grasa (%):</label>
                            <input type="number" step="0.01" id="porcentaje_grasa" name="porcentaje_grasa" class="w-full p-2 border border-gray-300 rounded-md" required>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">Guardar Progreso</button>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Historial de Progreso</h2>
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Peso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Altura</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Grasa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars(date('d-m-Y', strtotime($registro['fecha_registro']))); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($registro['peso']); ?> kg</td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($registro['altura']); ?> cm</td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($registro['porcentaje_grasa']); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
             <!-- Cuadros de Resumen -->
             <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Peso Actual</h2>
                    <p class="text-4xl font-bold text-blue-600"><?php echo htmlspecialchars($peso_actual); ?> kg</p>
                    <p class="text-sm text-gray-500">Cambio: <?php echo $cambio_peso >= 0 ? "+$cambio_peso" : $cambio_peso; ?> kg</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Porcentaje de Grasa</h2>
                    <p class="text-4xl font-bold text-red-600"><?php echo htmlspecialchars($grasa_actual); ?>%</p>
                    <p class="text-sm text-gray-500">Cambio: <?php echo $cambio_grasa >= 0 ? "+$cambio_grasa" : $cambio_grasa; ?>%</p>
                </div>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">IMC</h2>
                    <p class="text-4xl font-bold text-green-600"><?php echo htmlspecialchars($imc_actual); ?></p>
                    <p class="text-sm text-gray-500">Indice de Masa Corporal</p>
                </div>
            </div>
            
            <!-- Gráfica de Progreso -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Gráfica de Progreso</h2>
                <canvas id="progressChart"></canvas>
            </div>
        </main>
    </div>
    <script>
        const labels = <?php echo json_encode($labels); ?>;
        const weights = <?php echo json_encode($weights); ?>;
        const fatPercentages = <?php echo json_encode($fatPercentages); ?>;

        new Chart(document.getElementById('progressChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Peso (kg)',
                        data: weights,
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.1
                    },
                    {
                        label: '% Grasa',
                        data: fatPercentages,
                        borderColor: 'rgb(220, 38, 38)',
                        tension: 0.1
                    }
                ]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
