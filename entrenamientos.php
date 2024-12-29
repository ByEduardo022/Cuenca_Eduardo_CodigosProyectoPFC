<?php
session_start();
include('conexion.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    // Redirigir al formulario de inicio de sesión si no hay una sesión activa
    header("Location: login.php");
    exit();
}

// Obtener el nombre del usuario desde la sesión
$nombreUsuario = $_SESSION['nombre'];

// Manejar la solicitud de añadir tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion'], $_POST['fecha'])) {
    $user_id = $_SESSION['user_id']; // Obtener ID del usuario desde la sesión
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    try {
        // Insertar la tarea en la base de datos
        $sql = "INSERT INTO tareas (user_id, fecha, descripcion) VALUES (:user_id, :fecha, :descripcion)";
        $stmt = $base->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->execute();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Obtener las tareas del usuario para el calendario
$user_id = $_SESSION['user_id'];
$tareas = [];

try {
    $sql = "SELECT fecha, descripcion FROM tareas WHERE user_id = :user_id";
    $stmt = $base->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    while ($row = $stmt->fetch()) {
        $tareas[$row['fecha']][] = $row['descripcion'];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Bienvenido al Panel de Entrenamientos</h1>

            <div class="flex-1 flex">
                <div class="flex-1 mr-4">
                    <!-- Calendario -->
                    <div class="mb-4 bg-white p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-4">
                            <button id="prevMonth"
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 text-sm font-medium rounded">Anterior</button>
                            <h2 id="currentMonth" class="text-xl font-semibold"></h2>
                            <button id="nextMonth"
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-1 text-sm font-medium rounded">Siguiente</button>
                        </div><br>
                        <div class="grid grid-cols-7 gap-1 text-center text-sm">
                            <div class="font-semibold">Lun</div>
                            <div class="font-semibold">Mar</div>
                            <div class="font-semibold">Mié</div>
                            <div class="font-semibold">Jue</div>
                            <div class="font-semibold">Vie</div>
                            <div class="font-semibold">Sáb</div>
                            <div class="font-semibold">Dom</div>
                        </div><br>
                        <div id="calendar" class="grid grid-cols-7 gap-1 mt-2"></div>
                    </div>

                    <!-- Formulario para añadir tareas -->
                    <form method="POST"
                        class="bg-white p-6 rounded-lg shadow-md border border-blue-200">
                        <h2 class="text-2xl font-bold mb-4 text-gray-800">Añade tu proximo Entrenamiento</h2>
                        <div class="flex space-x-2">
                            <input type="text" name="descripcion" id="taskInput" placeholder="Escribe tu tarea"
                                class="flex-grow p-2 border rounded text-sm" required>
                            <input type="date" name="fecha" id="taskDate" class="p-2 border rounded text-sm" required>
                            <button type="submit" id="addTask"
                                class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 text-sm font-medium rounded hover:bg-green-600">Añadir Tarea</button>
                        </div>
                    </form>
                </div>

                <aside class="w-64 bg-white p-6 rounded-lg shadow-md border border-purple-200 flex flex-col h-[calc(100vh-2rem)]">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-800">Próximos Ejercicios</h3>
                    </div>
                    <br>
                    <!-- Contenedor con scroll -->
                    <div class="flex-1 overflow-y-auto pr-2 ">
                        <ul id="taskList" class="space-y-3">
                            <!-- Los elementos de la lista se generarán dinámicamente -->
                        </ul>

                        <!-- Cuando no hay ejercicios -->
                        <div id="noTasks" class="hidden text-center py-6">
                            <i class="fas fa-clipboard-list text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No hay ejercicios programados</p>
                        </div>
                    </div>
                    <br>
                </aside>
            </div>
        </div>
    </div>
    
    <script>
        const calendar = document.getElementById('calendar');
        const currentMonthElement = document.getElementById('currentMonth');
        const prevMonthButton = document.getElementById('prevMonth');
        const nextMonthButton = document.getElementById('nextMonth');
        const taskList = document.getElementById('taskList');

        let currentDate = new Date();
        const tareas = <?php echo json_encode($tareas); ?>; // Cargar tareas desde PHP

        function getRandomColor() {
            const colors = [
                { background: '#FFDDC1', text: '#B91C1C' },
                { background: '#D1FAE5', text: '#065F46' },
                { background: '#FEF9C3', text: '#B45309' },
                { background: '#E0F2FE', text: '#0E7490' },
                { background: '#F0ABFC', text: '#6B21A8' },
                { background: '#FFE4E1', text: '#9D174D' }, // Misty Rose & Maroon
                { background: '#E6E6FA', text: '#4C1D95' }, // Lavender & Indigo
                { background: '#F0FFF0', text: '#166534' }, // Honeydew & Forest Green
                { background: '#F0F8FF', text: '#1E40AF' }, // Alice Blue & Royal Blue
                { background: '#FFF5E6', text: '#92400E' }, // Cosmic Latte & Brown
                { background: '#F5E6D3', text: '#7C2D12' }, // Beige & Rust
                { background: '#E6F9FF', text: '#0369A1' }, // Light Cyan & Dark Blue
                { background: '#FFE5B4', text: '#9A3412' }, // Peach & Sienna
                { background: '#E0FFFF', text: '#047857' }, // Light Cyan & Teal
                { background: '#FFF0F5', text: '#831843' }, // Lavender Blush & Deep Pink
                { background: '#F0FFFF', text: '#1F2937' }, // Azure & Dark Gray
                { background: '#F5F5DC', text: '#3F3F46' }, // Beige & Zinc
                { background: '#E6FFE6', text: '#064E3B' }, // Mint Cream & Dark Green
                { background: '#FFF0DB', text: '#854D0E' }, // Blanched Almond & Dark Goldenrod
                { background: '#E6E6FF', text: '#3730A3' }  // Lavender & Indigo
            ];
            return colors[Math.floor(Math.random() * colors.length)];
        }

        function updateCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            currentMonthElement.textContent = new Date(year, month).toLocaleString('es-ES', { month: 'long', year: 'numeric' });

            calendar.innerHTML = '';
            taskList.innerHTML = ''; // Limpiar la lista de tareas

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const startingDay = firstDay === 0 ? 6 : firstDay - 1;

            for (let i = 0; i < startingDay; i++) {
                const emptyDay = document.createElement('div');
                calendar.appendChild(emptyDay);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                const dayElement = document.createElement('div');
                dayElement.textContent = day;
                dayElement.className = 'border p-1 h-24 overflow-y-auto text-sm bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300';
                dayElement.setAttribute('data-date', date);

                // Highlight current day
                if (date === new Date().toISOString().split('T')[0]) {
                    dayElement.classList.add('ring-2', 'ring-blue-500', 'font-bold');
                }

                // Mostrar tareas en el día correspondiente
                if (tareas[date]) {
                    tareas[date].forEach(task => {
                        const taskElement = document.createElement('div');
                        const color = getRandomColor();
                        taskElement.textContent = task;
                        taskElement.className = 'p-1 mt-1 text-xs rounded-md truncate';
                        taskElement.style.backgroundColor = color.background;
                        taskElement.style.color = color.text;
                        dayElement.appendChild(taskElement);

                        // Agregar tarea a la lista de próximos ejercicios si está en el futuro
                        const taskDate = new Date(date);
                        if (taskDate >= new Date()) {
                            addTaskToList(task, date, color);
                        }
                    });
                }

                calendar.appendChild(dayElement);
            }

            checkEmptyTasks();
        }

        function addTaskToList(task, date, color) {
            const li = document.createElement('li');
            li.className = 'rounded-lg p-3 shadow-sm border border-purple-100 transition-all hover:shadow-md';
            li.style.backgroundColor = color.background;

            li.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-1">
                        <i class="fas fa-running" style="color: ${color.text};"></i>
                    </div>
                    <div class="flex-grow">
                        <p class="font-medium mb-1" style="color: ${color.text};">${task}</p>
                        <div class="flex items-center text-sm" style="color: ${color.text};">
                            <i class="far fa-calendar-alt mr-2"></i>
                            <span>${new Date(date).toLocaleDateString('es-ES')}</span>
                        </div>
                    </div>
                </div>
            `;

            taskList.appendChild(li);
        }

        function checkEmptyTasks() {
            const noTasks = document.getElementById('noTasks');
            if (taskList.children.length === 0) {
                noTasks.classList.remove('hidden');
            } else {
                noTasks.classList.add('hidden');
            }
        }

        prevMonthButton.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        });

        nextMonthButton.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        });

        // Set min date and default value for the date input
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('taskDate');
            const today = new Date();

            // Format today's date as YYYY-MM-DD for the min attribute
            const formattedToday = today.toISOString().split('T')[0];

            // Set the minimum date to today
            dateInput.min = formattedToday;

            // Set the default value to today
            dateInput.value = formattedToday;

            updateCalendar();
        });
    </script>
</body>
</html>

