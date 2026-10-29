<?php
// public/index.php
require_once '../app/auth.php';
require_once '../app/utils.php';

require_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal</title>
    <style>
        body { font-family: sans-serif; padding: 40px; text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>¡Bienvenido al Gestor de Incidencias!</h1>
    <p>Hola, <strong><?php echo e(current_user()); ?></strong>. Has iniciado sesión correctamente.</p>
    
    <a href="items_list.php" class="btn">Ver Listado de Incidencias</a>
    
    <p style="margin-top: 40px;"><a href="logout.php">Cerrar Sesión</a></p>
</body>
</html>