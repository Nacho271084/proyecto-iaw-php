<?php
// public/items_form.php
require_once '../app/auth.php';
require_once '../app/pdo.php';
require_once '../app/utils.php';
require_once '../app/csrf.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$esEdicion = ($id > 0);

$errores = [];
$titulo = '';
$descripcion = '';
$prioridad = 'media';
$estado = 'abierta';

// Si es edición, cargamos los datos actuales
if ($esEdicion) {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        die("Ticket no encontrado.");
    }
    
    $titulo = $ticket->titulo;
    $descripcion = $ticket->descripcion;
    $prioridad = $ticket->prioridad;
    $estado = $ticket->estado;
}

// Procesar el formulario al enviar (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $estado = $_POST['estado'] ?? 'abierta';
    
    // Validaciones
    if ($titulo === '') {
        $errores[] = "El título es obligatorio.";
    }
    if ($descripcion === '') {
        $errores[] = "La descripción es obligatoria.";
    }
    
    // Si no hay errores, guardamos en BD
    if (empty($errores)) {
        if ($esEdicion) {
            $sql = "UPDATE tickets SET titulo = :titulo, descripcion = :descripcion, prioridad = :prioridad, estado = :estado WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'prioridad' => $prioridad,
                'estado' => $estado,
                'id' => $id
            ]);
        } else {
            $sql = "INSERT INTO tickets (titulo, descripcion, prioridad, estado, usuario_id) VALUES (:titulo, :descripcion, :prioridad, :estado, :usuario_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'prioridad' => $prioridad,
                'estado' => $estado,
                'usuario_id' => $_SESSION['usuario_id']
            ]);
        }
        
        // Redirigir (PRG) para evitar reenvíos
        header('Location: items_list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $esEdicion ? 'Editar' : 'Nuevo'; ?> Ticket</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; color: white; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; }
        .btn-secondary { background: #6c757d; }
        .alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert ul { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $esEdicion ? 'Editar Ticket' : 'Nuevo Ticket'; ?></h2>
        
        <?php if (!empty($errores)): ?>
            <div class="alert">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="items_form.php<?php echo $esEdicion ? '?id=' . $id : ''; ?>">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?php echo e($titulo); ?>" required>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required><?php echo e($descripcion); ?></textarea>
            </div>

            <div class="form-group">
                <label for="prioridad">Prioridad:</label>
                <select id="prioridad" name="prioridad">
                    <option value="baja" <?php echo $prioridad === 'baja' ? 'selected' : ''; ?>>Baja</option>
                    <option value="media" <?php echo $prioridad === 'media' ? 'selected' : ''; ?>>Media</option>
                    <option value="alta" <?php echo $prioridad === 'alta' ? 'selected' : ''; ?>>Alta</option>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="abierta" <?php echo $estado === 'abierta' ? 'selected' : ''; ?>>Abierta</option>
                    <option value="cerrada" <?php echo $estado === 'cerrada' ? 'selected' : ''; ?>>Cerrada</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $esEdicion ? 'Actualizar' : 'Guardar'; ?></button>
            <a href="items_list.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>