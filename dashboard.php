<?php
session_start();
require_once __DIR__ . '/config/app.php';

/*
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
*/

$pdo = app_db();
$metrics = dashboard_metrics($pdo);
$series = dashboard_series($pdo);
$activities = fetch_recent_activity($pdo, 4);
$events = fetch_upcoming_events($pdo, 3);

function trend_class(float $value): string
{
    return $value >= 0 ? 'up' : 'down';
}

function trend_icon(float $value): string
{
    return $value >= 0 ? 'ph-trend-up' : 'ph-trend-down';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-icon">
                    <i class="ph ph-buildings"></i>
                </div>
                <div class="sidebar-brand">
                    <h2>AsociaPro</h2>
                    <p>AMUVIE</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <i class="ph ph-squares-four nav-icon"></i>
                    Dashboard
                </a>
                <a href="socios.php" class="nav-item">
                    <i class="ph ph-users nav-icon"></i>
                    Socios
                </a>
                <a href="usuarios.php" class="nav-item">
                    <i class="ph ph-user-gear nav-icon"></i>
                    Usuarios y Roles
                </a>
                <a href="solicitudes.php" class="nav-item">
                    <i class="ph ph-file-text nav-icon"></i>
                    Solicitudes
                    <span class="badge">2</span>
                </a>
                <a href="pagos.php" class="nav-item">
                    <i class="ph ph-credit-card nav-icon"></i>
                    Pagos y Cuotas
                </a>
                <a href="directorio.php" class="nav-item">
                    <i class="ph ph-book-open nav-icon"></i>
                    Directorio
                </a>
                <a href="comunicacion.php" class="nav-item">
                    <i class="ph ph-paper-plane-tilt nav-icon"></i>
                    Comunicacion
                </a>
                <a href="eventos.php" class="nav-item">
                    <i class="ph ph-calendar-blank nav-icon"></i>
                    Eventos
                </a>
                <a href="documentos.php" class="nav-item">
                    <i class="ph ph-file-doc nav-icon"></i>
                    Documentos
                </a>
                <a href="noticias.php" class="nav-item">
                    <i class="ph ph-newspaper nav-icon"></i>
                    Noticias
                </a>
                <a href="votaciones.php" class="nav-item">
                    <i class="ph ph-check-square nav-icon"></i>
                    Votaciones
                </a>
                <a href="crm.php" class="nav-item">
                    <i class="ph ph-briefcase nav-icon"></i>
                    CRM
                </a>
                <a href="finanzas.php" class="nav-item">
                    <i class="ph ph-currency-dollar nav-icon"></i>
                    Finanzas
                </a>
                <a href="patrocinadores.php" class="nav-item">
                    <i class="ph ph-medal nav-icon"></i>
                    Patrocinadores
                </a>
                <a href="portal.php" class="nav-item">
                    <i class="ph ph-user-circle nav-icon"></i>
                    Portal del Socio
                </a>
                <a href="reportes.php" class="nav-item">
                    <i class="ph ph-chart-bar nav-icon"></i>
                    Reportes
                </a>
                <a href="auditoria.php" class="nav-item">
                    <i class="ph ph-shield-check nav-icon"></i>
                    Auditoria
                </a>
            </nav>
        </aside>

        <div class="main-content">
            <header class="top-header">
                <form class="search-bar" action="socios.php" method="get">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Buscar socios, eventos, documentos...">
                </form>

                <div class="header-actions">
                    <a href="solicitudes.php" class="header-icon-btn" aria-label="Solicitudes pendientes">
                        <i class="ph ph-bell"></i>
                        <span class="notification-dot"></span>
                    </a>
                    <a href="usuarios.php" class="header-icon-btn" aria-label="Configuracion">
                        <i class="ph ph-gear"></i>
                    </a>

                    <div class="user-profile-wrapper">
                        <div class="user-profile" id="userProfileBtn">
                            <img src="https://i.pravatar.cc/150?img=11" alt="<?php echo h(current_user_name()); ?>" class="avatar">
                            <div class="user-info">
                                <span class="user-name"><?php echo h(current_user_name()); ?> <i class="ph ph-caret-down"></i></span>
                                <span class="user-role"><?php echo h(current_user_role_label()); ?></span>
                            </div>
                        </div>

                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">
                                <strong><?php echo h(current_user_name()); ?></strong>
                                <span><?php echo h(current_user_email()); ?></span>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="usuarios.php" class="dropdown-item">
                                <i class="ph ph-user-circle"></i> Mi Perfil
                            </a>
                            <a href="usuarios.php" class="dropdown-item">
                                <i class="ph ph-gear"></i> Configuracion
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item text-danger">
                                <i class="ph ph-sign-out"></i> Cerrar Sesion
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard-scrollable">
                <div class="dashboard-wrapper">
                    <div class="dashboard-header">
                        <div>
                            <h1>Dashboard</h1>
                            <p>Bienvenido al panel de control de AMUVIE</p>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <a href="socios.php" class="card stat-card stat-card-link">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon blue"><i class="ph ph-users"></i></div>
                                <div class="stat-trend <?php echo trend_class($metrics['trend_total']); ?>">
                                    <i class="ph <?php echo trend_icon($metrics['trend_total']); ?>"></i>
                                    <?php echo number_format(abs($metrics['trend_total']), 1); ?>%
                                </div>
                            </div>
                            <div class="stat-label">Total Socios</div>
                            <div class="stat-value"><?php echo (int) $metrics['total']; ?></div>
                        </a>

                        <a href="socios.php?status=Activo" class="card stat-card stat-card-link">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon green"><i class="ph ph-user-check"></i></div>
                                <div class="stat-trend <?php echo trend_class($metrics['trend_active']); ?>">
                                    <i class="ph <?php echo trend_icon($metrics['trend_active']); ?>"></i>
                                    <?php echo number_format(abs($metrics['trend_active']), 1); ?>%
                                </div>
                            </div>
                            <div class="stat-label">Socios Activos</div>
                            <div class="stat-value"><?php echo (int) $metrics['active']; ?></div>
                        </a>

                        <a href="socios.php?status=Vencido" class="card stat-card stat-card-link">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon red"><i class="ph ph-user-minus"></i></div>
                                <div class="stat-trend <?php echo trend_class($metrics['trend_expired']); ?>">
                                    <i class="ph <?php echo trend_icon($metrics['trend_expired']); ?>"></i>
                                    <?php echo number_format(abs($metrics['trend_expired']), 1); ?>%
                                </div>
                            </div>
                            <div class="stat-label">Socios Vencidos</div>
                            <div class="stat-value"><?php echo (int) $metrics['expired']; ?></div>
                        </a>

                        <a href="finanzas.php" class="card stat-card stat-card-link">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon purple"><i class="ph ph-currency-dollar"></i></div>
                                <div class="stat-trend <?php echo trend_class($metrics['trend_revenue']); ?>">
                                    <i class="ph <?php echo trend_icon($metrics['trend_revenue']); ?>"></i>
                                    <?php echo number_format(abs($metrics['trend_revenue']), 1); ?>%
                                </div>
                            </div>
                            <div class="stat-label">Ingresos del Mes</div>
                            <div class="stat-value"><?php echo h(format_compact_currency($metrics['monthly_revenue'])); ?></div>
                        </a>
                    </div>

                    <div class="charts-grid">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Crecimiento de Socios</h3>
                            </div>
                            <div style="height: 300px; width: 100%;">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Ingresos Mensuales</h3>
                            </div>
                            <div style="height: 300px; width: 100%;">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="lists-grid">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actividad Reciente</h3>
                                <a href="socios.php" class="card-link">Ver todo</a>
                            </div>
                            <?php if ($activities === []): ?>
                                <div class="inline-empty-state">No hay actividad registrada todavia.</div>
                            <?php else: ?>
                                <ul class="activity-list">
                                    <?php foreach ($activities as $activity): ?>
                                        <li class="activity-item">
                                            <div class="activity-dot"></div>
                                            <div class="activity-content">
                                                <p><?php echo h($activity['descripcion']); ?></p>
                                                <span class="activity-time"><?php echo h(format_datetime_display($activity['created_at'])); ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Proximos Eventos</h3>
                                <a href="eventos.php" class="card-link">Ver todos &rarr;</a>
                            </div>
                            <?php if ($events === []): ?>
                                <div class="inline-empty-state">No hay eventos proximos configurados.</div>
                            <?php else: ?>
                                <div class="events-list">
                                    <?php foreach ($events as $event): ?>
                                        <?php
                                        $eventDate = new DateTimeImmutable($event['fecha_inicio']);
                                        $eventMonth = strtoupper(month_name_short((int) $eventDate->format('n')));
                                        ?>
                                        <div class="event-item">
                                            <div class="event-date">
                                                <span><?php echo h($eventMonth); ?></span>
                                                <span><?php echo $eventDate->format('d'); ?></span>
                                            </div>
                                            <div class="event-content">
                                                <h4><?php echo h($event['titulo']); ?></h4>
                                                <p><?php echo h($event['ubicacion']); ?></p>
                                                <p><?php echo (int) $event['registrados']; ?>/<?php echo (int) $event['capacidad']; ?> registrados</p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h3 class="card-title section-title-spacer">Acciones Rapidas</h3>
                    <div class="actions-grid">
                        <a href="socios.php?action=new" class="card action-card">
                            <i class="ph ph-users action-icon"></i>
                            <span class="action-label">Agregar Socio</span>
                        </a>

                        <a href="eventos.php" class="card action-card">
                            <i class="ph ph-calendar-plus action-icon"></i>
                            <span class="action-label">Crear Evento</span>
                        </a>

                        <a href="comunicacion.php" class="card action-card">
                            <i class="ph ph-file-text action-icon"></i>
                            <span class="action-label">Enviar Comunicado</span>
                        </a>

                        <a href="reportes.php" class="card action-card">
                            <i class="ph ph-trend-up action-icon"></i>
                            <span class="action-label">Ver Reportes</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dashboardLabels = <?php echo json_encode($series['labels']); ?>;
        const dashboardMembers = <?php echo json_encode(array_map('intval', $series['membership_counts'])); ?>;
        const dashboardRevenue = <?php echo json_encode(array_map('floatval', $series['revenue_totals'])); ?>;

        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: dashboardLabels,
                datasets: [{
                    label: 'Socios',
                    data: dashboardMembers,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#111827',
                        bodyColor: '#111827',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#6b7280', precision: 0 },
                        grid: { borderDash: [4, 4], color: '#e5e7eb' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' },
                        border: { display: false }
                    }
                }
            }
        });

        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: dashboardLabels,
                datasets: [{
                    label: 'Ingresos',
                    data: dashboardRevenue,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 6,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#111827',
                        bodyColor: '#8b5cf6',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return 'Ingreso: $' + new Intl.NumberFormat('es-MX').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#6b7280',
                            callback: function (value) {
                                return '$' + new Intl.NumberFormat('es-MX').format(value);
                            }
                        },
                        grid: { borderDash: [4, 4], color: '#e5e7eb' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' },
                        border: { display: false }
                    }
                }
            }
        });

        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        userProfileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !userProfileBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
