<?php
require_once __DIR__ . '/../model/autor_por_libro.php';
require_once __DIR__ . '/../controler/autor_por_libroctrl.php';

function escaparRelacionForm($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlRelaciones = new AutorPorLibroCtrl();
$idAutorOriginal = $_POST['id_autor_original'] ?? $_GET['id_autor'] ?? '';
$isbnOriginal = $_POST['isbn_original'] ?? $_GET['isbn'] ?? '';
$modoEdicion = $idAutorOriginal !== '' || $isbnOriginal !== '';
if ($modoEdicion && (
    !is_string($idAutorOriginal) || !ctype_digit($idAutorOriginal) || (int) $idAutorOriginal < 1
    || !is_string($isbnOriginal) || !ctype_digit($isbnOriginal) || (int) $isbnOriginal < 1
)) {
    header('Location: crud_autor_por_libro.php');
    exit;
}

$relacionOriginal = $modoEdicion
    ? $ctrlRelaciones->obtener((int) $idAutorOriginal, (int) $isbnOriginal)
    : null;
if ($modoEdicion && $relacionOriginal === null) {
    header('Location: crud_autor_por_libro.php?no_encontrado=1');
    exit;
}

$datos = [
    'id_autor' => $relacionOriginal ? $relacionOriginal->getIdAutor() : '',
    'isbn' => $relacionOriginal ? $relacionOriginal->getIsbn() : ''
];
$autores = $ctrlRelaciones->listarAutores();
$libros = $ctrlRelaciones->listarLibros();
$errores = [];
$mensajeError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach (['id_autor', 'isbn'] as $campo) {
        $valor = $_POST[$campo] ?? '';
        $datos[$campo] = is_string($valor) ? trim($valor) : '';
    }

    $idsAutores = array_map('strval', array_column($autores, 'id_autor'));
    $isbnsLibros = array_map('strval', array_column($libros, 'isbn'));
    if (!ctype_digit($datos['id_autor']) || !in_array($datos['id_autor'], $idsAutores, true)) {
        $errores['id_autor'] = 'Selecciona un autor válido.';
    }
    if (!ctype_digit($datos['isbn']) || !in_array($datos['isbn'], $isbnsLibros, true)) {
        $errores['isbn'] = 'Selecciona un libro válido.';
    }

    if (!$errores) {
        $relacion = new Autor_por_libro((int) $datos['id_autor'], (int) $datos['isbn']);
        $guardado = $modoEdicion
            ? $ctrlRelaciones->actualizar($relacion, (int) $idAutorOriginal, (int) $isbnOriginal)
            : $ctrlRelaciones->insertar($relacion);
        if ($guardado) {
            header('Location: crud_autor_por_libro.php?' . ($modoEdicion ? 'actualizado=1' : 'registrado=1'));
            exit;
        }
        $mensajeError = 'No se pudo guardar la asociación. Puede que ya exista o que una clave relacionada no sea válida.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modoEdicion ? 'Editar asociación' : 'Asociar autor y libro' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>.error { color: rgb(128, 2, 2); }</style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 border p-4 mt-5 rounded-2 shadow-lg">
            <h1 class="h2"><?= $modoEdicion ? 'Editar asociación' : 'Asociar autor y libro' ?></h1>
            <?php if ($mensajeError !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= escaparRelacionForm($mensajeError) ?></div>
            <?php endif; ?>
            <?php if (!$autores || !$libros): ?>
                <div class="alert alert-warning" role="alert">
                    Registra al menos un autor y un libro antes de crear una asociación.
                </div>
            <?php endif; ?>
            <form method="post" action="<?= escaparRelacionForm($_SERVER['PHP_SELF']) ?>">
                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="id_autor_original" value="<?= escaparRelacionForm($idAutorOriginal) ?>">
                    <input type="hidden" name="isbn_original" value="<?= escaparRelacionForm($isbnOriginal) ?>">
                <?php endif; ?>
                <div class="form-floating mb-2">
                    <select class="form-select" id="id_autor" name="id_autor" required>
                        <option value="">Selecciona un autor</option>
                        <?php foreach ($autores as $autor): ?>
                            <option value="<?= escaparRelacionForm($autor['id_autor']) ?>"
                                <?= (string) $datos['id_autor'] === (string) $autor['id_autor'] ? 'selected' : '' ?>>
                                <?= escaparRelacionForm($autor['nombre'] . ' ' . $autor['apellido']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="id_autor">Autor</label>
                    <?php if (isset($errores['id_autor'])): ?><span class="error"><?= escaparRelacionForm($errores['id_autor']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" id="isbn" name="isbn" required>
                        <option value="">Selecciona un libro</option>
                        <?php foreach ($libros as $libro): ?>
                            <option value="<?= escaparRelacionForm($libro['isbn']) ?>"
                                <?= (string) $datos['isbn'] === (string) $libro['isbn'] ? 'selected' : '' ?>>
                                <?= escaparRelacionForm($libro['titulo'] . ' (ISBN ' . $libro['isbn'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="isbn">Libro</label>
                    <?php if (isset($errores['isbn'])): ?><span class="error"><?= escaparRelacionForm($errores['isbn']) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100" <?= (!$autores || !$libros) ? 'disabled' : '' ?>>
                    <?= $modoEdicion ? 'Guardar cambios' : 'Crear asociación' ?>
                </button>
                <a href="crud_autor_por_libro.php" class="btn btn-link w-100 mt-2">Volver a asociaciones</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
