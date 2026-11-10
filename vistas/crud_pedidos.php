<?php
require_once __DIR__ . '/../controler/pedidos_por_clientesctrl.php';

function escaparPedido($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlPedidos = new PedidosPorClientesCtrl();
$mensaje = '';
$tipoMensaje = 'success';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $id = $_POST['eliminar_id'] ?? '';
    if (is_string($id) && ctype_digit($id) && (int) $id > 0) {
        if ($ctrlPedidos->eliminar((int) $id)) {
            $mensaje = 'El pedido se eliminó correctamente.';
        } else {
            $mensaje = 'No se pudo eliminar el pedido.';
            $tipoMensaje = 'danger';
        }
    } else {
        $mensaje = 'El identificador del pedido no es válido.';
        $tipoMensaje = 'danger';
    }
}
$pedidos = $ctrlPedidos->listar();
if ($mensaje === '') {
    if (isset($_GET['registrado'])) {
        $mensaje = 'El pedido se registró correctamente.';
    } elseif (isset($_GET['actualizado'])) {
        $mensaje = 'El pedido se actualizó correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos por clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11 border p-4 mt-5 mb-5 rounded-2 shadow-lg">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div>
                    <h1 class="h2 mb-1">Pedidos por clientes</h1>
                    <p class="text-body-secondary mb-0">Administra los libros pedidos por cada cliente.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="form_pedido.php" class="btn btn-primary">Registrar pedido</a>
                    <a href="crud_c.php" class="btn btn-outline-secondary">Volver a clientes</a>
                </div>
            </div>
            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= escaparPedido($tipoMensaje) ?>" role="status"><?= escaparPedido($mensaje) ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th><th scope="col">N.º pedido</th>
                            <th scope="col">Cliente</th><th scope="col">Libro</th>
                            <th scope="col">Fecha</th><th scope="col">Cantidad</th>
                            <th scope="col">Subtotal</th><th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$pedidos): ?>
                        <tr><td colspan="8" class="text-center text-body-secondary py-4">No hay pedidos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= escaparPedido($pedido['id_pedido']) ?></td>
                                <td><?= escaparPedido($pedido['nro_pedido']) ?></td>
                                <td><?= escaparPedido($pedido['nombre_cliente'] . ' ' . $pedido['apellidos_cliente']) ?></td>
                                <td><?= escaparPedido($pedido['titulo']) ?></td>
                                <td><?= escaparPedido($pedido['fecha_pedido']) ?></td>
                                <td><?= escaparPedido($pedido['cantidad']) ?></td>
                                <td><?= escaparPedido(number_format((float) $pedido['subtotal'], 0, ',', '.')) ?></td>
                                <td class="text-center text-nowrap">
                                    <a href="form_pedido.php?id=<?= rawurlencode((string) $pedido['id_pedido']) ?>"
                                       class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este pedido?');">
                                        <input type="hidden" name="eliminar_id" value="<?= escaparPedido($pedido['id_pedido']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</body>
</html>
