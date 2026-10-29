<?php
// public/preferencias.php
require_once '../app/auth.php';
require_once '../app/utils.php';

require_login();

// Si el usuario envía el formulario para cambiar el tema
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoTema = $_POST['tema'] ?? 'claro';
    
    // Validamos que sea un valor permitido
    if (in_array($nuevoTema, ['claro', 'oscuro'])) {
        // Guardamos la cookie por 30 días (time() + 60*60*24*30)
        setcookie('tema', $nuevoTema, time() + (86400 * 30), "/");
        // Actualizamos la variable global para que se aplique ya mismo sin recargar
        $_COOKIE['tema'] = $nuevoTema;
    }
    
    // Redirigimos para evitar reenvíos (PRG)
    header('Location: preferencias.php');
    exit;
}

// Leemos la cookie actual (por defecto 'claro')
$temaActual = $_COOKIE['tema'] ?? 'claro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Preferencias</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        /* Aquí aplicamos el CSS dependiendo de la cookie */
        body.oscuro { background: #121212; color: #ffffff; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); color: #333; }
        body.oscuro .container { background: #1e1e1e; color: #eee; box-shadow: 0 2px 5px rgba(255,255,255,0.1); }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; background: #007bff; color: white; cursor: pointer; text-decoration: none; display: inline-block;}
    </style>
</head>
<!-- Aplicamos la clase 'oscuro' al body si la cookie lo indica -->
<body class="<?php echo $temaActual === 'oscuro' ? 'oscuro' : ''; ?>">
    <div class="container">
        <h2>Preferencias de Apariencia</h2>
        
        <form method="POST" action="preferencias.php">
            <div style="margin-bottom: 20px;">
                <label>
                    <input type="radio" name="tema" value="claro" <?php echo $temaActual === 'claro' ? 'checked' : ''; ?>>
                    Tema Claro (Por defecto)
                </label><br><br>
                <label>
                    <input type="radio" name="tema" value="oscuro" <?php echo $temaActual === 'oscuro' ? 'checked' : ''; ?>>
                    Tema Oscuro
                </label>
            </div>
            
            <button type="submit" class="btn">Guardar Preferencia</button>
            <a href="items_list.php" class="btn" style="background: #6c757d; text-decoration: none;">Volver al Listado</a>
        </form>
    </div>
</body>
</html>