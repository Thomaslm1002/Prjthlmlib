<?php
require_once __DIR__ . '/../controler/clientesctrl.php';

function escaparConfirmacionCliente($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$idCliente = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$mensajeError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $idCliente = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($idCliente && (new Clientesctrl())->eliminar($idCliente) > 0) {
        header('Location: crud_c.php?eliminado=1');
        exit;
    }
    $mensajeError = 'No se pudo eliminar el cliente. Comprueba que todavía exista.';
}

if (!$idCliente || $idCliente < 1) {
    header('Location: 404.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <section class="col-12 col-md-8 col-lg-6 border p-4 mt-5 mb-5 rounded-2 shadow-lg" aria-labelledby="delete-title">
            <p class="text-uppercase small fw-bold text-danger mb-2">Acción irreversible</p>
            <h1 id="delete-title" class="h2 mb-3">Eliminar cliente</h1>
            <p class="text-body-secondary">¿Confirmas que quieres eliminar el cliente <strong>#<?= escaparConfirmacionCliente($idCliente) ?></strong>?</p>
            <?php if ($mensajeError !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= escaparConfirmacionCliente($mensajeError) ?></div>
            <?php endif; ?>
            <form action="<?= escaparConfirmacionCliente($_SERVER['PHP_SELF']) ?>?id=<?= escaparConfirmacionCliente($idCliente) ?>" method="post" class="d-flex flex-wrap gap-2">
                <input type="hidden" value="<?= escaparConfirmacionCliente($idCliente) ?>" name="id">
                <button type="submit" class="btn btn-outline-danger">Sí, eliminar</button>
                <a class="btn btn-outline-secondary" href="crud_c.php">Cancelar</a>
            </form>
        </section>
    </div>
</main>
</body>
</html>