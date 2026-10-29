<?php
// public/items_delete.php
require_once '../app/auth.php';
require_once '../app/pdo.php';
require_once '../app/utils.php';
require_once '../app/csrf.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: items_list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare("SELECT titulo FROM tickets WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        die("Ticket no encontrado.");
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Confirmar Borrado</title>
        <style>
            body { font-family: sans-serif; background: #f4f7f6; padding: 40px; text-align: center; }
            .box { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
            .btn { padding: 10px 20px; border: none; border-radius: 4px; color: white; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
            .btn-danger { background: #dc3545; }
            .btn-secondary { background: #6c757d; }
            .actions { margin-top: 20px; display: flex; gap: 10px; justify-content: center; }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>¿Estás seguro de que quieres borrar este ticket?</h2>
            <p><strong><?php echo e($ticket->titulo); ?></strong></p>
            
            <form method="POST" action="items_delete.php?id=<?php echo $id; ?>">
                <?php echo csrf_field(); ?>
                
                <!-- CASILLA DE SIMULACIÓN DE FALLO -->
                <div style="margin-bottom: 20px; background: #fff3cd; padding: 15px; border-radius: 4px; text-align: left; color: #856404; border: 1px solid #ffeeba;">
                    <label style="cursor: pointer; display: block;">
                        <input type="checkbox" name="simular_fallo" value="1" style="margin-right: 8px;">
                        <strong>Simular fallo técnico (Prueba de Rollback)</strong><br>
                        <small style="margin-left: 24px; display: block; margin-top: 5px;">Si marcas esto, el borrado fallará a propósito para demostrar que la base de datos no se corrompe.</small>
                    </label>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-danger">Sí, borrar definitivamente</button>
                    <a href="items_list.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

check_csrf();

$simularFallo = isset($_POST['simular_fallo']) && $_POST['simular_fallo'] == '1';

try {
    $pdo->beginTransaction();

    $stmt1 = $pdo->prepare("DELETE FROM tickets WHERE id = :id");
    $stmt1->execute(['id' => $id]);

    if ($simularFallo) {
        throw new Exception("Fallo simulado intencionadamente entre el borrado y la auditoría.");
    }

    $stmt2 = $pdo->prepare("INSERT INTO auditoria (accion, tabla_afectada, registro_id, usuario_id) VALUES ('BORRADO', 'tickets', :id, :usuario_id)");
    $stmt2->execute([
        'id' => $id,
        'usuario_id' => $_SESSION['usuario_id']
    ]);

    $pdo->commit();

    $_SESSION['mensaje_exito'] = "Ticket borrado y auditado correctamente.";
    header('Location: items_list.php');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    
    die("<h1 style='font-family: sans-serif; text-align: center; margin-top: 50px;'>Error detectado</h1><p style='font-family: sans-serif; text-align: center;'>Se han deshecho los cambios. Motivo: " . e($e->getMessage()) . "</p><p style='font-family: sans-serif; text-align: center;'><a href='items_list.php'>Volver al listado</a></p>");
}