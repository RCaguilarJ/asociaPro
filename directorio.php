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
$pendingRequests = count_pending_requests($pdo);
$directoryFilters = [
    'search' => trim((string) ($_GET['search'] ?? '')),
    'industria' => trim((string) ($_GET['industria'] ?? '')),
    'view' => in_array(($_GET['view'] ?? 'grid'), ['grid', 'list'], true) ? (string) $_GET['view'] : 'grid',
];

$members = fetch_members($pdo, [
    'search' => $directoryFilters['search'],
    'status' => 'Activo',
    'sort' => 'alpha',
    'tipo' => '',
    'estado' => '',
    'industria' => $directoryFilters['industria'],
]);
$industryOptions = industry_options();
$directorySummary = count($members) . ' socio' . (count($members) === 1 ? '' : 's') . ' activo' . (count($members) === 1 ? '' : 's');
if ($directoryFilters['industria'] !== '') {
    $directorySummary .= ' en ' . $directoryFilters['industria'];
}

function membership_badge_style(string $membershipType): string
{
    return match ($membershipType) {
        'Corporativa' => 'background-color: #ede9fe; color: #7c3aed;',
        'Basica' => 'background-color: #f1f5f9; color: #475569;',
        default => 'background-color: #eff6ff; color: #2563eb;',
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de Socios - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
                <a href="dashboard.php" class="nav-item">
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
                    <?php if ($pendingRequests > 0): ?><span class="badge"><?php echo h(request_badge_label($pendingRequests)); ?></span><?php endif; ?>
                </a>
                <a href="pagos.php" class="nav-item">
                    <i class="ph ph-credit-card nav-icon"></i>
                    Pagos y Cuotas
                </a>
                <a href="directorio.php" class="nav-item active">
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
                        <?php if ($pendingRequests > 0): ?><span class="notification-dot"></span><?php endif; ?>
                    </a>
                    <a href="finanzas.php" class="header-icon-btn" aria-label="Configuracion">
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
                            <a href="finanzas.php" class="dropdown-item">
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
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Directorio de Socios</h1>
                            <p>Directorio publico de miembros activos</p>
                        </div>
                    </div>

                    <form method="get" class="filters-bar">
                        <input type="hidden" name="view" value="<?php echo h($directoryFilters['view']); ?>">
                        <div class="search-input-wrapper">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" name="search" value="<?php echo h($directoryFilters['search']); ?>" placeholder="Buscar por nombre, empresa o ciudad...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select" name="industria" onchange="this.form.submit()">
                                <option value="">Todas las industrias</option>
                                <?php foreach ($industryOptions as $industry): ?>
                                    <option value="<?php echo h($industry); ?>" <?php echo $directoryFilters['industria'] === $industry ? 'selected' : ''; ?>><?php echo h($industry); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <div class="view-toggle">
                                <a href="directorio.php?<?php echo h(build_query_string(['search' => $directoryFilters['search'], 'industria' => $directoryFilters['industria'], 'view' => 'grid'])); ?>" class="view-toggle-btn <?php echo $directoryFilters['view'] === 'grid' ? 'active' : ''; ?>">Grid</a>
                                <a href="directorio.php?<?php echo h(build_query_string(['search' => $directoryFilters['search'], 'industria' => $directoryFilters['industria'], 'view' => 'list'])); ?>" class="view-toggle-btn <?php echo $directoryFilters['view'] === 'list' ? 'active' : ''; ?>">Lista</a>
                            </div>
                        </div>
                    </form>

                    <div class="directory-summary">
                        <span><?php echo h($directorySummary); ?></span>
                    </div>

                    <?php if ($members === []): ?>
                        <div class="empty-state">
                            <i class="ph ph-address-book"></i>
                            <h3>Sin resultados</h3>
                            <p>No se encontraron socios activos con esos filtros.</p>
                            <a href="directorio.php" class="btn btn-primary">Limpiar filtros</a>
                        </div>
                    <?php else: ?>
                        <div class="socios-grid directory-results <?php echo $directoryFilters['view'] === 'list' ? 'list-view' : ''; ?>">
                            <?php foreach ($members as $member): ?>
                                <?php
                                $avatarUrl = trim((string) ($member['avatar_url'] ?? ''));
                                $phoneDigits = sanitize_phone_number((string) ($member['telefono'] ?? ''));
                                $canCall = $phoneDigits !== '';
                                $canWhatsapp = whatsapp_phone_number((string) ($member['telefono'] ?? '')) !== '';
                                $canEmail = trim((string) ($member['email'] ?? '')) !== '';
                                ?>
                                <div class="directorio-card">
                                    <div class="directorio-header">
                                        <?php if ($avatarUrl !== ''): ?>
                                            <img src="<?php echo h($avatarUrl); ?>" alt="<?php echo h($member['nombre_completo']); ?>" class="socio-avatar">
                                        <?php else: ?>
                                            <div class="avatar-initial"><?php echo h(request_initial($member['nombre_completo'])); ?></div>
                                        <?php endif; ?>

                                        <div class="directorio-info">
                                            <h3><?php echo h($member['nombre_completo']); ?></h3>
                                            <div class="directorio-job"><?php echo h($member['puesto'] ?: 'Socio activo'); ?></div>
                                            <span class="badge-membership" style="<?php echo membership_badge_style((string) $member['tipo_membresia']); ?>">
                                                <?php echo h($member['tipo_membresia']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="directorio-details">
                                        <div class="detail-item">
                                            <i class="ph ph-buildings"></i>
                                            <span><?php echo h($member['empresa']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="ph ph-globe"></i>
                                            <span><?php echo h($member['industria']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="ph ph-map-pin"></i>
                                            <span><?php echo h($member['ciudad'] . ', ' . $member['estado']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="ph ph-phone"></i>
                                            <span><?php echo h($member['telefono']); ?></span>
                                        </div>
                                    </div>

                                    <div class="directorio-actions">
                                        <a
                                            href="<?php echo $canEmail ? 'mailto:' . h((string) $member['email']) . '?subject=' . rawurlencode('Contacto desde el directorio de AMUVIE') : '#'; ?>"
                                            class="btn-contact email <?php echo !$canEmail ? 'is-disabled' : ''; ?>"
                                            <?php echo !$canEmail ? 'aria-disabled="true"' : ''; ?>
                                        >
                                            <i class="ph ph-envelope-simple"></i> Email
                                        </a>
                                        <a
                                            href="<?php echo $canWhatsapp ? h(member_whatsapp_url($member)) : '#'; ?>"
                                            class="btn-contact whatsapp <?php echo !$canWhatsapp ? 'is-disabled' : ''; ?>"
                                            <?php echo $canWhatsapp ? 'target="_blank" rel="noopener"' : 'aria-disabled="true"'; ?>
                                        >
                                            <i class="ph ph-whatsapp-logo"></i> WhatsApp
                                        </a>
                                        <a
                                            href="<?php echo $canCall ? 'tel:' . h($phoneDigits) : '#'; ?>"
                                            class="btn-contact call <?php echo !$canCall ? 'is-disabled' : ''; ?>"
                                            <?php echo !$canCall ? 'aria-disabled="true"' : ''; ?>
                                        >
                                            <i class="ph ph-phone"></i> Llamar
                                        </a>
                                        <a href="socios.php?action=edit&id=<?php echo (int) $member['id']; ?>" class="btn-contact profile">
                                            <i class="ph ph-user-circle"></i> Ver socio
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
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
