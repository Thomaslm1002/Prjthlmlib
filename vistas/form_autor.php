<?php
require_once __DIR__ . '/../model/autores.php';
require_once __DIR__ . '/../controler/autoresctrl.php';

function escaparAutorForm($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlAutores = new AutoresCtrl();
$idParam = $_POST['id_original'] ?? $_GET['id'] ?? '';
$editar = $idParam !== '';
if ($editar && (!is_string($idParam) || !ctype_digit($idParam) || (int) $idParam < 1)) {
    header('Location: crud_autores.php');
    exit;
}
$original = $editar ? $ctrlAutores->obtenerPorId((int) $idParam) : null;
if ($editar && !$original) {
    header('Location: crud_autores.php?no_encontrado=1');
    exit;
}
$nombre = $original ? $original->getNombres() : '';
$apellido = $original ? $original->getApellidos() : '';
$errores = [];
$mensajeError = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $nombreRecibido = $_POST['nombre'] ?? '';
    $apellidoRecibido = $_POST['apellido'] ?? '';
    $nombre = is_string($nombreRecibido) ? trim($nombreRecibido) : '';
    $apellido = is_string($apellidoRecibido) ? trim($apellidoRecibido) : '';
    if ($nombre === '' || strlen($nombre) > 64) {
        $errores['nombre'] = 'El nombre es obligatorio y admite hasta 64 caracteres.';
    }
    if ($apellido === '' || strlen($apellido) > 64) {
        $errores['apellido'] = 'El apellido es obligatorio y admite hasta 64 caracteres.';
    }
    if (!$errores) {
        $autor = new Autores($nombre, $apellido);
        if ($editar) {
            $autor->setIdAutor((int) $idParam);
        }
        $guardado = $editar ? $ctrlAutores->actualizar($autor) : $ctrlAutores->insertar($autor);
        if ($guardado) {
            header('Location: crud_autores.php?' . ($editar ? 'actualizado=1' : 'registrado=1'));
            exit;
        }
        $mensajeError = 'No se pudo guardar el autor. Inténtalo de nuevo.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editar ? 'Editar autor' : 'Registrar autor' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>.error { color: rgb(128, 2, 2); }</style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 border p-4 mt-5 rounded-2 shadow-lg">
            <h1 class="h2"><?= $editar ? 'Editar autor' : 'Registrar autor' ?></h1>
            <?php if ($mensajeError !== ''): ?><div class="alert alert-danger" role="alert"><?= escaparAutorForm($mensajeError) ?></div><?php endif; ?>
            <form method="post" action="<?= escaparAutorForm($_SERVER['PHP_SELF']) ?>">
                <?php if ($editar): ?><input type="hidden" name="id_original" value="<?= escaparAutorForm($idParam) ?>"><?php endif; ?>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?= escaparAutorForm($nombre) ?>" placeholder="Nombre" maxlength="64" required>
                    <label for="nombre">Nombre</label>
                    <?php if (isset($errores['nombre'])): ?><span class="error"><?= escaparAutorForm($errores['nombre']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="apellido" name="apellido"
                           value="<?= escaparAutorForm($apellido) ?>" placeholder="Apellido" maxlength="64" required>
                    <label for="apellido">Apellido</label>
                    <?php if (isset($errores['apellido'])): ?><span class="error"><?= escaparAutorForm($errores['apellido']) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?= $editar ? 'Guardar cambios' : 'Registrar autor' ?></button>
                <a href="crud_autores.php" class="btn btn-link w-100 mt-2">Volver a autores</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
