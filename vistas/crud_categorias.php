<?php
require_once __DIR__ . '/../controler/categoriasctrl.php';

function escaparCategoria($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlCategorias = new CategoriasCtrl();
$mensaje = '';
$tipoMensaje = 'success';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $id = $_POST['eliminar_id'] ?? '';
    if (is_string($id) && ctype_digit($id) && (int) $id > 0) {
        if ($ctrlCategorias->eliminar((int) $id)) {
            $mensaje = 'La categoría se eliminó correctamente.';
        } else {
            $mensaje = 'No se pudo eliminar la categoría. Verifica que no tenga libros asociados.';
            $tipoMensaje = 'danger';
        }
    } else {
        $mensaje = 'El identificador de categoría no es válido.';
        $tipoMensaje = 'danger';
    }
}
$categorias = $ctrlCategorias->listar();
if ($mensaje === '') {
    if (isset($_GET['registrado'])) {
        $mensaje = 'La categoría se registró correctamente.';
    } elseif (isset($_GET['actualizado'])) {
        $mensaje = 'La categoría se actualizó correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías</title>
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
                    <h1 class="h2 mb-1">Categorías</h1>
                    <p class="text-body-secondary mb-0">Administra las categorías del catálogo.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="form_categoria.php" class="btn btn-primary">Registrar categoría</a>
                    <a href="crud_libros.php" class="btn btn-outline-secondary">Volver a libros</a>
                </div>
            </div>
            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= escaparCategoria($tipoMensaje) ?>" role="status"><?= escaparCategoria($mensaje) ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr><th scope="col">ID</th><th scope="col">Categoría</th><th scope="col" class="text-center">Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!$categorias): ?>
                        <tr><td colspan="3" class="text-center text-body-secondary py-4">No hay categorías registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <tr>
                                <td><?= escaparCategoria($categoria->getIdCategoria()) ?></td>
                                <td><?= escaparCategoria($categoria->getCategoria()) ?></td>
                                <td class="text-center text-nowrap">
                                    <a href="form_categoria.php?id=<?= rawurlencode((string) $categoria->getIdCategoria()) ?>"
                                       class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?');">
                                        <input type="hidden" name="eliminar_id" value="<?= escaparCategoria($categoria->getIdCategoria()) ?>">
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
