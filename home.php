<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'];
$user_id = $_SESSION['user_id'];

function formatearFecha($fecha) {
    return date('d-m-Y', strtotime($fecha));
}

function calcularDiasTranscurridos($fechaEntrenamiento) {
    $hoy = new DateTime();
    $entrenamiento = new DateTime($fechaEntrenamiento);
    $diferencia = $hoy->diff($entrenamiento);
    return max(0, $diferencia->days);
}

function obtenerDiaSemana($fecha) {
    $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    return $diasSemana[date('w', strtotime($fecha))];
}

try {
    // Consulta para nutrición
    $sql = "SELECT 
                SUM(proteinas) AS total_proteinas, 
                SUM(carbohidratos) AS total_carbohidratos, 
                SUM(grasas) AS total_grasas 
            FROM comidas 
            WHERE user_id = :user_id";

    $stmt = $base->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $resultados = $stmt->fetch();

    $totalProteinas = $resultados['total_proteinas'] ?? 0;
    $totalCarbohidratos = $resultados['total_carbohidratos'] ?? 0;
    $totalGrasas = $resultados['total_grasas'] ?? 0;

    // Consulta para obtener los entrenamientos
    $sqlTareas = "SELECT fecha, descripcion, created_at,
                  GREATEST(DATEDIFF(CURDATE(), fecha), 0) as dias_transcurridos
                  FROM tareas 
                  WHERE user_id = :user_id 
                  ORDER BY dias_transcurridos ASC, fecha DESC
                  LIMIT 5";
    
    $stmtTareas = $base->prepare($sqlTareas);
    $stmtTareas->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtTareas->execute();
    $tareas = $stmtTareas->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para datos del gráfico de entrenamientos
    $sqlGrafico = "SELECT DATE(fecha) as fecha, COUNT(*) as cantidad 
                   FROM tareas 
                   WHERE user_id = :user_id 
                   AND fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                   GROUP BY DATE(fecha) 
                   ORDER BY fecha";
    
    $stmtGrafico = $base->prepare($sqlGrafico);
    $stmtGrafico->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtGrafico->execute();
    $datosGrafico = $stmtGrafico->fetchAll(PDO::FETCH_ASSOC);

    // Nueva consulta para contar el número total de entrenamientos
    $sqlTotalEntrenamientos = "SELECT COUNT(*) as total_entrenamientos
                              FROM tareas
                              WHERE user_id = :user_id";
    $stmtTotalEntrenamientos = $base->prepare($sqlTotalEntrenamientos);
    $stmtTotalEntrenamientos->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtTotalEntrenamientos->execute();
    $resultadoTotalEntrenamientos = $stmtTotalEntrenamientos->fetch();
    $totalEntrenamientos = $resultadoTotalEntrenamientos['total_entrenamientos'] ?? 0;

    // Cálculo estimado de calorías (basado en el número de entrenamientos)
    // Asumimos un promedio de 300 calorías por entrenamiento
    $caloriasEstimadas = $totalEntrenamientos * 200;

    // Cálculo de la puntuación nutricional (ejemplo simplificado)
    $puntuacionNutricional = min(100, ($totalProteinas + $totalCarbohidratos + $totalGrasas) / 3);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Preparar datos para el gráfico
$diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
$cantidadesPorDia = array_fill_keys($diasSemana, 0);

// Obtener la fecha actual y calcular el inicio de la semana (Lunes)
$hoy = new DateTime();
$inicioSemana = clone $hoy;
$inicioSemana->modify('last monday');

// Preparar array con todos los días de la semana
$fechasCompletas = [];
for ($i = 0; $i < 7; $i++) {
    $fecha = clone $inicioSemana;
    $fecha->modify("+$i days");
    $fechasCompletas[$fecha->format('Y-m-d')] = 0;
}

// Rellenar con datos reales
foreach ($datosGrafico as $dato) {
    $diaSemana = obtenerDiaSemana($dato['fecha']);
    $cantidadesPorDia[$diaSemana] = (int)$dato['cantidad'];
}

$fechas = array_values($diasSemana);
$cantidades = array_values($cantidadesPorDia);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal</title>
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

        <!-- Contenido Principal -->
        <main class="main-content flex-1 p-8 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Resumen de Entrenamiento -->
                <div class="bg-white p-6 rounded-lg shadow-md col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Resumen de Entrenamiento</h3>
                        <a href="#" class="text-blue-600 hover:underline">Ver Todo</a>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="workoutChart"></canvas>
                    </div>
                </div>

                <!-- Resumen -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-4">Resumen</h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-3 bg-orange-100 rounded-lg">
                            <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-fire text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Calorías Estimadas</p>
                                <p class="font-semibold"><?php echo number_format($caloriasEstimadas, 0); ?> kcal</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-blue-100 rounded-lg">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-dumbbell text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Entrenamientos Completados</p>
                                <p class="font-semibold"><?php echo $totalEntrenamientos; ?> sesiones</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-green-100 rounded-lg">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-apple-alt text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Puntuación Nutricional</p>
                                <p class="font-semibold"><?php echo number_format($puntuacionNutricional, 0); ?>/100</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historial de Usuario -->
                <div class="bg-white p-6 rounded-lg shadow-md col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Historial de Entrenamiento</h3>
                        <a href="#" class="text-blue-600 hover:underline">Ver Todo</a>
                    </div>
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-600">
                                <th class="pb-3 pr-8">Fecha</th>
                                <th class="pb-3 pr-8">Entrenamiento</th>
                                <th class="pb-3 text-center">Hora de Registro</th>
                                <th class="pb-3 text-center">Días Transcurridos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tareas as $tarea): ?>
                                <tr class="border-t">
                                    <td class="py-2 pr-8"><?php echo formatearFecha($tarea['fecha']); ?></td>
                                    <td class="py-2 pr-8"><?php echo htmlspecialchars($tarea['descripcion']); ?></td>
                                    <td class="py-2 text-center"><?php echo date('H:i', strtotime($tarea['created_at'])); ?></td>
                                    <td class="py-2 text-center"><?php echo $tarea['dias_transcurridos']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Plan Nutricional -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Plan Nutricional</h3>
                    </div>
                    <canvas id="nutritionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </main>
    </div>


    <script>
        // Datos del gráfico de entrenamientos desde PHP
        const fechas = <?php echo json_encode($fechas); ?>;
        const cantidades = <?php echo json_encode($cantidades); ?>;

        var ctxWorkout = document.getElementById('workoutChart').getContext('2d');
        var workoutChart = new Chart(ctxWorkout, {
            type: 'bar',
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Entrenamientos por día',
                    data: cantidades,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Datos obtenidos desde PHP
        const totalProteinas = <?php echo json_encode($totalProteinas); ?>;
        const totalCarbohidratos = <?php echo json_encode($totalCarbohidratos); ?>;
        const totalGrasas = <?php echo json_encode($totalGrasas); ?>;

        // Configuración de Chart.js
        var ctx = document.getElementById('nutritionChart').getContext('2d');
        var nutritionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Proteínas', 'Carbohidratos', 'Grasas'],
                datasets: [{
                    label: 'Gramos Consumidos Totales',
                    data: [totalProteinas, totalCarbohidratos, totalGrasas],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)', // Proteínas
                        'rgba(255, 206, 86, 0.2)', // Carbohidratos
                        'rgba(153, 102, 255, 0.2)' // Grasas
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>

