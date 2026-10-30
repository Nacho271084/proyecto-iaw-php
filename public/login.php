<?php
// public/login.php
require_once '../app/pdo.php';
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/csrf.php';

// Si ya está logueado, lo mandamos al panel principal (index.php)
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

// Comprobamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Validar CSRF (seguridad)
    check_csrf();

    // 2. Recoger datos
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 3. Validar que no estén vacíos
    if ($email === '' || $password === '') {
        $error = 'Por favor, rellena todos los campos.';
    } else {
        // 4. Buscar al usuario en la base de datos
        $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();

        // 5. Verificar contraseña (USANDO OBJETOS)
        if ($usuario && password_verify($password, $usuario->password)) {
            // Login correcto: guardamos datos en sesión
            $_SESSION['usuario_id'] = $usuario->id;
            $_SESSION['usuario_nombre'] = $usuario->nombre;
            $_SESSION['usuario_rol'] = $usuario->rol;

            // Redirigimos al panel
            header('Location: index.php');
            exit;
        } else {
            // Error genérico (no decimos si falló el email o la contraseña por seguridad)
            $error = 'Correo o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Gestor de Incidencias</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; }
        .login-box h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .error { color: red; margin-bottom: 15px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Acceso al Sistema</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <!-- Token de seguridad CSRF -->
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
</body>
</html>