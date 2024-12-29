<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitLife - Tu Compañero de Fitness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos de respaldo por si Tailwind no carga */
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
    </style>
</head>

<body class="h-full bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Barra de navegación superior -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <span class="text-3xl font-extrabold text-indigo-600">FitLife</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="register.php"
                            class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-6 py-3 rounded-full text-sm font-medium transition duration-300 ease-in-out transform hover:scale-105">Registrarse</a>
                        <a href="login.php"
                            class="bg-indigo-600 text-white hover:bg-indigo-700 px-6 py-3 rounded-full text-sm font-medium transition duration-300 ease-in-out transform hover:scale-105">Iniciar
                            Sesión</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Contenido principal -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
                <!-- Hero section -->
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-3xl shadow-2xl overflow-hidden mb-12">
                    <div class="px-6 py-12 sm:px-12 lg:px-16 xl:px-24">
                        <div class="text-center max-w-4xl mx-auto">
                            <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight">
                                <span class="block">Transforma tu vida</span>
                                <span class="block text-indigo-200">con FitLife</span>
                            </h2>
                            <p class="mt-6 text-xl md:text-2xl leading-8 text-indigo-100">
                                Alcanza tus metas de fitness con planes personalizados, seguimiento de progreso en
                                tiempo real y una comunidad de apoyo motivadora.
                            </p>
                            <div
                                class="mt-10 flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                                <a href="login.php"
                                    class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-indigo-700 bg-white hover:bg-indigo-50 transition duration-300 ease-in-out transform hover:scale-105">
                                    Empieza ahora
                                </a>
                                <a href="login.php"
                                    class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-indigo-500 hover:bg-indigo-400 transition duration-300 ease-in-out transform hover:scale-105">
                                    Conoce más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                    <!-- Tarjetas de información -->
                    <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            class="bg-white overflow-hidden shadow-lg rounded-2xl transition duration-300 ease-in-out transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-indigo-500 rounded-full p-4">
                                        <i class="fas fa-dumbbell text-2xl text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-lg font-medium text-gray-500 truncate">Planes de
                                                Entrenamiento
                                            </dt>
                                            <dd class="mt-1 text-4xl font-semibold text-indigo-600">50+</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-indigo-50 px-6 py-4">
                                <div class="text-sm">
                                    <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Ver todos los
                                        planes <span aria-hidden="true">&rarr;</span></a>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white overflow-hidden shadow-lg rounded-2xl transition duration-300 ease-in-out transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-green-500 rounded-full p-4">
                                        <i class="fas fa-utensils text-2xl text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-lg font-medium text-gray-500 truncate">Guías de Nutrición
                                            </dt>
                                            <dd class="mt-1 text-4xl font-semibold text-green-600">100+</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 px-6 py-4">
                                <div class="text-sm">
                                    <a href="login.php" class="font-medium text-green-600 hover:text-green-500">Explorar guías
                                        <span aria-hidden="true">&rarr;</span></a>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-white overflow-hidden shadow-lg rounded-2xl transition duration-300 ease-in-out transform hover:scale-105">
                            <div class="p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-purple-500 rounded-full p-4">
                                        <i class="fas fa-users text-2xl text-white"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-lg font-medium text-gray-500 truncate">Comunidad Activa</dt>
                                            <dd class="mt-1 text-4xl font-semibold text-purple-600">10k+</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 px-6 py-4">
                                <div class="text-sm">
                                    <a href="login.php" class="font-medium text-purple-600 hover:text-purple-500">Unirse a la
                                        comunidad <span aria-hidden="true">&rarr;</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </main>

        <!-- Pie de página -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Columna 1: Información de FitLife -->
                    <div class="space-y-4">
                        <h2 class="text-2xl font-bold text-indigo-400">FitLife</h2>
                        <p class="text-gray-300">Tu compañero de fitness para una vida más saludable y activa.</p>
                    </div>

                    <!-- Columna 2: Enlaces Rápidos -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-indigo-300">Enlaces Rápidos</h3>
                        <ul class="space-y-2">
                            <li><a href="login.php" class="text-gray-300 hover:text-white transition duration-300">Inicio</a>
                            </li>
                            <li><a href="login.php"
                                    class="text-gray-300 hover:text-white transition duration-300">Planes de
                                    Entrenamiento</a></li>
                            <li><a href="login.php" class="text-gray-300 hover:text-white transition duration-300">Guías
                                    de Nutrición</a></li>
                            <li><a href="login.php"
                                    class="text-gray-300 hover:text-white transition duration-300">Comunidad</a></li>
                        </ul>
                    </div>

                    <!-- Columna 3: Soporte -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-indigo-300">Soporte</h3>
                        <ul class="space-y-2">
                            <li><a href="FAQ.html"
                                    class="text-gray-300 hover:text-white transition duration-300">FAQ</a></li>
                            <li><a href="terminos_condiciones.html"
                                    class="text-gray-300 hover:text-white transition duration-300">Política de
                                    Privacidad</a></li>
                            <li><a href="terminos_condiciones.html"
                                    class="text-gray-300 hover:text-white transition duration-300">Términos de
                                    Servicio</a></li>
                        </ul>
                    </div>

                    <!-- Columna 4: Contáctanos -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-indigo-300">Contáctanos</h3>
                        <ul class="space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-envelope text-indigo-400 mr-2"></i>
                                <a href="mailto:info@fitlife.com"
                                    class="text-gray-300 hover:text-white transition duration-300">fitlife.soporte.022@gmail.com</a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-phone text-indigo-400 mr-2"></i>
                                <a href="tel:+34912345678"
                                    class="text-gray-300 hover:text-white transition duration-300">+34 601009110</a>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-map-marker-alt text-indigo-400 mr-2"></i>
                                <span class="text-gray-300">España - Madrid</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Copyright -->
                <div class="mt-8 pt-8 border-t border-gray-700">
                    <p class="text-center text-gray-400">&copy; <span id="current-year"></span> FitLife. Todos los
                        derechos reservados.</p>
                </div>
            </div>
        </footer>

        <script>
            // Set the current year for the copyright notice
            document.getElementById('current-year').textContent = new Date().getFullYear();
        </script>
    </div>
</body>

</html>