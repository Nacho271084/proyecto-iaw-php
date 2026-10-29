<?php
// public/items_list.php
require_once '../app/auth.php';
require_once '../app/pdo.php';
require_once '../app/utils.php';

// Bloqueamos el acceso si no hay sesión
require_login();

// 1. Recogemos el término de búsqueda (si existe)
$textoBuscar = trim($_GET['buscar'] ?? '');

// 2. Recogemos la página actual (por defecto 1) y validamos
$paginaActual = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 
    ? (int)$_GET['page'] 
    : 1;

$porPagina = 5; // Mostramos 5 tickets por página
$offset = ($paginaActual - 1) * $porPagina;

// 3. Preparamos la consulta base para el conteo total
$sqlTotal = "SELECT COUNT(*) FROM tickets WHERE titulo LIKE :buscar";
$stmtTotal = $pdo->prepare($sqlTotal);
$parametroBuscar = "%" . $textoBuscar . "%";
$stmtTotal->execute(['buscar' => $parametroBuscar]);
$totalRegistros = $stmtTotal->fetchColumn();

// Calculamos el número total de páginas
$totalPaginas = ceil($totalRegistros / $porPagina);

// 4. Consulta principal con LIMIT y OFFSET (SQL Preparado)
$sqlListado = "SELECT t.id, t.titulo, t.prioridad, t.estado, t.created_at, u.nombre AS usuario 
               FROM tickets t 
               JOIN usuarios u ON t.usuario_id = u.id 
               WHERE t.titulo LIKE :buscar 
               ORDER BY t.id DESC 
               LIMIT :limit OFFSET :offset";

$stmtListado = $pdo->prepare($sqlListado);

// ¡OJO! LIMIT y OFFSET deben ser enteros obligatoriamente
$stmtListado->bindValue(':buscar', $parametroBuscar, PDO::PARAM_STR);
$stmtListado->bindValue(':limit', $porPagina, PDO::PARAM_INT);
$stmtListado->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmtListado->execute();

$tickets = $stmtListado->fetchAll();

// Leemos la cookie del tema (por defecto 'claro')
$temaActual = $_COOKIE['tema'] ?? 'claro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Tickets</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .btn { padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; display: inline-block; }
        .btn-primary { background: #007bff; }
        .btn-danger { background: #dc3545; }
        .btn-secondary { background: #6c757d; }
        .btn-sm { padding: 4px 8px; font-size: 12px; }
        .search-form { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-form input { padding: 8px; border: 1px solid #ccc; border-radius: 4px; flex-grow: 1; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .bg-alta { background: #f8d7da; color: #721c24; }
        .bg-media { background: #fff3cd; color: #856404; }
        .bg-baja { background: #d4edda; color: #155724; }
        .bg-abierta { background: #cce5ff; color: #004085; }
        .bg-cerrada { background: #e2e3e5; color: #383d41; }
        .pagination { display: flex; gap: 10px; justify-content: center; align-items: center; }
        .pagination a { padding: 6px 12px; text-decoration: none; background: #eee; border-radius: 4px; color: #333; }
        .pagination a:hover { background: #ddd; }

        /* ESTILOS PARA EL TEMA OSCURO */
        body.oscuro { background: #121212; color: #fff; }
        body.oscuro .container { background: #1e1e1e; color: #eee; }
        body.oscuro .header { border-bottom: 2px solid #333; }
        body.oscuro table th { background: #333; color: #fff;}
        body.oscuro table td { border-bottom: 1px solid #444; }
        body.oscuro .pagination a { background: #333; color: #fff; }
    </style>
</head>
<!-- AQUÍ APLICAMOS LA CLASE OSCURO SI LA COOKIE ESTÁ ACTIVA -->
<body class="<?php echo $temaActual === 'oscuro' ? 'oscuro' : ''; ?>">
    <div class="container">
        <div class="header">
            <h2>Listado de Incidencias</h2>
            <div>
                <a href="preferencias.php" class="btn btn-secondary btn-sm">Preferencias</a>
                <a href="items_form.php" class="btn btn-primary btn-sm">+ Nuevo Ticket</a>
                <strong><?php echo e(current_user()); ?></strong> | 
                <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Formulario de Búsqueda -->
        <form method="GET" action="items_list.php" class="search-form">
            <input type="text" name="buscar" placeholder="Buscar por título..." value="<?php echo e($textoBuscar); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <?php if ($textoBuscar !== ''): ?>
                <a href="items_list.php" class="btn btn-danger" style="text-decoration:none;">Limpiar</a>
            <?php endif; ?>
        </form>

        <!-- Tabla de Datos -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($tickets) > 0): ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><?php echo e($t->id); ?></td>
                            <td><?php echo e($t->titulo); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($t->prioridad); ?>">
                                    <?php echo e(ucfirst($t->prioridad)); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($t->estado); ?>">
                                    <?php echo e(ucfirst($t->estado)); ?>
                                </span>
                            </td>
                            <td><?php echo $t->created_at ? e(date('d/m/Y', strtotime($t->created_at))) : 'Sin fecha'; ?></td>
                           <td>
                                <a href="items_form.php?id=<?php echo $t->id; ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="items_delete.php?id=<?php echo $t->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que quieres ir a la pantalla de borrado?');">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 20px;">No se encontraron tickets.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <?php if ($totalPaginas > 1): ?>
            <div class="pagination">
                <?php if ($paginaActual > 1): ?>
                    <a href="items_list.php?page=<?php echo $paginaActual - 1; ?>&buscar=<?php echo urlencode($textoBuscar); ?>">« Anterior</a>
                <?php endif; ?>

                <span>Página <strong><?php echo $paginaActual; ?></strong> de <strong><?php echo $totalPaginas; ?></strong></span>

                <?php if ($paginaActual < $totalPaginas): ?>
                    <a href="items_list.php?page=<?php echo $paginaActual + 1; ?>&buscar=<?php echo urlencode($textoBuscar); ?>">Siguiente »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>