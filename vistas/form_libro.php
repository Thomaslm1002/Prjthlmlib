<?php
require_once __DIR__ . '/../model/libros.php';
require_once __DIR__ . '/../controler/librosctrl.php';

function escaparLibroForm($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$ctrlLibros = new LibrosCtrl();
$isbnParam = $_POST['isbn_original'] ?? $_GET['isbn'] ?? '';
$modoEdicion = $isbnParam !== '';
if ($modoEdicion && (!is_string($isbnParam) || !ctype_digit($isbnParam) || (int) $isbnParam < 1)) {
    header('Location: crud_libros.php');
    exit;
}

$libroOriginal = $modoEdicion ? $ctrlLibros->obtenerPorIsbn((int) $isbnParam) : null;
if ($modoEdicion && $libroOriginal === null) {
    header('Location: crud_libros.php?no_encontrado=1');
    exit;
}

$datos = [
    'isbn' => $libroOriginal ? $libroOriginal->getIsbn() : '',
    'titulo' => $libroOriginal ? $libroOriginal->getTitulo() : '',
    'categoria' => $libroOriginal ? $libroOriginal->getCategoria() : '',
    'fecha_publicacion' => $libroOriginal ? $libroOriginal->getFecha() : '',
    'precio' => $libroOriginal ? $libroOriginal->getPrecio() : '',
    'portada' => $libroOriginal ? $libroOriginal->getPortada() : 'no_imagen.png',
    'cantidad_stock' => $libroOriginal ? $libroOriginal->getCantidadStock() : ''
];
$errores = [];
$mensajeError = '';
$categorias = $ctrlLibros->listarCategorias();
$quitarPortada = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    foreach (['isbn', 'titulo', 'categoria', 'fecha_publicacion', 'precio', 'cantidad_stock'] as $campo) {
        $valor = $_POST[$campo] ?? '';
        $datos[$campo] = is_string($valor) ? trim($valor) : '';
    }
    $quitarPortada = isset($_POST['quitar_portada']) && $_POST['quitar_portada'] === '1';

    if (!$modoEdicion && (!ctype_digit($datos['isbn']) || (int) $datos['isbn'] < 1)) {
        $errores['isbn'] = 'Ingresa un ISBN numérico válido.';
    }
    if ($datos['titulo'] === '' || strlen($datos['titulo']) > 128) {
        $errores['titulo'] = 'El título es obligatorio y admite hasta 128 caracteres.';
    }
    if (!ctype_digit($datos['categoria']) || !in_array((int) $datos['categoria'], array_column($categorias, 'id_categoria'), false)) {
        $errores['categoria'] = 'Selecciona una categoría válida.';
    }
    $fecha = DateTime::createFromFormat('Y-m-d', $datos['fecha_publicacion']);
    if (!$fecha || $fecha->format('Y-m-d') !== $datos['fecha_publicacion']) {
        $errores['fecha_publicacion'] = 'Ingresa una fecha válida.';
    }
    $precio = filter_var($datos['precio'], FILTER_VALIDATE_INT);
    if ($precio === false || $precio < 0) {
        $errores['precio'] = 'El precio debe ser un número entero igual o mayor que cero.';
    }
    $stock = filter_var($datos['cantidad_stock'], FILTER_VALIDATE_INT);
    if ($stock === false || $stock < 0) {
        $errores['cantidad_stock'] = 'El stock debe ser un número entero igual o mayor que cero.';
    }

    $archivoPortada = $_FILES['portada'] ?? null;
    $subirPortada = false;
    if ($archivoPortada !== null) {
        if (!isset($archivoPortada['error']) || !is_int($archivoPortada['error'])) {
            $errores['portada'] = 'El archivo seleccionado no es una carga válida.';
        } elseif ($archivoPortada['error'] === UPLOAD_ERR_INI_SIZE
            || $archivoPortada['error'] === UPLOAD_ERR_FORM_SIZE
            || (isset($archivoPortada['size']) && $archivoPortada['size'] > 5 * 1024 * 1024)) {
            $errores['portada'] = 'La imagen debe pesar como máximo 5 MB.';
        } elseif ($archivoPortada['error'] === UPLOAD_ERR_PARTIAL) {
            $errores['portada'] = 'La imagen se recibió incompleta. Intenta cargarla nuevamente.';
        } elseif ($archivoPortada['error'] !== UPLOAD_ERR_NO_FILE
            && $archivoPortada['error'] !== UPLOAD_ERR_OK) {
            $errores['portada'] = 'No se pudo recibir la imagen. Intenta seleccionarla nuevamente.';
        } elseif ($archivoPortada['error'] === UPLOAD_ERR_OK
            && (!isset($archivoPortada['tmp_name'], $archivoPortada['size'])
                || !is_string($archivoPortada['tmp_name'])
                || !is_int($archivoPortada['size'])
                || !is_uploaded_file($archivoPortada['tmp_name']))) {
            $errores['portada'] = 'El archivo seleccionado no es una carga válida.';
        } elseif ($archivoPortada['error'] === UPLOAD_ERR_OK
            && ($archivoPortada['size'] < 1 || $archivoPortada['size'] > 5 * 1024 * 1024)) {
            $errores['portada'] = 'La imagen debe pesar como máximo 5 MB.';
        } else {
            if ($archivoPortada['error'] === UPLOAD_ERR_OK) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($archivoPortada['tmp_name']);
                $extensionesPermitidas = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/webp' => 'webp'
                ];
                $imagen = isset($extensionesPermitidas[$mime])
                    ? @getimagesize($archivoPortada['tmp_name'])
                    : false;
                if (!$imagen || $imagen['mime'] !== $mime) {
                    $errores['portada'] = 'Formato no válido. Usa una imagen JPG, PNG o WEBP.';
                } else {
                    $extensionPortada = $extensionesPermitidas[$mime];
                    $subirPortada = true;
                }
            }
        }
    }

    if (!$errores) {
        $portadaAnterior = $libroOriginal ? $libroOriginal->getPortada() : null;
        $portada = $quitarPortada ? 'no_imagen.png' : ($portadaAnterior ?: 'no_imagen.png');
        $archivoPortadaNuevo = null;

        if ($subirPortada) {
            $directorioPortadas = __DIR__ . '/../uploads/libros';
            if (!is_dir($directorioPortadas)
                && !mkdir($directorioPortadas, 0755, true)
                && !is_dir($directorioPortadas)) {
                $mensajeError = 'No se pudo preparar la carpeta para guardar portadas.';
            } else {
                try {
                    $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $extensionPortada;
                    $archivoPortadaNuevo = $directorioPortadas . '/' . $nombreArchivo;
                    if (move_uploaded_file($archivoPortada['tmp_name'], $archivoPortadaNuevo)) {
                        $portada = 'uploads/libros/' . $nombreArchivo;
                    } else {
                        $archivoPortadaNuevo = null;
                        $mensajeError = 'No se pudo guardar la imagen seleccionada.';
                    }
                } catch (Exception $exc) {
                    error_log('Error al crear el nombre de la portada: ' . $exc->getMessage());
                    $archivoPortadaNuevo = null;
                    $mensajeError = 'Ocurrió un error al preparar la imagen.';
                }
            }
        }

        if ($mensajeError === '') {
            $libro = new Libros(
                $modoEdicion ? (int) $isbnParam : (int) $datos['isbn'],
                $datos['titulo'],
                (int) $datos['categoria'],
                $datos['fecha_publicacion'],
                $precio,
                $portada,
                $stock
            );
            $guardado = $modoEdicion ? $ctrlLibros->actualizar($libro) : $ctrlLibros->insertar($libro);
            if ($guardado) {
                $portadaAnteriorSegura = $portadaAnterior !== null
                    && preg_match('~^uploads/libros/[a-f0-9]{32}\.(jpg|png|webp)$~', $portadaAnterior)
                    ? __DIR__ . '/../' . $portadaAnterior
                    : null;
                if ($portadaAnteriorSegura !== null
                    && ($quitarPortada || ($archivoPortadaNuevo !== null && $portadaAnterior !== $portada))
                    && is_file($portadaAnteriorSegura)
                    && !unlink($portadaAnteriorSegura)) {
                    error_log('No se pudo eliminar la portada anterior: ' . $portadaAnteriorSegura);
                }
                header('Location: crud_libros.php?' . ($modoEdicion ? 'actualizado=1' : 'registrado=1'));
                exit;
            }
            if ($archivoPortadaNuevo !== null && is_file($archivoPortadaNuevo)
                && !unlink($archivoPortadaNuevo)) {
                error_log('No se pudo limpiar la portada nueva tras fallar el guardado: ' . $archivoPortadaNuevo);
            }
            $mensajeError = 'No se pudo guardar el libro. Verifica que el ISBN no esté repetido y vuelve a intentarlo.';
        }
    }
}

$portadaActual = $datos['portada'];
$portadaActualSrc = preg_match('~^uploads/libros/[a-f0-9]{32}\.(jpg|png|webp)$~', (string) $portadaActual)
    && is_file(__DIR__ . '/../' . $portadaActual)
    ? '../' . $portadaActual
    : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $modoEdicion ? 'Editar libro' : 'Registrar libro' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <style>
        .error { color: rgb(128, 2, 2); }
        .cover-preview { width: 120px; height: 160px; object-fit: cover; }
    </style>
</head>
<body>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-8 border p-4 mt-5 rounded-2 shadow-lg">
            <h1 class="h2"><?= $modoEdicion ? 'Editar libro' : 'Registrar libro' ?></h1>
            <?php if ($mensajeError !== ''): ?>
                <div class="alert alert-danger" role="alert"><?= escaparLibroForm($mensajeError) ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data" action="<?= escaparLibroForm($_SERVER['PHP_SELF']) ?>">
                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="isbn_original" value="<?= escaparLibroForm($isbnParam) ?>">
                <?php endif; ?>
                <div class="form-floating mb-2">
                    <input type="number" class="form-control" id="isbn" name="isbn"
                           value="<?= escaparLibroForm($datos['isbn']) ?>" placeholder="ISBN"
                           min="1" <?= $modoEdicion ? 'readonly' : 'required' ?>>
                    <label for="isbn">ISBN</label>
                    <?php if (isset($errores['isbn'])): ?><span class="error"><?= escaparLibroForm($errores['isbn']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="titulo" name="titulo"
                           value="<?= escaparLibroForm($datos['titulo']) ?>" placeholder="Título" maxlength="128" required>
                    <label for="titulo">Título</label>
                    <?php if (isset($errores['titulo'])): ?><span class="error"><?= escaparLibroForm($errores['titulo']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <select class="form-select" id="categoria" name="categoria" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= escaparLibroForm($categoria['id_categoria']) ?>"
                                <?= (string) $datos['categoria'] === (string) $categoria['id_categoria'] ? 'selected' : '' ?>>
                                <?= escaparLibroForm($categoria['categoria']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="categoria">Categoría</label>
                    <?php if (isset($errores['categoria'])): ?><span class="error"><?= escaparLibroForm($errores['categoria']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion"
                           value="<?= escaparLibroForm($datos['fecha_publicacion']) ?>" required>
                    <label for="fecha_publicacion">Fecha de publicación</label>
                    <?php if (isset($errores['fecha_publicacion'])): ?><span class="error"><?= escaparLibroForm($errores['fecha_publicacion']) ?></span><?php endif; ?>
                </div>
                <div class="form-floating mb-2">
                    <input type="number" class="form-control" id="precio" name="precio"
                           value="<?= escaparLibroForm($datos['precio']) ?>" placeholder="Precio" min="0" step="1" required>
                    <label for="precio">Precio</label>
                    <?php if (isset($errores['precio'])): ?><span class="error"><?= escaparLibroForm($errores['precio']) ?></span><?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="portada" class="form-label">Imagen de portada (JPG, PNG o WEBP; máximo 5 MB)</label>
                    <input type="file" class="form-control" id="portada" name="portada"
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <div class="form-text">Si no seleccionas una imagen, se conservará la portada actual.</div>
                    <?php if (isset($errores['portada'])): ?><span class="error"><?= escaparLibroForm($errores['portada']) ?></span><?php endif; ?>
                </div>
                <?php if ($portadaActualSrc !== null): ?>
                    <div class="mb-3">
                        <p class="form-label">Portada actual</p>
                        <img src="<?= escaparLibroForm($portadaActualSrc) ?>" alt="Portada actual del libro"
                             class="cover-preview rounded border mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quitar_portada"
                                   name="quitar_portada" value="1" <?= $quitarPortada ? 'checked' : '' ?>>
                            <label class="form-check-label" for="quitar_portada">Quitar la portada actual</label>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="cantidad_stock" name="cantidad_stock"
                           value="<?= escaparLibroForm($datos['cantidad_stock']) ?>" placeholder="Stock" min="0" step="1" required>
                    <label for="cantidad_stock">Cantidad en stock</label>
                    <?php if (isset($errores['cantidad_stock'])): ?><span class="error"><?= escaparLibroForm($errores['cantidad_stock']) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?= $modoEdicion ? 'Guardar cambios' : 'Registrar libro' ?></button>
                <a href="crud_libros.php" class="btn btn-link w-100 mt-2">Volver a libros</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
