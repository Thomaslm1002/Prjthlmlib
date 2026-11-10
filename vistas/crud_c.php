<?php
require_once __DIR__ . '/../model/clientes.php';
require_once __DIR__ . '/../controler/clientesctrl.php';

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlClientes = new ClientesCtrl();
$clientes = $ctrlClientes->listar();
$mensaje = '';

if (isset($_GET['registrado'])) {
    $mensaje = 'El cliente se registró correctamente.';
} elseif (isset($_GET['actualizado'])) {
    $mensaje = 'Los datos del cliente se actualizaron correctamente.';
} elseif (isset($_GET['eliminado'])) {
    $mensaje = 'El cliente se eliminó correctamente.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 border p-4 mt-5 mb-5 rounded-2 shadow-lg">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div>
                    <h1 class="h2 mb-1">Clientes</h1>
                    <p class="text-body-secondary mb-0">Consulta y administra los clientes registrados.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="insertar_c.php" class="btn btn-primary">Registrar cliente</a>
                    <a href="crud_pedidos.php" class="btn btn-outline-secondary">Pedidos</a>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-success" role="status"><?= escapar($mensaje) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Identificación</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Celular</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Correo</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$clientes): ?>
                        <tr>
                            <td colspan="7" class="text-center text-body-secondary py-4">
                                Aún no hay clientes registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?= escapar($cliente->getIdCliente()) ?></td>
                                <td><?= escapar($cliente->getIdentificacion()) ?></td>
                                <td><?= escapar($cliente->getNombre() . ' ' . $cliente->getApellido()) ?></td>
                                <td><?= escapar($cliente->getCelular()) ?></td>
                                <td><?= escapar($cliente->getDireccion()) ?></td>
                                <td><?= escapar($cliente->getCorreo()) ?></td>
                                <td class="text-center text-nowrap">
                                    <a href="editar_c.php?id=<?= rawurlencode((string) $cliente->getIdCliente()) ?>"
                                       class="btn btn-sm btn-outline-primary">Editar</a>
                                    <a href="eliminar_c.php?id=<?= rawurlencode((string) $cliente->getIdCliente()) ?>"
                                       class="btn btn-sm btn-outline-danger">Eliminar</a>
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
