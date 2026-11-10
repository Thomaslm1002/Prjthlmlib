<?php
require_once __DIR__ . '/../model/pedidos_por_clientes.php';
require_once __DIR__ . '/../controler/pedidos_por_clientesctrl.php';

function escaparPedidoForm($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlPedidos = new PedidosPorClientesCtrl();
$idParam = $_POST['id_original'] ?? $_GET['id'] ?? '';
$editar = $idParam !== '';
if ($editar && (!is_string($idParam) || !ctype_digit($idParam) || (int) $idParam < 1)) {
    header('Location: crud_pedidos.php');
    exit;
}
$original = $editar ? $ctrlPedidos->obtenerPorId((int) $idParam) : null;
if ($editar && !$original) {
    header('Location: crud_pedidos.php?no_encontrado=1');
    exit;
}
$clientes = $ctrlPedidos->listarClientes();
$libros = $ctrlPedidos->listarLibros();
$datos = [
    'nro_pedido' => $original ? $original->getNroPedido() : '',
    'id_cliente' => $original ? $original->getIdCliente() : '',
    'isbn' => $original ? $original->getIsbn() : '',
    'fecha_pedido' => $original ? date('Y-m-d\TH:i', strtotime($original->getFechaPedido())) : date('Y-m-d\TH:i'),
    'cantidad' => $original ? $original->getCantidad() : '1',
    'subtotal' => $original ? $original->getSubtotal() : ''
];
$errores = [];
$mensajeError = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach (array_keys($datos) as $campo) {
        $valor = $_POST[$campo] ?? '';
        $datos[$campo] = is_string($valor) ? trim($valor) : '';
    }
    $idsClientes = array_map('strval', array_column($clientes, 'id_cliente'));
    $isbnsLibros = array_map('strval', array_column($libros, 'isbn'));
    $numeroPedido = filter_var($datos['nro_pedido'], FILTER_VALIDATE_INT);
    $cantidad = filter_var($datos['cantidad'], FILTER_VALIDATE_INT);
    $subtotal = filter_var($datos['subtotal'], FILTER_VALIDATE_INT);
    $fecha = DateTime::createFromFormat('Y-m-d\TH:i', $datos['fecha_pedido']);
    if ($numeroPedido === false || $numeroPedido < 1) {
        $errores['nro_pedido'] = 'Ingresa un número de pedido válido.';
    }
    if (!in_array($datos['id_cliente'], $idsClientes, true)) {
        $errores['id_cliente'] = 'Selecciona un cliente válido.';
    }
    if (!in_array($datos['isbn'], $isbnsLibros, true)) {
        $errores['isbn'] = 'Selecciona un libro válido.';
    }
    if (!$fecha || $fecha->format('Y-m-d\TH:i') !== $datos['fecha_pedido']) {
        $errores['fecha_pedido'] = 'Ingresa una fecha y hora válidas.';
    }
    if ($cantidad === false || $cantidad < 1) {
        $errores['cantidad'] = 'La cantidad debe ser un entero mayor que cero.';
    }
    if ($subtotal === false || $subtotal < 0) {
        $errores['subtotal'] = 'El subtotal debe ser un entero igual o mayor que cero.';
    }
    if (!$errores) {
        $pedido = new Pedidos_por_clientes(
            $editar ? (int) $idParam : null,
            $numeroPedido,
            (int) $datos['id_cliente'],
            (int) $datos['isbn'],
            $fecha->format('Y-m-d H:i:s'),
            $cantidad,
            $subtotal
        );
        if ($editar) {
            $pedido->setIdPedido((int) $idParam);
        }
        $guardado = $editar ? $ctrlPedidos->actualizar($pedido) : $ctrlPedidos->insertar($pedido);
        if ($guardado) {
            header('Location: crud_pedidos.php?' . ($editar ? 'actualizado=1' : 'registrado=1'));
            exit;
        }
        $mensajeError = 'No se pudo guardar el pedido. Revisa los datos e inténtalo de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editar ? 'Editar pedido' : 'Registrar pedido' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>.error { color: rgb(128, 2, 2); }</style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 border p-4 mt-5 rounded-2 shadow-lg">
            <h1 class="h2"><?= $editar ? 'Editar pedido' : 'Registrar pedido' ?></h1>
            <?php if ($mensajeError !== ''): ?><div class="alert alert-danger" role="alert"><?= escaparPedidoForm($mensajeError) ?></div><?php endif; ?>
            <?php if (!$clientes || !$libros): ?>
                <div class="alert alert-warning" role="alert">Registra al menos un cliente y un libro antes de crear un pedido.</div>
            <?php endif; ?>
            <form method="post" action="<?= escaparPedidoForm($_SERVER['PHP_SELF']) ?>">
                <?php if ($editar): ?><input type="hidden" name="id_original" value="<?= escaparPedidoForm($idParam) ?>"><?php endif; ?>
                <div class="form-floating mb-2">
                    <input type="number" class="form-control" id="nro_pedido" name="nro_pedido" min="1" step="1"
                           value="<?= escaparPedidoForm($datos['nro_pedido']) ?>" placeholder="Número de pedido" required>
                    <label for="nro_pedido">Número de pedido</label>
                    <?php if (isset($errores['nro_pedido'])): ?><span class="error"><?= escaparPedidoForm($errores['nro_pedido']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <select class="form-select" id="id_cliente" name="id_cliente" required>
                        <option value="">Selecciona un cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= escaparPedidoForm($cliente['id_cliente']) ?>"
                                <?= (string) $datos['id_cliente'] === (string) $cliente['id_cliente'] ? 'selected' : '' ?>>
                                <?= escaparPedidoForm($cliente['nombre'] . ' ' . $cliente['apellidos']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="id_cliente">Cliente</label>
                    <?php if (isset($errores['id_cliente'])): ?><span class="error"><?= escaparPedidoForm($errores['id_cliente']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <select class="form-select" id="isbn" name="isbn" required>
                        <option value="">Selecciona un libro</option>
                        <?php foreach ($libros as $libro): ?>
                            <option value="<?= escaparPedidoForm($libro['isbn']) ?>"
                                <?= (string) $datos['isbn'] === (string) $libro['isbn'] ? 'selected' : '' ?>>
                                <?= escaparPedidoForm($libro['titulo'] . ' (ISBN ' . $libro['isbn'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="isbn">Libro</label>
                    <?php if (isset($errores['isbn'])): ?><span class="error"><?= escaparPedidoForm($errores['isbn']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="datetime-local" class="form-control" id="fecha_pedido" name="fecha_pedido"
                           value="<?= escaparPedidoForm($datos['fecha_pedido']) ?>" required>
                    <label for="fecha_pedido">Fecha y hora del pedido</label>
                    <?php if (isset($errores['fecha_pedido'])): ?><span class="error"><?= escaparPedidoForm($errores['fecha_pedido']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" step="1"
                           value="<?= escaparPedidoForm($datos['cantidad']) ?>" placeholder="Cantidad" required>
                    <label for="cantidad">Cantidad</label>
                    <?php if (isset($errores['cantidad'])): ?><span class="error"><?= escaparPedidoForm($errores['cantidad']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="subtotal" name="subtotal" min="0" step="1"
                           value="<?= escaparPedidoForm($datos['subtotal']) ?>" placeholder="Subtotal" required>
                    <label for="subtotal">Subtotal</label>
                    <?php if (isset($errores['subtotal'])): ?><span class="error"><?= escaparPedidoForm($errores['subtotal']) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100" <?= (!$clientes || !$libros) ? 'disabled' : '' ?>>
                    <?= $editar ? 'Guardar cambios' : 'Registrar pedido' ?>
                </button>
                <a href="crud_pedidos.php" class="btn btn-link w-100 mt-2">Volver a pedidos</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
