<?php
require_once __DIR__ . '/../controler/autoresctrl.php';

function escaparAutor($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlAutores = new AutoresCtrl();
$mensaje = '';
$tipoMensaje = 'success';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $id = $_POST['eliminar_id'] ?? '';
    if (is_string($id) && ctype_digit($id) && (int) $id > 0) {
        if ($ctrlAutores->eliminar((int) $id)) {
            $mensaje = 'El autor se eliminó correctamente.';
        } else {
            $mensaje = 'No se pudo eliminar el autor. Verifica que no tenga libros asociados.';
            $tipoMensaje = 'danger';
        }
    } else {
        $mensaje = 'El identificador del autor no es válido.';
        $tipoMensaje = 'danger';
    }
}
$autores = $ctrlAutores->listar();
if ($mensaje === '') {
    if (isset($_GET['registrado'])) {
        $mensaje = 'El autor se registró correctamente.';
    } elseif (isset($_GET['actualizado'])) {
        $mensaje = 'El autor se actualizó correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores</title>
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
                    <h1 class="h2 mb-1">Autores</h1>
                    <p class="text-body-secondary mb-0">Administra los autores del catálogo.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="form_autor.php" class="btn btn-primary">Registrar autor</a>
                    <a href="crud_autor_por_libro.php" class="btn btn-outline-secondary">Autores por libro</a>
                </div>
            </div>
            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= escaparAutor($tipoMensaje) ?>" role="status"><?= escaparAutor($mensaje) ?></div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr><th scope="col">ID</th><th scope="col">Nombre</th><th scope="col">Apellido</th><th scope="col" class="text-center">Acciones</th></tr>
                    </thead>
                    <tbody>
                    <?php if (!$autores): ?>
                        <tr><td colspan="4" class="text-center text-body-secondary py-4">No hay autores registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($autores as $autor): ?>
                            <tr>
                                <td><?= escaparAutor($autor->getIdAutor()) ?></td>
                                <td><?= escaparAutor($autor->getNombres()) ?></td>
                                <td><?= escaparAutor($autor->getApellidos()) ?></td>
                                <td class="text-center text-nowrap">
                                    <a href="form_autor.php?id=<?= rawurlencode((string) $autor->getIdAutor()) ?>"
                                       class="btn btn-sm btn-outline-primary">Editar</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este autor?');">
                                        <input type="hidden" name="eliminar_id" value="<?= escaparAutor($autor->getIdAutor()) ?>">
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
