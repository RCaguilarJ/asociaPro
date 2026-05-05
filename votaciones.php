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

function redirect_to_votes(array $params = []): void
{
    $query = build_query_string($params);
    header('Location: votaciones.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['form_action'] ?? '') === 'save_vote')) {
    $formData = normalize_voting_form($_POST);
    $errors = validate_voting_form($formData);

    if ($errors !== []) {
        store_form_state($formData, $errors);
        flash_message('error', 'Corrige los datos de la votacion para continuar.');
        redirect_to_votes(['action' => 'new-vote']);
    }

    $voteId = save_vote($pdo, $formData);
    flash_message('success', 'La votacion se creo correctamente.');
    redirect_to_votes(['highlight' => $voteId]);
}

if (isset($_GET['download'])) {
    $voteId = (int) $_GET['download'];
    $vote = find_vote_by_id($pdo, $voteId);
    if (!$vote) {
        flash_message('error', 'No se encontro la votacion solicitada.');
        redirect_to_votes();
    }

    output_vote_minutes_response($vote);
}

$flash = pull_flash_message();
$formState = pull_form_state();
$votes = fetch_votes($pdo);
$selectedVoteId = (int) ($_GET['id'] ?? 0);
$selectedVote = $selectedVoteId > 0 ? find_vote_by_id($pdo, $selectedVoteId) : null;
$showCreateModal = (($_GET['action'] ?? '') === 'new-vote') || !empty($formState['data']);
$showDetailsModal = in_array(($_GET['action'] ?? ''), ['view-vote', 'live-vote'], true) && $selectedVote !== null;
$isLiveView = (($_GET['action'] ?? '') === 'live-vote');
$showAnyModal = $showCreateModal || $showDetailsModal;
$highlightVoteId = (int) ($_GET['highlight'] ?? 0);
$formData = array_merge(voting_form_defaults(), $formState['data'] ?? []);
$formErrors = $formState['errors'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asambleas y Votaciones - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="<?php echo $showAnyModal ? 'modal-open' : ''; ?>">
    <div class="app-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-icon"><i class="ph ph-buildings"></i></div>
                <div class="sidebar-brand">
                    <h2>AsociaPro</h2>
                    <p>AMUVIE</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item"><i class="ph ph-squares-four nav-icon"></i>Dashboard</a>
                <a href="socios.php" class="nav-item"><i class="ph ph-users nav-icon"></i>Socios</a>
                <a href="usuarios.php" class="nav-item"><i class="ph ph-user-gear nav-icon"></i>Usuarios y Roles</a>
                <a href="solicitudes.php" class="nav-item"><i class="ph ph-file-text nav-icon"></i>Solicitudes<?php if ($pendingRequests > 0): ?><span class="badge"><?php echo h(request_badge_label($pendingRequests)); ?></span><?php endif; ?></a>
                <a href="pagos.php" class="nav-item"><i class="ph ph-credit-card nav-icon"></i>Pagos y Cuotas</a>
                <a href="directorio.php" class="nav-item"><i class="ph ph-book-open nav-icon"></i>Directorio</a>
                <a href="comunicacion.php" class="nav-item"><i class="ph ph-paper-plane-tilt nav-icon"></i>Comunicacion</a>
                <a href="eventos.php" class="nav-item"><i class="ph ph-calendar-blank nav-icon"></i>Eventos</a>
                <a href="documentos.php" class="nav-item"><i class="ph ph-file-doc nav-icon"></i>Documentos</a>
                <a href="noticias.php" class="nav-item"><i class="ph ph-newspaper nav-icon"></i>Noticias</a>
                <a href="votaciones.php" class="nav-item active"><i class="ph ph-check-square nav-icon"></i>Votaciones</a>
                <a href="crm.php" class="nav-item"><i class="ph ph-briefcase nav-icon"></i>CRM</a>
                <a href="finanzas.php" class="nav-item"><i class="ph ph-currency-dollar nav-icon"></i>Finanzas</a>
                <a href="patrocinadores.php" class="nav-item"><i class="ph ph-medal nav-icon"></i>Patrocinadores</a>
                <a href="portal.php" class="nav-item"><i class="ph ph-user-circle nav-icon"></i>Portal del Socio</a>
                <a href="reportes.php" class="nav-item"><i class="ph ph-chart-bar nav-icon"></i>Reportes</a>
                <a href="auditoria.php" class="nav-item"><i class="ph ph-shield-check nav-icon"></i>Auditoria</a>
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
                    <a href="finanzas.php" class="header-icon-btn" aria-label="Configuracion"><i class="ph ph-gear"></i></a>
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
                            <a href="usuarios.php" class="dropdown-item"><i class="ph ph-user-circle"></i> Mi Perfil</a>
                            <a href="finanzas.php" class="dropdown-item"><i class="ph ph-gear"></i> Configuracion</a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item text-danger"><i class="ph ph-sign-out"></i> Cerrar Sesion</a>
                        </div>
                    </div>
                </div>
            </header>

            <div class="dashboard-scrollable">
                <div class="dashboard-wrapper">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Asambleas y Votaciones</h1>
                            <p>Gestion democratica y participacion</p>
                        </div>
                        <a href="votaciones.php?action=new-vote" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Nueva Votacion
                        </a>
                    </div>

                    <div class="votaciones-list">
                        <?php if ($votes === []): ?>
                            <div class="votacion-card no-results">
                                <div class="empty-state">
                                    <i class="ph ph-check-square-offset"></i>
                                    <h3>Sin votaciones programadas</h3>
                                    <p>Crea la primera votacion para abrir participacion a tus socios.</p>
                                    <a href="votaciones.php?action=new-vote" class="btn btn-primary">Nueva votacion</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($votes as $vote): ?>
                                <?php $noResultsClass = $vote['participacion_total'] === 0 ? ' no-results' : ''; ?>
                                <?php $highlightClass = ((int) $vote['id'] === $highlightVoteId) ? ' is-highlighted-row-card' : ''; ?>
                                <div class="votacion-card<?php echo $noResultsClass . $highlightClass; ?>">
                                    <div class="votacion-header">
                                        <div class="votacion-title-group">
                                            <div class="votacion-icon"><i class="ph ph-check-square-offset"></i></div>
                                            <div class="votacion-info">
                                                <h3><?php echo h($vote['titulo']); ?></h3>
                                                <p><?php echo h($vote['descripcion']); ?></p>
                                            </div>
                                        </div>
                                        <span class="badge-status <?php echo h($vote['status_badge_class']); ?>"><?php echo h($vote['status_label']); ?></span>
                                    </div>

                                    <div class="votacion-stats-grid">
                                        <div class="votacion-stat">
                                            <span class="votacion-stat-label"><i class="ph ph-calendar-blank"></i> Inicia</span>
                                            <span class="votacion-stat-value"><?php echo h(format_short_date($vote['fecha_inicio'])); ?></span>
                                        </div>
                                        <div class="votacion-stat">
                                            <span class="votacion-stat-label"><i class="ph ph-calendar-blank"></i> Finaliza</span>
                                            <span class="votacion-stat-value"><?php echo h(format_short_date($vote['fecha_cierre'])); ?></span>
                                        </div>
                                        <div class="votacion-stat">
                                            <span class="votacion-stat-label"><i class="ph ph-users"></i> Participacion</span>
                                            <span class="votacion-stat-value"><?php echo h((string) $vote['participacion_total']); ?> / <?php echo h((string) $vote['total_elegibles']); ?></span>
                                        </div>
                                        <div class="votacion-stat">
                                            <span class="votacion-stat-label"><i class="ph ph-chart-bar"></i> Porcentaje</span>
                                            <span class="votacion-stat-value"><?php echo h((string) $vote['participacion_porcentaje']); ?>%</span>
                                        </div>
                                    </div>

                                    <?php if ($vote['participacion_total'] > 0): ?>
                                        <div class="votacion-results">
                                            <div class="votacion-results-title">Resultados parciales</div>

                                            <div class="result-row">
                                                <div class="result-labels">
                                                    <span class="result-label">A favor</span>
                                                    <span class="result-value"><?php echo h((string) $vote['votos_favor']); ?> votos</span>
                                                </div>
                                                <div class="result-track"><div class="result-fill green" style="width: <?php echo (int) $vote['favor_porcentaje']; ?>%;"></div></div>
                                            </div>

                                            <div class="result-row">
                                                <div class="result-labels">
                                                    <span class="result-label">En contra</span>
                                                    <span class="result-value"><?php echo h((string) $vote['votos_contra']); ?> votos</span>
                                                </div>
                                                <div class="result-track"><div class="result-fill red" style="width: <?php echo (int) $vote['contra_porcentaje']; ?>%;"></div></div>
                                            </div>

                                            <div class="result-row">
                                                <div class="result-labels">
                                                    <span class="result-label">Abstencion</span>
                                                    <span class="result-value"><?php echo h((string) $vote['abstenciones']); ?> votos</span>
                                                </div>
                                                <div class="result-track"><div class="result-fill gray" style="width: <?php echo (int) $vote['abstencion_porcentaje']; ?>%;"></div></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="votacion-actions">
                                        <a href="votaciones.php?action=view-vote&id=<?php echo (int) $vote['id']; ?>" class="btn-votacion">Ver detalles</a>
                                        <a href="votaciones.php?download=<?php echo (int) $vote['id']; ?>" class="btn-votacion">Descargar acta</a>
                                        <?php if ($vote['status_label'] === 'En curso'): ?>
                                            <a href="votaciones.php?action=live-vote&id=<?php echo (int) $vote['id']; ?>" class="btn-votacion btn-votacion-primary">Ver en vivo</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($flash): ?>
        <div class="toast-notification <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-x-circle' : 'ph-check-circle'; ?>"></i>
            <span><?php echo h($flash['message']); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($showCreateModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal">
                <div class="modal-header">
                    <h2>Crear Nueva Votacion</h2>
                    <a href="votaciones.php" class="modal-close" aria-label="Cerrar"><i class="ph ph-x"></i></a>
                </div>

                <form method="post" class="member-form">
                    <input type="hidden" name="form_action" value="save_vote">

                    <?php if ($formErrors !== []): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Corrige los campos marcados para guardar la votacion.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group full-width">
                            <label for="titulo">Titulo de la votacion *</label>
                            <input id="titulo" name="titulo" type="text" value="<?php echo h($formData['titulo']); ?>" placeholder="Ej: Aprobacion de nuevos estatutos 2024">
                            <?php if (isset($formErrors['titulo'])): ?><span class="form-error"><?php echo h($formErrors['titulo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="descripcion">Descripcion *</label>
                            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Describe el tema a votar y su importancia..."><?php echo h($formData['descripcion']); ?></textarea>
                            <?php if (isset($formErrors['descripcion'])): ?><span class="form-error"><?php echo h($formErrors['descripcion']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de inicio *</label>
                            <input id="fecha_inicio" name="fecha_inicio" type="date" value="<?php echo h($formData['fecha_inicio']); ?>">
                            <?php if (isset($formErrors['fecha_inicio'])): ?><span class="form-error"><?php echo h($formErrors['fecha_inicio']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="fecha_cierre">Fecha de cierre *</label>
                            <input id="fecha_cierre" name="fecha_cierre" type="date" value="<?php echo h($formData['fecha_cierre']); ?>">
                            <?php if (isset($formErrors['fecha_cierre'])): ?><span class="form-error"><?php echo h($formErrors['fecha_cierre']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="inline-note-box">
                        <strong>Nota:</strong> La votacion estara disponible solo para socios activos durante el periodo seleccionado.
                    </div>

                    <div class="modal-footer">
                        <a href="votaciones.php" class="btn btn-outline"><i class="ph ph-x"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="ph ph-floppy-disk"></i> Crear Votacion</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showDetailsModal && $selectedVote): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card request-details-modal">
                <div class="modal-header">
                    <h2><?php echo $isLiveView ? 'Resultados en vivo' : 'Detalle de votacion'; ?></h2>
                    <a href="votaciones.php" class="modal-close" aria-label="Cerrar"><i class="ph ph-x"></i></a>
                </div>

                <div class="request-details-body">
                    <div class="request-detail-hero">
                        <div class="votacion-icon"><i class="ph ph-check-square-offset"></i></div>
                        <div>
                            <h3><?php echo h($selectedVote['titulo']); ?></h3>
                            <p><?php echo h($selectedVote['descripcion']); ?></p>
                        </div>
                    </div>

                    <div class="request-details-grid">
                        <div class="request-detail-item">
                            <span class="request-detail-label">Periodo</span>
                            <strong><?php echo h(format_short_date($selectedVote['fecha_inicio'])); ?> al <?php echo h(format_short_date($selectedVote['fecha_cierre'])); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span class="request-detail-label">Estado</span>
                            <strong><?php echo h($selectedVote['status_label']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span class="request-detail-label">Participacion</span>
                            <strong><?php echo h((string) $selectedVote['participacion_total']); ?> / <?php echo h((string) $selectedVote['total_elegibles']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span class="request-detail-label">Porcentaje</span>
                            <strong><?php echo h((string) $selectedVote['participacion_porcentaje']); ?>%</strong>
                        </div>
                    </div>

                    <div class="votacion-results">
                        <div class="votacion-results-title"><?php echo $isLiveView ? 'Conteo en vivo' : 'Resultados'; ?></div>
                        <div class="result-row">
                            <div class="result-labels"><span class="result-label">A favor</span><span class="result-value"><?php echo h((string) $selectedVote['votos_favor']); ?> votos</span></div>
                            <div class="result-track"><div class="result-fill green" style="width: <?php echo (int) $selectedVote['favor_porcentaje']; ?>%;"></div></div>
                        </div>
                        <div class="result-row">
                            <div class="result-labels"><span class="result-label">En contra</span><span class="result-value"><?php echo h((string) $selectedVote['votos_contra']); ?> votos</span></div>
                            <div class="result-track"><div class="result-fill red" style="width: <?php echo (int) $selectedVote['contra_porcentaje']; ?>%;"></div></div>
                        </div>
                        <div class="result-row">
                            <div class="result-labels"><span class="result-label">Abstencion</span><span class="result-value"><?php echo h((string) $selectedVote['abstenciones']); ?> votos</span></div>
                            <div class="result-track"><div class="result-fill gray" style="width: <?php echo (int) $selectedVote['abstencion_porcentaje']; ?>%;"></div></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="votaciones.php" class="btn btn-outline">Cerrar</a>
                    <a href="votaciones.php?download=<?php echo (int) $selectedVote['id']; ?>" class="btn btn-primary"><i class="ph ph-download-simple"></i> Descargar acta</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (userProfileBtn && profileDropdown) {
            userProfileBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function (event) {
                if (!profileDropdown.contains(event.target) && !userProfileBtn.contains(event.target)) {
                    profileDropdown.classList.remove('show');
                }
            });
        }
    </script>
</body>
</html>
