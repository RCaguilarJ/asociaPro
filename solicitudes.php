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

function redirect_to_requests(array $extra = []): void
{
    $query = build_query_string($extra);
    header('Location: solicitudes.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = (int) ($_POST['request_id'] ?? 0);
    $action = (string) ($_POST['request_action'] ?? '');

    try {
        if ($action === 'approve_request' && $requestId > 0) {
            $approved = approve_request($pdo, $requestId);
            flash_message('success', 'Solicitud de ' . $approved['request']['nombre_completo'] . ' aprobada correctamente');
        }

        if ($action === 'reject_request' && $requestId > 0) {
            $rejected = reject_request($pdo, $requestId);
            flash_message('error', 'Solicitud de ' . $rejected['nombre_completo'] . ' rechazada');
        }
    } catch (Throwable $exception) {
        flash_message('error', $exception->getMessage());
    }

    redirect_to_requests();
}

if (isset($_GET['download'])) {
    $request = find_request_by_id($pdo, (int) $_GET['download']);
    if (!$request) {
        flash_message('error', 'No se encontro la solicitud solicitada.');
        redirect_to_requests();
    }

    download_request_documents($request);
}

$pendingCount = count_pending_requests($pdo);
$pendingRequests = fetch_pending_requests($pdo);
$flash = pull_flash_message();
$selectedRequest = isset($_GET['view']) ? find_request_by_id($pdo, (int) $_GET['view']) : null;
$showDetailsModal = $selectedRequest !== null;
$requestCountLabel = $pendingCount . ' ' . ($pendingCount === 1 ? 'solicitud pendiente de revision' : 'solicitudes pendientes de revision');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes de Membresia - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="<?php echo $showDetailsModal ? 'modal-open' : ''; ?>">
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
                <a href="solicitudes.php" class="nav-item active">
                    <i class="ph ph-file-text nav-icon"></i>
                    Solicitudes
                    <?php if ($pendingCount > 0): ?><span class="badge"><?php echo h(request_badge_label($pendingCount)); ?></span><?php endif; ?>
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
                        <?php if ($pendingCount > 0): ?><span class="notification-dot"></span><?php endif; ?>
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
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Solicitudes de Membresia</h1>
                            <p><?php echo h($requestCountLabel); ?></p>
                        </div>
                    </div>

                    <?php if ($pendingRequests === []): ?>
                        <div class="requests-empty-card">
                            <div class="requests-empty-icon">
                                <i class="ph ph-check"></i>
                            </div>
                            <h3>Todo al dia</h3>
                            <p>No hay solicitudes pendientes de revision</p>
                        </div>
                    <?php else: ?>
                        <div class="solicitudes-list">
                            <?php foreach ($pendingRequests as $request): ?>
                                <div class="solicitud-card">
                                    <div class="solicitud-header">
                                        <div class="solicitud-profile">
                                            <div class="avatar-initial"><?php echo h(request_initial($request['nombre_completo'])); ?></div>
                                            <div class="solicitud-info">
                                                <h3><?php echo h($request['nombre_completo']); ?></h3>
                                                <p><?php echo h($request['puesto'] . ' en ' . $request['empresa']); ?></p>
                                            </div>
                                        </div>
                                        <span class="badge-warning">Pendiente</span>
                                    </div>

                                    <div class="solicitud-grid">
                                        <div class="solicitud-data-group">
                                            <span class="solicitud-label">Correo electronico</span>
                                            <span class="solicitud-value"><?php echo h($request['email']); ?></span>
                                        </div>
                                        <div class="solicitud-data-group">
                                            <span class="solicitud-label">Telefono</span>
                                            <span class="solicitud-value"><?php echo h($request['telefono']); ?></span>
                                        </div>
                                        <div class="solicitud-data-group">
                                            <span class="solicitud-label">Membresia solicitada</span>
                                            <span class="solicitud-value"><?php echo h($request['tipo_membresia']); ?></span>
                                        </div>
                                    </div>

                                    <div class="solicitud-docs">
                                        <span class="solicitud-label">Documentos adjuntos</span>
                                        <div class="doc-pills">
                                            <?php foreach ($request['documentos'] as $document): ?>
                                                <div class="doc-pill"><i class="ph ph-file-text"></i> <?php echo h($document['nombre']); ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <p class="solicitud-date">Solicitado el <?php echo h(format_request_submission_date($request['submitted_at'])); ?></p>

                                    <div class="solicitud-actions">
                                        <a href="solicitudes.php?view=<?php echo (int) $request['id']; ?>" class="btn-solicitud btn-outline-dark">
                                            <i class="ph ph-eye"></i> Ver detalles
                                        </a>
                                        <a href="solicitudes.php?download=<?php echo (int) $request['id']; ?>" class="btn-solicitud btn-outline-dark">
                                            <i class="ph ph-download-simple"></i> Descargar documentos
                                        </a>
                                        <form method="post" onsubmit="return confirm('Se rechazara esta solicitud. Deseas continuar?');">
                                            <input type="hidden" name="request_action" value="reject_request">
                                            <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                            <button type="submit" class="btn-solicitud btn-outline-danger">
                                                <i class="ph ph-x"></i> Rechazar
                                            </button>
                                        </form>
                                        <form method="post" onsubmit="return confirm('Al aprobar se creara el socio en el padron. Deseas continuar?');">
                                            <input type="hidden" name="request_action" value="approve_request">
                                            <input type="hidden" name="request_id" value="<?php echo (int) $request['id']; ?>">
                                            <button type="submit" class="btn-solicitud btn-success">
                                                <i class="ph ph-check"></i> Aprobar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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

    <?php if ($showDetailsModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card request-details-modal">
                <div class="modal-header">
                    <h2>Detalle de solicitud</h2>
                    <a href="solicitudes.php" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <div class="request-details-body">
                    <div class="request-detail-hero">
                        <div class="avatar-initial large"><?php echo h(request_initial($selectedRequest['nombre_completo'])); ?></div>
                        <div>
                            <h3><?php echo h($selectedRequest['nombre_completo']); ?></h3>
                            <p><?php echo h($selectedRequest['puesto'] . ' en ' . $selectedRequest['empresa']); ?></p>
                            <span class="badge-warning">Pendiente</span>
                        </div>
                    </div>

                    <div class="request-details-grid">
                        <div class="request-detail-item">
                            <span>Correo</span>
                            <strong><?php echo h($selectedRequest['email']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Telefono</span>
                            <strong><?php echo h($selectedRequest['telefono']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Ciudad</span>
                            <strong><?php echo h($selectedRequest['ciudad']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Estado</span>
                            <strong><?php echo h($selectedRequest['estado']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Industria</span>
                            <strong><?php echo h($selectedRequest['industria']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Membresia solicitada</span>
                            <strong><?php echo h($selectedRequest['tipo_membresia']); ?></strong>
                        </div>
                    </div>

                    <div class="request-notes-box">
                        <h4>Notas de la solicitud</h4>
                        <p><?php echo h($selectedRequest['notas'] ?: 'Sin notas adicionales.'); ?></p>
                    </div>

                    <div class="request-documents-box">
                        <h4>Documentos adjuntos</h4>
                        <div class="doc-pills">
                            <?php foreach ($selectedRequest['documentos'] as $document): ?>
                                <div class="doc-pill"><i class="ph ph-file-text"></i> <?php echo h($document['nombre']); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="solicitudes.php" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cerrar
                        </a>
                        <a href="solicitudes.php?download=<?php echo (int) $selectedRequest['id']; ?>" class="btn btn-outline">
                            <i class="ph ph-download-simple"></i> Descargar documentos
                        </a>
                    </div>
                </div>
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
