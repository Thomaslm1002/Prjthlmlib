<?php
require_once __DIR__ . '/../controler/librosctrl.php';
require_once __DIR__ . '/../controler/pedidos_por_clientesctrl.php';
require_once __DIR__ . '/../controler/clientesctrl.php';

function escaparDashboard($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$libros = (new LibrosCtrl())->listar();
$pedidos = (new PedidosPorClientesCtrl())->listar();
$clientes = (new Clientesctrl())->listar();

$librosPorCategoria = [];
foreach ($libros as $libro) {
    $categoria = trim((string) ($libro->getNombreCategoria() ?: 'Sin categoría'));
    $librosPorCategoria[$categoria] = ($librosPorCategoria[$categoria] ?? 0) + 1;
}
arsort($librosPorCategoria);
$librosPorCategoria = array_slice($librosPorCategoria, 0, 7, true);

$nombresMeses = [1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'];
$inicioMes = new DateTimeImmutable('first day of this month');
$pedidosPorMes = [];
for ($offset = 5; $offset >= 0; $offset--) {
    $mes = $inicioMes->modify('-' . $offset . ' months');
    $clave = $mes->format('Y-m');
    $pedidosPorMes[$clave] = ['label' => $nombresMeses[(int) $mes->format('n')], 'total' => 0];
}

$pedidosPorCliente = [];
foreach ($pedidos as $pedido) {
    $mes = substr((string) ($pedido['fecha_pedido'] ?? ''), 0, 7);
    if (isset($pedidosPorMes[$mes])) {
        $pedidosPorMes[$mes]['total']++;
    }
    $idCliente = (string) ($pedido['id_cliente'] ?? '');
    if ($idCliente !== '') {
        if (!isset($pedidosPorCliente[$idCliente])) {
            $pedidosPorCliente[$idCliente] = [
                'label' => trim(($pedido['nombre_cliente'] ?? '') . ' ' . ($pedido['apellidos_cliente'] ?? '')),
                'total' => 0,
            ];
        }
        $pedidosPorCliente[$idCliente]['total']++;
    }
}
usort($pedidosPorCliente, static function ($a, $b) {
    return $b['total'] <=> $a['total'];
});
$pedidosPorCliente = array_slice($pedidosPorCliente, 0, 6);

$datosGraficos = json_encode([
    'categories' => [
        'labels' => array_keys($librosPorCategoria) ?: ['Sin datos'],
        'values' => array_values($librosPorCategoria) ?: [0],
    ],
    'orders' => [
        'labels' => array_column(array_values($pedidosPorMes), 'label'),
        'values' => array_column(array_values($pedidosPorMes), 'total'),
    ],
    'customers' => [
        'labels' => array_column($pedidosPorCliente, 'label') ?: ['Sin datos'],
        'values' => array_column($pedidosPorCliente, 'total') ?: [0],
    ],
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Librería THLM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="dashboard-shell">
    <aside class="dashboard-sidebar">
        <a class="dashboard-brand" href="dashboard.php">
            <span class="admin-brand-mark" aria-hidden="true">TH</span>
            <span>Biblioteca THLM<small>Panel administrativo</small></span>
        </a>
        <p class="dashboard-nav-label">GESTIÓN</p>
        <nav class="dashboard-menu" aria-label="Menú principal">
            <a class="is-current" href="dashboard.php" aria-current="page"><span aria-hidden="true"></span> Resumen</a>
            <a href="crud_libros.php"><span aria-hidden="true"></span> Libros</a>
            <a href="crud_pedidos.php"><span aria-hidden="true"></span> Pedidos</a>
            <a href="crud_c.php"><span aria-hidden="true"></span> Clientes</a>
            <a href="crud_categorias.php"><span aria-hidden="true"></span> Categorías</a>
            <a href="crud_autores.php"><span aria-hidden="true"></span> Autores</a>
            <a href="crud_autor_por_libro.php"><span aria-hidden="true"></span> Autores por libro</a>
        </nav>
        <div class="dashboard-sidebar-foot">THLM <span>·</span> Gestión de librería</div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-page-header">
            <div>
                <p class="dashboard-eyebrow">RESUMEN GENERAL</p>
                <h1>Panel de control</h1>
                <p>Indicadores actualizados desde el catálogo y las operaciones registradas.</p>
            </div>
            <div class="dashboard-date"><span aria-hidden="true"></span> <?= escaparDashboard(date('d/m/Y')) ?></div>
        </header>

        <section class="dashboard-metrics" aria-label="Indicadores principales">
            <article class="metric-panel metric-books">
                <p>Libros registrados</p>
                <strong><?= number_format(count($libros), 0, ',', '.') ?></strong>
                <span>En todo el catálogo</span>
            </article>
            <article class="metric-panel metric-orders">
                <p>Pedidos registrados</p>
                <strong><?= number_format(count($pedidos), 0, ',', '.') ?></strong>
                <span>Historial completo</span>
            </article>
            <article class="metric-panel metric-clients">
                <p>Clientes registrados</p>
                <strong><?= number_format(count($clientes), 0, ',', '.') ?></strong>
                <span>Base de clientes</span>
            </article>
        </section>

        <section class="dashboard-charts" aria-label="Gráficos del negocio">
            <article class="dashboard-panel chart-panel chart-panel-wide">
                <header class="panel-heading">
                    <div><p>CATÁLOGO</p><h2>Libros por categoría</h2></div>
                    <a href="crud_libros.php">Ver libros <span aria-hidden="true">↗</span></a>
                </header>
                <div class="chart-wrap chart-wrap-category"><canvas id="categoryChart" aria-label="Cantidad de libros por categoría" role="img"></canvas></div>
            </article>

            <article class="dashboard-panel chart-panel">
                <header class="panel-heading">
                    <div><p>ACTIVIDAD</p><h2>Pedidos por mes</h2></div>
                    <a href="crud_pedidos.php">Ver pedidos <span aria-hidden="true">↗</span></a>
                </header>
                <div class="chart-wrap"><canvas id="ordersChart" aria-label="Pedidos de los últimos seis meses" role="img"></canvas></div>
            </article>

            <article class="dashboard-panel chart-panel">
                <header class="panel-heading">
                    <div><p>CLIENTES</p><h2>Más pedidos</h2></div>
                    <a href="crud_c.php">Ver clientes <span aria-hidden="true">↗</span></a>
                </header>
                <div class="chart-wrap"><canvas id="customersChart" aria-label="Clientes con más pedidos registrados" role="img"></canvas></div>
            </article>

            <article class="dashboard-panel recent-panel">
                <header class="panel-heading">
                    <div><p>ÚLTIMOS MOVIMIENTOS</p><h2>Pedidos recientes</h2></div>
                    <a href="crud_pedidos.php">Ver todos <span aria-hidden="true">↗</span></a>
                </header>
                <div class="table-responsive dashboard-recent-table">
                    <table class="table align-middle mb-0">
                        <thead><tr><th scope="col">N.º</th><th scope="col">Cliente</th><th scope="col">Libro</th><th scope="col">Fecha</th></tr></thead>
                        <tbody>
                        <?php if (!$pedidos): ?>
                            <tr><td colspan="4" class="text-center text-body-secondary py-4">Todavía no hay pedidos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach (array_slice($pedidos, 0, 5) as $pedido): ?>
                                <tr>
                                    <td>#<?= escaparDashboard($pedido['nro_pedido']) ?></td>
                                    <td><?= escaparDashboard(trim($pedido['nombre_cliente'] . ' ' . $pedido['apellidos_cliente'])) ?></td>
                                    <td><?= escaparDashboard($pedido['titulo']) ?></td>
                                    <td><?= escaparDashboard(date('d/m/Y', strtotime($pedido['fecha_pedido']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
        <footer class="dashboard-footer">Biblioteca THLM <span>·</span> Datos de catálogo y pedidos</footer>
    </main>
</div>

<script>
const dashboardData = <?= $datosGraficos ?: '{}' ?>;
const chartColors = ['#17684f', '#d36a4e', '#a1b950', '#548b83', '#dcaa4b', '#7886a0', '#b26b88'];
const baseChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { displayColors: false, padding: 10 } },
};

new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: { labels: dashboardData.categories.labels, datasets: [{ data: dashboardData.categories.values, backgroundColor: chartColors, borderRadius: 4, maxBarThickness: 42 }] },
    options: { ...baseChartOptions, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e8eee9' } } } },
});

new Chart(document.getElementById('ordersChart'), {
    type: 'line',
    data: { labels: dashboardData.orders.labels, datasets: [{ data: dashboardData.orders.values, borderColor: '#17684f', backgroundColor: 'rgba(23, 104, 79, .12)', fill: true, tension: .35, pointRadius: 3, pointBackgroundColor: '#17684f' }] },
    options: { ...baseChartOptions, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e8eee9' } } } },
});

new Chart(document.getElementById('customersChart'), {
    type: 'bar',
    data: { labels: dashboardData.customers.labels, datasets: [{ data: dashboardData.customers.values, backgroundColor: '#d36a4e', borderRadius: 4, maxBarThickness: 26 }] },
    options: { ...baseChartOptions, indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e8eee9' } }, y: { grid: { display: false } } } },
});
</script>
</body>
</html>
