<?php
$paginaActual = basename($_SERVER['PHP_SELF'] ?? '');
$moduloActual = [
    'crud_libros.php' => 'Libros', 'form_libro.php' => 'Libros',
    'crud_pedidos.php' => 'Pedidos', 'form_pedido.php' => 'Pedidos',
    'crud_c.php' => 'Clientes', 'insertar_c.php' => 'Clientes',
    'editar_c.php' => 'Clientes', 'eliminar_c.php' => 'Clientes',
    'crud_categorias.php' => 'Categorías', 'form_categoria.php' => 'Categorías',
    'crud_autores.php' => 'Autores', 'form_autor.php' => 'Autores',
    'crud_autor_por_libro.php' => 'Autores por libro', 'form_autor_por_libro.php' => 'Autores por libro',
][$paginaActual] ?? '';
$opcionesMenu = [
    'Panel' => 'dashboard.php',
    'Libros' => 'crud_libros.php',
    'Pedidos' => 'crud_pedidos.php',
    'Clientes' => 'crud_c.php',
    'Categorías' => 'crud_categorias.php',
    'Autores' => 'crud_autores.php',
    'Autores por libro' => 'crud_autor_por_libro.php',
];
?>
<header class="admin-topbar">
    <div class="container admin-topbar-inner">
        <a class="admin-brand" href="dashboard.php">
            <span class="admin-brand-mark" aria-hidden="true">TH</span>
            <span>Biblioteca THLM<small>Administración</small></span>
        </a>
        <p class="admin-nav-label">GESTIÓN</p>
        <nav class="admin-nav" aria-label="Navegación principal">
            <?php foreach ($opcionesMenu as $etiqueta => $destino): ?>
                <a href="<?= $destino ?>" class="<?= $moduloActual === $etiqueta ? 'is-current' : '' ?>"<?= $moduloActual === $etiqueta ? ' aria-current="page"' : '' ?>><?= $etiqueta ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar-foot">THLM <span>·</span> Gestión de librería</div>
    </div>
</header>