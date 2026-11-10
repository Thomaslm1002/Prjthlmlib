<?php
require_once __DIR__ . '/../model/categorias.php';
require_once __DIR__ . '/../controler/categoriasctrl.php';

function escaparCategoriaForm($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlCategorias = new CategoriasCtrl();
$idParam = $_POST['id_original'] ?? $_GET['id'] ?? '';
$editar = $idParam !== '';
if ($editar && (!is_string($idParam) || !ctype_digit($idParam) || (int) $idParam < 1)) {
    header('Location: crud_categorias.php');
    exit;
}
$original = $editar ? $ctrlCategorias->obtenerPorId((int) $idParam) : null;
if ($editar && !$original) {
    header('Location: crud_categorias.php?no_encontrada=1');
    exit;
}
$nombre = $original ? $original->getCategoria() : '';
$error = '';
$mensajeError = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $valor = $_POST['categoria'] ?? '';
    $nombre = is_string($valor) ? trim($valor) : '';
    if ($nombre === '' || strlen($nombre) > 100) {
        $error = 'La categoría es obligatoria y admite hasta 100 caracteres.';
    } else {
        $categoria = new Categorias($nombre);
        if ($editar) {
            $categoria->setIdCategoria((int) $idParam);
        }
        $guardado = $editar ? $ctrlCategorias->actualizar($categoria) : $ctrlCategorias->insertar($categoria);
        if ($guardado) {
            header('Location: crud_categorias.php?' . ($editar ? 'actualizado=1' : 'registrado=1'));
            exit;
        }
        $mensajeError = 'No se pudo guardar la categoría. Verifica que el nombre no esté repetido.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editar ? 'Editar categoría' : 'Registrar categoría' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>.error { color: rgb(128, 2, 2); }</style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 border p-4 mt-5 rounded-2 shadow-lg">
            <h1 class="h2"><?= $editar ? 'Editar categoría' : 'Registrar categoría' ?></h1>
            <?php if ($mensajeError !== ''): ?><div class="alert alert-danger" role="alert"><?= escaparCategoriaForm($mensajeError) ?></div><?php endif; ?>
            <form method="post" action="<?= escaparCategoriaForm($_SERVER['PHP_SELF']) ?>">
                <?php if ($editar): ?><input type="hidden" name="id_original" value="<?= escaparCategoriaForm($idParam) ?>"><?php endif; ?>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="categoria" name="categoria"
                           value="<?= escaparCategoriaForm($nombre) ?>" placeholder="Categoría" maxlength="100" required>
                    <label for="categoria">Nombre de la categoría</label>
                    <?php if ($error !== ''): ?><span class="error"><?= escaparCategoriaForm($error) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?= $editar ? 'Guardar cambios' : 'Registrar categoría' ?></button>
                <a href="crud_categorias.php" class="btn btn-link w-100 mt-2">Volver a categorías</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
