<?php
// public/items_show.php
require_once '../app/auth.php';
require_once '../app/pdo.php';
require_once '../app/utils.php';

require_login();

// Recogemos el ID de la URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: items_list.php');
    exit;
}

// Buscamos el ticket en la base de datos (junto con el nombre del usuario)
$sql = "SELECT t.*, u.nombre AS usuario_nombre 
        FROM tickets t 
        JOIN usuarios u ON t.usuario_id = u.id 
        WHERE t.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$ticket = $stmt->fetch();

// Si el ticket no existe, mostramos un error
if (!$ticket) {
    die("<h1>Error 404</h1><p>El ticket solicitado no existe.</p><a href='items_list.php'>Volver al listado</a>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Ticket #<?php echo e($ticket->id); ?></title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; display: inline-block; }
        .btn-secondary { background: #6c757d; }
        .btn-primary { background: #007bff; }
        .badge { padding: 6px 12px; border-radius: 12px; font-size: 14px; font-weight: bold; }
        .bg-alta { background: #f8d7da; color: #721c24; }
        .bg-media { background: #fff3cd; color: #856404; }
        .bg-baja { background: #d4edda; color: #155724; }
        .bg-abierta { background: #cce5ff; color: #004085; }
        .bg-cerrada { background: #e2e3e5; color: #383d41; }
        .detail-box { background: #f9f9f9; padding: 20px; border-radius: 8px; border-left: 5px solid #007bff; margin-bottom: 20px; }
        .detail-desc { font-size: 16px; line-height: 1.6; white-space: pre-wrap; }
        .meta-info { color: #666; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Detalle del Ticket #<?php echo e($ticket->id); ?></h2>
            <a href="items_list.php" class="btn btn-secondary">&laquo; Volver al listado</a>
        </div>

        <div style="margin-bottom: 20px;">
            <h1 style="margin-top: 0;"><?php echo e($ticket->titulo); ?></h1>
            <p>
                <span class="badge bg-<?php echo e($ticket->prioridad); ?>">Prioridad: <?php echo e(ucfirst($ticket->prioridad)); ?></span>
                <span class="badge bg-<?php echo e($ticket->estado); ?>">Estado: <?php echo e(ucfirst($ticket->estado)); ?></span>
            </p>
        </div>

        <div class="detail-box">
            <h3 style="margin-top: 0;">Descripción:</h3>
            <p class="detail-desc"><?php echo e($ticket->descripcion); ?></p>
        </div>

        <div class="meta-info">
            <p><strong>Creado por:</strong> <?php echo e($ticket->usuario_nombre); ?></p>
            <p><strong>Fecha de creación:</strong> <?php echo e(date('d/m/Y H:i', strtotime($ticket->created_at))); ?></p>
            <?php if ($ticket->created_at !== $ticket->updated_at): ?>
                <p><strong>Última modificación:</strong> <?php echo e(date('d/m/Y H:i', strtotime($ticket->updated_at))); ?></p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 30px; text-align: right;">
            <a href="items_form.php?id=<?php echo e($ticket->id); ?>" class="btn btn-primary">Editar este ticket</a>
        </div>
    </div>
</body>
</html>