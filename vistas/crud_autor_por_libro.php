<?php
require_once __DIR__ . '/../model/autor_por_libro.php';
require_once __DIR__ . '/../controler/autor_por_libroctrl.php';

function escaparRelacion($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlRelaciones = new AutorPorLibroCtrl();
$mensaje = '';
$tipoMensaje = 'success';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $idAutor = $_POST['eliminar_id_autor'] ?? '';
    $isbn = $_POST['eliminar_isbn'] ?? '';
    if (is_string($idAutor) && ctype_digit($idAutor) && (int) $idAutor > 0
        && is_string($isbn) && ctype_digit($isbn) && (int) $isbn > 0) {
        if ($ctrlRelaciones->eliminar((int) $idAutor, (int) $isbn)) {
            $mensaje = 'La relación se eliminó correctamente.';
        } else {
            $mensaje = 'No se pudo eliminar la relación.';
            $tipoMensaje = 'danger';
        }
    } else {
        $mensaje = 'Los datos de la relación no son válidos.';
        $tipoMensaje = 'danger';
    }
}

$relaciones = $ctrlRelaciones->listar();
if ($mensaje === '') {
    if (isset($_GET['registrado'])) {
        $mensaje = 'La relación se registró correctamente.';
    } elseif (isset($_GET['actualizado'])) {
        $mensaje = 'La relación se actualizó correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autores por libro</title>
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
                    <h1 class="h2 mb-1">Autores por libro</h1>
                    <p class="text-body-secondary mb-0">Administra las asociaciones entre autores y libros.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="form_autor_por_libro.php" class="btn btn-primary">Asociar autor y libro</a>
                    <a href="crud_autores.php" class="btn btn-outline-secondary">Autores</a>
                    <a href="crud_libros.php" class="btn btn-outline-secondary">Volver a libros</a>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= escaparRelacion($tipoMensaje) ?>" role="status"><?= escaparRelacion($mensaje) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Autor</th>
                            <th scope="col">Libro</th>
                            <th scope="col">ISBN</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$relaciones): ?>
                        <tr><td colspan="4" class="text-center text-body-secondary py-4">No hay asociaciones registradas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($relaciones as $relacion): ?>
                            <tr>
                                <td><?= escaparRelacion($relacion['nombre'] . ' ' . $relacion['apellido']) ?></td>
                                <td><?= escaparRelacion($relacion['titulo']) ?></td>
                                <td><?= escaparRelacion($relacion['isbn']) ?></td>
                                <td class="text-center text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="form_autor_por_libro.php?id_autor=<?= rawurlencode((string) $relacion['id_autor']) ?>&amp;isbn=<?= rawurlencode((string) $relacion['isbn']) ?>">Editar</a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar esta asociación?');">
                                        <input type="hidden" name="eliminar_id_autor" value="<?= escaparRelacion($relacion['id_autor']) ?>">
                                        <input type="hidden" name="eliminar_isbn" value="<?= escaparRelacion($relacion['isbn']) ?>">
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
