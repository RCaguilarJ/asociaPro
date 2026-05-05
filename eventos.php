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
$filters = event_filters_from_request($_GET);

function redirect_to_events(array $params = []): void
{
    $query = build_query_string($params);
    header('Location: eventos.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['form_action'] ?? '') === 'save_event')) {
    $formData = normalize_event_form($_POST);
    $errors = validate_event_form($formData);
    $returnFilters = event_filters_from_request($_POST);

    if ($errors !== []) {
        store_form_state($formData, $errors);
        flash_message('error', 'Corrige los errores del evento para continuar.');
        redirect_to_events(array_merge($returnFilters, ['action' => 'new-event']));
    }

    $eventId = save_event($pdo, $formData);
    flash_message('success', 'El evento se creo correctamente.');
    redirect_to_events(array_merge($returnFilters, ['highlight' => $eventId]));
}

$flash = pull_flash_message();
$formState = pull_form_state();
$events = fetch_events($pdo, $filters);
$showCreateModal = (($_GET['action'] ?? '') === 'new-event') || !empty($formState['data']);
$highlightEventId = (int) ($_GET['highlight'] ?? 0);
$formData = array_merge(event_form_defaults(), $formState['data'] ?? []);
$formErrors = $formState['errors'] ?? [];
$eventsSummary = count($events) . ' evento' . (count($events) === 1 ? '' : 's') . ' programado' . (count($events) === 1 ? '' : 's');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos y Capacitaciones - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="<?php echo $showCreateModal ? 'modal-open' : ''; ?>">
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
                <a href="directorio.php" class="nav-item">
                    <i class="ph ph-book-open nav-icon"></i>
                    Directorio
                </a>
                <a href="comunicacion.php" class="nav-item">
                    <i class="ph ph-paper-plane-tilt nav-icon"></i>
                    Comunicacion
                </a>
                <a href="eventos.php" class="nav-item active">
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
                            <h1>Eventos y Capacitaciones</h1>
                            <p><?php echo h($eventsSummary); ?></p>
                        </div>
                        <a href="eventos.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'new-event']))); ?>" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Crear Evento
                        </a>
                    </div>

                    <?php if ($flash): ?>
                        <div class="page-alert <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-warning-circle' : 'ph-check-circle'; ?>"></i>
                            <span><?php echo h($flash['message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="get" class="filters-bar">
                        <div class="search-input-wrapper" style="flex: 1;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" name="search" value="<?php echo h($filters['search']); ?>" placeholder="Buscar eventos...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select" name="type" onchange="this.form.submit()">
                                <option value="">Todos los tipos</option>
                                <?php foreach (event_type_options() as $type): ?>
                                    <option value="<?php echo h($type); ?>" <?php echo $filters['type'] === $type ? 'selected' : ''; ?>><?php echo h($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline" type="submit">
                                <i class="ph ph-funnel"></i> Filtros
                            </button>
                        </div>
                    </form>

                    <div class="eventos-grid">
                        <?php if ($events === []): ?>
                            <div class="empty-state event-empty-state">
                                <i class="ph ph-calendar-plus"></i>
                                <h3>Sin eventos</h3>
                                <p>No se encontraron eventos con esos filtros.</p>
                                <a href="eventos.php?action=new-event" class="btn btn-primary">Crear evento</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                                <?php
                                $progress = max(0, min(100, (int) ($event['ocupacion'] ?? 0)));
                                $rowClass = ((int) $event['id'] === $highlightEventId) ? 'is-highlighted-row-card' : '';
                                ?>
                                <div class="evento-card <?php echo $rowClass; ?>">
                                    <div class="evento-image-wrapper">
                                        <img src="<?php echo h($event['imagen_url'] ?: event_image_for_type((string) $event['tipo'])); ?>" alt="<?php echo h($event['titulo']); ?>">
                                        <span class="badge-proximo">Proximo</span>
                                    </div>
                                    <div class="evento-body">
                                        <div class="evento-title-row">
                                            <h3><?php echo h($event['titulo']); ?></h3>
                                            <span class="badge-tipo"><?php echo h($event['tipo']); ?></span>
                                        </div>
                                        <p class="evento-desc"><?php echo h($event['descripcion'] ?: 'Evento programado para la comunidad AMUVIE.'); ?></p>
                                        <div class="evento-details">
                                            <div class="evento-detail-item">
                                                <i class="ph ph-calendar-blank"></i>
                                                <span><?php echo h(format_event_datetime($event['fecha_inicio'], $event['hora_inicio'] ?: '09:00:00')); ?></span>
                                            </div>
                                            <div class="evento-detail-item">
                                                <i class="ph ph-map-pin"></i>
                                                <span><?php echo h($event['ubicacion']); ?></span>
                                            </div>
                                            <div class="evento-detail-item">
                                                <i class="ph ph-users"></i>
                                                <span><?php echo h($event['registrados'] . ' / ' . $event['capacidad'] . ' registrados'); ?></span>
                                            </div>
                                            <?php if ((float) $event['precio'] > 0): ?>
                                                <div class="evento-detail-item">
                                                    <i class="ph ph-currency-dollar"></i>
                                                    <span><?php echo h(format_currency((float) $event['precio'])); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="evento-progress-container">
                                            <div class="progress-track">
                                                <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
                                            </div>
                                            <span class="progress-text"><?php echo h((string) $progress); ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($showCreateModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal">
                <div class="modal-header">
                    <h2>Crear Nuevo Evento</h2>
                    <a href="eventos.php<?php echo build_query_string($filters) !== '' ? '?' . h(build_query_string($filters)) : ''; ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="modal-form">
                    <input type="hidden" name="form_action" value="save_event">
                    <input type="hidden" name="search" value="<?php echo h($filters['search']); ?>">
                    <input type="hidden" name="type" value="<?php echo h($filters['type']); ?>">
                    <input type="hidden" name="modalidad" value="Presencial">

                    <?php if (!empty($formErrors)): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Hay campos pendientes o invalidos. Revisa el formulario.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group full-width">
                            <label for="titulo">Nombre del evento *</label>
                            <input id="titulo" name="titulo" type="text" value="<?php echo h($formData['titulo']); ?>" placeholder="Ej: Workshop de Innovacion Digital">
                            <?php if (isset($formErrors['titulo'])): ?><span class="field-error"><?php echo h($formErrors['titulo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha *</label>
                            <input id="fecha_inicio" name="fecha_inicio" type="date" value="<?php echo h($formData['fecha_inicio']); ?>">
                            <?php if (isset($formErrors['fecha_inicio'])): ?><span class="field-error"><?php echo h($formErrors['fecha_inicio']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">Hora *</label>
                            <input id="hora_inicio" name="hora_inicio" type="time" value="<?php echo h($formData['hora_inicio']); ?>">
                            <?php if (isset($formErrors['hora_inicio'])): ?><span class="field-error"><?php echo h($formErrors['hora_inicio']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="ubicacion">Ubicacion *</label>
                            <input id="ubicacion" name="ubicacion" type="text" value="<?php echo h($formData['ubicacion']); ?>" placeholder="Ej: Centro de Convenciones o Virtual - Zoom">
                            <?php if (isset($formErrors['ubicacion'])): ?><span class="field-error"><?php echo h($formErrors['ubicacion']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo *</label>
                            <select id="tipo" name="tipo">
                                <?php foreach (event_type_options() as $type): ?>
                                    <option value="<?php echo h($type); ?>" <?php echo $formData['tipo'] === $type ? 'selected' : ''; ?>><?php echo h($type); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['tipo'])): ?><span class="field-error"><?php echo h($formErrors['tipo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="capacidad">Capacidad *</label>
                            <input id="capacidad" name="capacidad" type="number" min="1" value="<?php echo h($formData['capacidad']); ?>">
                            <?php if (isset($formErrors['capacidad'])): ?><span class="field-error"><?php echo h($formErrors['capacidad']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio (MXN)</label>
                            <input id="precio" name="precio" type="number" min="0" step="0.01" value="<?php echo h($formData['precio']); ?>">
                            <?php if (isset($formErrors['precio'])): ?><span class="field-error"><?php echo h($formErrors['precio']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="descripcion">Descripcion *</label>
                            <textarea id="descripcion" name="descripcion" rows="5" placeholder="Describe el evento, objetivos y beneficios para los asistentes..."><?php echo h($formData['descripcion']); ?></textarea>
                            <?php if (isset($formErrors['descripcion'])): ?><span class="field-error"><?php echo h($formErrors['descripcion']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="eventos.php<?php echo build_query_string($filters) !== '' ? '?' . h(build_query_string($filters)) : ''; ?>" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk"></i> Crear Evento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

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
