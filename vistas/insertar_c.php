<?php
require_once __DIR__ . '/../model/clientes.php';
require_once __DIR__ . '/../controler/clientesctrl.php';

$campos = ['identificacion', 'nombre', 'apellidos', 'celular', 'direccion', 'correo'];
$datos = array_fill_keys($campos, '');
$errores = [];
$mensajeError = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach ($campos as $campo) {
        $valor = $_POST[$campo] ?? '';
        $datos[$campo] = is_string($valor) ? trim($valor) : '';

        if ($datos[$campo] === '') {
            $errores[$campo] = 'Este campo es obligatorio.';
        }
    }

    if (!isset($errores['correo']) && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = 'Ingresa un correo electrónico válido.';
    }

    if (!$errores) {
        $cliente = new Clientes(
            $datos['identificacion'],
            $datos['nombre'],
            $datos['apellidos'],
            $datos['celular'],
            $datos['direccion'],
            $datos['correo']
        );

        $clientesCtrl = new ClientesCtrl();
        if ($clientesCtrl->insertar($cliente)) {
            header('Location: crud_c.php?registrado=1');
            exit;
        }

        $mensajeError = 'No se pudo registrar el cliente. Inténtalo de nuevo.';
    }
}

function escapar($valor) {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .error { color: rgb(128, 2, 2); }
    </style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 border p-4 mt-5 rounded-2 shadow-lg">
            <h1 class="h2">Registrar cliente</h1>

            <?php if ($mensajeError !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= escapar($mensajeError) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= escapar($_SERVER['PHP_SELF']) ?>">
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="identificacion" name="identificacion"
                           value="<?= escapar($datos['identificacion']) ?>" placeholder="Identificación" required>
                    <label for="identificacion">Identificación</label>
                    <?php if (isset($errores['identificacion'])): ?>
                        <span class="error"><?= escapar($errores['identificacion']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           value="<?= escapar($datos['nombre']) ?>" placeholder="Nombre" required>
                    <label for="nombre">Nombre</label>
                    <?php if (isset($errores['nombre'])): ?>
                        <span class="error"><?= escapar($errores['nombre']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="apellidos" name="apellidos"
                           value="<?= escapar($datos['apellidos']) ?>" placeholder="Apellidos" required>
                    <label for="apellidos">Apellidos</label>
                    <?php if (isset($errores['apellidos'])): ?>
                        <span class="error"><?= escapar($errores['apellidos']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="tel" class="form-control" id="celular" name="celular"
                           value="<?= escapar($datos['celular']) ?>" placeholder="Celular" required>
                    <label for="celular">Celular</label>
                    <?php if (isset($errores['celular'])): ?>
                        <span class="error"><?= escapar($errores['celular']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="direccion" name="direccion"
                           value="<?= escapar($datos['direccion']) ?>" placeholder="Dirección" required>
                    <label for="direccion">Dirección</label>
                    <?php if (isset($errores['direccion'])): ?>
                        <span class="error"><?= escapar($errores['direccion']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="correo" name="correo"
                           value="<?= escapar($datos['correo']) ?>" placeholder="Correo electrónico" required>
                    <label for="correo">Correo electrónico</label>
                    <?php if (isset($errores['correo'])): ?>
                        <span class="error"><?= escapar($errores['correo']) ?></span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrarme</button>
                <a href="crud_c.php" class="btn btn-link w-100 mt-2">Volver a clientes</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
