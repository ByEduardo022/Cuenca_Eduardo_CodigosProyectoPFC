<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - FitLife</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="h-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600">
    <!-- Botón de volver -->
    <a href="index.php"
        class="absolute top-4 left-4 flex items-center text-sm font-medium text-gray-800 bg-gradient-to-r from-gray-200 to-gray-300 px-4 py-2 rounded-full shadow-md hover:from-gray-300 hover:to-gray-400 focus:ring-2 focus:ring-gray-500 focus:outline-none transition duration-300">
        <!-- Icono opcional -->
        <svg class="w-5 h-5 mr-2 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>

    </a>
    <div class="w-full max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="md:flex">
                <div class="md:w-1/2 bg-cover bg-center"
                    style="background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
                </div>
                <div class="md:w-1/2 p-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Bienvenido de nuevo a FitLife</h2>
                    <p class="text-gray-600 mb-8">Inicia sesión en tu cuenta para continuar tu viaje fitness</p>

                    <form action="iniciar_sesion.php" method="POST" class="space-y-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Usuario</label>
                            <input type="text" id="username" name="username" required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Introduce tu Usuario">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input type="password" id="password" name="password" required
                                class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Introduce tu Contraseña">
                        </div>
                        <div>
                            <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Iniciar Sesión
                            </button>
                        </div>
                    </form>
                    <p class="mt-6 text-center text-sm text-gray-500">
                        ¿No tienes una cuenta?
                        <a href="register.php" class="font-medium text-purple-600 hover:text-purple-500">
                            Regístrate
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>