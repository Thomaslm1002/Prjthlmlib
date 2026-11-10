<?php
require_once __DIR__ . '/../model/libros.php';
require_once __DIR__ . '/../controler/librosctrl.php';

function escaparLibro($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlLibros = new LibrosCtrl();
$mensaje = '';
$tipoMensaje = 'success';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['eliminar_isbn'])) {
    $isbnEliminar = $_POST['eliminar_isbn'];
    if (is_string($isbnEliminar) && ctype_digit($isbnEliminar) && (int) $isbnEliminar > 0) {
        if ($ctrlLibros->eliminar((int) $isbnEliminar)) {
            $mensaje = 'El libro se eliminó correctamente.';
        } else {
            $mensaje = 'No se pudo eliminar el libro. Puede estar relacionado con un autor.';
            $tipoMensaje = 'danger';
        }
    } else {
        $mensaje = 'El ISBN indicado no es válido.';
        $tipoMensaje = 'danger';
    }
}

$libros = $ctrlLibros->listar();
if ($mensaje === '') {
    if (isset($_GET['registrado'])) {
        $mensaje = 'El libro se registró correctamente.';
    } elseif (isset($_GET['actualizado'])) {
        $mensaje = 'El libro se actualizó correctamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .book-cover { width: 54px; height: 72px; object-fit: cover; }
        .book-cover-placeholder {
            width: 54px;
            height: 72px;
            display: inline-grid;
            place-items: center;
            background: #e9ecef;
            color: #6c757d;
            font-size: .7rem;
            text-align: center;
        }
    </style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11 border p-4 mt-5 mb-5 rounded-2 shadow-lg">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
                <div>
                    <h1 class="h2 mb-1">Libros</h1>
                    <p class="text-body-secondary mb-0">Consulta y administra el catálogo de libros.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="form_libro.php" class="btn btn-primary">Registrar libro</a>
                    <a href="crud_categorias.php" class="btn btn-outline-secondary">Categorías</a>
                    <a href="crud_autores.php" class="btn btn-outline-secondary">Autores</a>
                    <a href="crud_autor_por_libro.php" class="btn btn-outline-secondary">Autores por libro</a>
                    <a href="crud_pedidos.php" class="btn btn-outline-secondary">Pedidos</a>
                </div>
            </div>

            <?php if ($mensaje !== ''): ?>
                <div class="alert alert-<?= escaparLibro($tipoMensaje) ?>" role="status"><?= escaparLibro($mensaje) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ISBN</th>
                            <th scope="col">Portada</th>
                            <th scope="col">Título</th>
                            <th scope="col">Categoría</th>
                            <th scope="col">Fecha de publicación</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Stock</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$libros): ?>
                        <tr><td colspan="8" class="text-center text-body-secondary py-4">No hay libros registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?= escaparLibro($libro->getIsbn()) ?></td>
                                <td>
                                    <?php
                                    $portada = $libro->getPortada();
                                    $portadaSrc = is_string($portada)
                                        && preg_match('~^uploads/libros/[a-f0-9]{32}\.(jpg|png|webp)$~', $portada)
                                        && is_file(__DIR__ . '/../' . $portada)
                                        ? '../' . $portada
                                        : null;
                                    ?>
                                    <?php if ($portadaSrc !== null): ?>
                                        <img src="<?= escaparLibro($portadaSrc) ?>"
                                             alt="Portada de <?= escaparLibro($libro->getTitulo()) ?>"
                                             class="book-cover rounded border">
                                    <?php else: ?>
                                        <span class="book-cover-placeholder rounded" aria-label="Sin portada">Sin portada</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= escaparLibro($libro->getTitulo()) ?></td>
                                <td><?= escaparLibro($libro->getNombreCategoria() ?: $libro->getCategoria()) ?></td>
                                <td><?= escaparLibro($libro->getFecha()) ?></td>
                                <td><?= escaparLibro(number_format((float) $libro->getPrecio(), 0, ',', '.')) ?></td>
                                <td><?= escaparLibro($libro->getCantidadStock()) ?></td>
                                <td class="text-center text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="form_libro.php?isbn=<?= rawurlencode((string) $libro->getIsbn()) ?>">Editar</a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este libro?');">
                                        <input type="hidden" name="eliminar_isbn" value="<?= escaparLibro($libro->getIsbn()) ?>">
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
