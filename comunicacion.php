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
$filters = communication_filters_from_request($_GET);

function redirect_to_communications(array $params = []): void
{
    $query = build_query_string($params);
    header('Location: comunicacion.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['form_action'] ?? '') === 'save_communication')) {
    $formData = normalize_communication_form($_POST);
    $errors = validate_communication_form($formData, $pdo);
    $returnFilters = communication_filters_from_request($_POST);
    $redirectState = $formData['id'] !== ''
        ? ['action' => 'edit-communication', 'id' => $formData['id']]
        : ['action' => 'new-communication'];

    if ($errors !== []) {
        store_form_state($formData, $errors);
        flash_message('error', 'Corrige los errores del comunicado para continuar.');
        redirect_to_communications(array_merge($returnFilters, $redirectState));
    }

    $communicationId = save_communication($pdo, $formData);
    flash_message(
        'success',
        $formData['id'] !== ''
            ? 'El comunicado se actualizo correctamente.'
            : ($formData['status'] === 'Enviado'
                ? 'Comunicado enviado correctamente.'
                : 'Borrador guardado correctamente.')
    );
    redirect_to_communications(array_merge($returnFilters, ['highlight' => $communicationId]));
}

$flash = pull_flash_message();
$formState = pull_form_state();
$communications = fetch_communications($pdo, $filters);
$highlightCommunicationId = (int) ($_GET['highlight'] ?? 0);
$selectedCommunication = isset($_GET['view']) ? find_communication_by_id($pdo, (int) $_GET['view']) : null;
$showDetailsModal = $selectedCommunication !== null;
$showComposerModal = false;
$composerMode = '';
$currentQuery = build_query_string($filters);
$formData = communication_form_defaults();
$formErrors = $formState['errors'] ?? [];

$action = (string) ($_GET['action'] ?? '');
if ($action === 'new-communication') {
    $showComposerModal = true;
    $composerMode = 'new';
} elseif ($action === 'edit-communication') {
    $record = find_communication_by_id($pdo, (int) ($_GET['id'] ?? 0));
    if ($record) {
        $formData = communication_form_data_from_record($record);
        $showComposerModal = true;
        $composerMode = 'edit';
    } else {
        flash_message('error', 'No se encontro el comunicado solicitado.');
        redirect_to_communications($filters);
    }
} elseif ($action === 'duplicate-communication') {
    $record = find_communication_by_id($pdo, (int) ($_GET['id'] ?? 0));
    if ($record) {
        $formData = communication_form_data_from_record($record);
        $formData['id'] = '';
        $formData['titulo'] = 'Copia de ' . $formData['titulo'];
        $showComposerModal = true;
        $composerMode = 'duplicate';
    } else {
        flash_message('error', 'No se encontro el comunicado solicitado para duplicar.');
        redirect_to_communications($filters);
    }
}

if (!empty($formState['data'])) {
    $formData = array_merge($formData, $formState['data']);
    $showComposerModal = true;
    if ($composerMode === '') {
        $composerMode = $formData['id'] !== '' ? 'edit' : 'new';
    }
}

$communicationCounts = [];
foreach (communication_audience_options() as $audienceOption) {
    $communicationCounts[$audienceOption] = count(communication_recipient_members($pdo, $audienceOption));
}

$detailRecipients = $selectedCommunication
    ? communication_recipients_preview($pdo, $selectedCommunication['audiencia'])
    : [];
$composerRecipientCount = $communicationCounts[$formData['audiencia']] ?? 0;
$composerTitle = match ($composerMode) {
    'edit' => 'Editar Comunicado',
    'duplicate' => 'Duplicar Comunicado',
    default => 'Nuevo Comunicado',
};
$composerButtonLabel = $formData['status'] === 'Borrador' ? 'Guardar borrador' : 'Guardar y enviar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicacion - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="<?php echo ($showComposerModal || $showDetailsModal) ? 'modal-open' : ''; ?>">
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
                <a href="comunicacion.php" class="nav-item active">
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
                            <h1>Comunicacion</h1>
                            <p>Envio de comunicados y mensajes masivos</p>
                        </div>
                        <a href="comunicacion.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'new-communication']))); ?>" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Nuevo Comunicado
                        </a>
                    </div>

                    <?php if ($flash): ?>
                        <div class="page-alert <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-warning-circle' : 'ph-check-circle'; ?>"></i>
                            <span><?php echo h($flash['message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="tabs-container">
                        <a href="comunicacion.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="tab-item <?php echo !$showComposerModal ? 'active' : ''; ?>">Comunicados enviados</a>
                        <a href="comunicacion.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'new-communication']))); ?>" class="tab-item <?php echo $showComposerModal ? 'active' : ''; ?>">Nuevo comunicado</a>
                    </div>

                    <div class="comunicacion-list">
                        <?php if ($communications === []): ?>
                            <div class="empty-state">
                                <i class="ph ph-chat-circle-text"></i>
                                <h3>Sin comunicados</h3>
                                <p>No hay resultados con esos filtros.</p>
                                <a href="comunicacion.php?action=new-communication" class="btn btn-primary">Crear comunicado</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($communications as $communication): ?>
                                <?php $rowClass = ((int) $communication['id'] === $highlightCommunicationId) ? 'is-highlighted-row-card' : ''; ?>
                                <div class="comunicado-card <?php echo $rowClass; ?>">
                                    <div class="comunicado-header">
                                        <div class="comunicado-title-group">
                                            <div class="comunicado-icon">
                                                <i class="ph <?php echo $communication['canal'] === 'WhatsApp' ? 'ph-whatsapp-logo' : ($communication['canal'] === 'Mixto' ? 'ph-megaphone-simple' : 'ph-envelope-simple'); ?>"></i>
                                            </div>
                                            <div class="comunicado-info">
                                                <h3><?php echo h($communication['titulo']); ?></h3>
                                                <p><?php echo h($communication['status'] === 'Enviado' && $communication['sent_at'] ? 'Enviado el ' . format_datetime_label($communication['sent_at']) : 'Borrador actualizado el ' . format_datetime_label($communication['created_at'])); ?></p>
                                            </div>
                                        </div>
                                        <span class="badge-status <?php echo $communication['status'] === 'Enviado' ? 'active' : 'info'; ?>"><?php echo h($communication['status']); ?></span>
                                    </div>

                                    <div class="comunicado-stats-grid">
                                        <div class="comunicado-stat">
                                            <span class="comunicado-stat-label"><i class="ph ph-users"></i> Destinatarios</span>
                                            <span class="comunicado-stat-value"><?php echo h((string) $communication['destinatarios_count']); ?></span>
                                        </div>
                                        <div class="comunicado-stat">
                                            <span class="comunicado-stat-label"><i class="ph ph-eye"></i> Abiertos</span>
                                            <span class="comunicado-stat-value"><?php echo h($communication['abiertos_count'] . ' (' . communication_open_rate($communication) . '%)'); ?></span>
                                        </div>
                                        <div class="comunicado-stat">
                                            <span class="comunicado-stat-label"><i class="ph ph-cursor"></i> Clicks</span>
                                            <span class="comunicado-stat-value"><?php echo h($communication['clicks_count'] . ' (' . communication_click_rate($communication) . '%)'); ?></span>
                                        </div>
                                        <div class="comunicado-stat">
                                            <span class="comunicado-stat-label">Audiencia</span>
                                            <span class="comunicado-stat-value"><?php echo h(communication_audience_label($communication['audiencia'])); ?></span>
                                        </div>
                                    </div>

                                    <div class="comunicado-actions">
                                        <a href="comunicacion.php?<?php echo h(build_query_string(array_merge($filters, ['view' => (int) $communication['id']]))); ?>" class="btn-comunicado">Ver detalles</a>
                                        <a href="comunicacion.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'duplicate-communication', 'id' => (int) $communication['id']]))); ?>" class="btn-comunicado">Duplicar</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($showComposerModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal communication-modal">
                <div class="modal-header">
                    <h2><?php echo h($composerTitle); ?></h2>
                    <a href="comunicacion.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="modal-form">
                    <input type="hidden" name="form_action" value="save_communication">
                    <input type="hidden" name="id" value="<?php echo h($formData['id']); ?>">
                    <input type="hidden" name="search" value="<?php echo h($filters['search']); ?>">
                    <input type="hidden" name="channel" value="<?php echo h($filters['channel']); ?>">

                    <?php if (!empty($formErrors)): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Hay campos pendientes o invalidos. Revisa el formulario.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group">
                            <label for="titulo">Titulo *</label>
                            <input id="titulo" name="titulo" type="text" value="<?php echo h($formData['titulo']); ?>" placeholder="Ej. Recordatorio de pago">
                            <?php if (isset($formErrors['titulo'])): ?><span class="field-error"><?php echo h($formErrors['titulo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="asunto">Asunto *</label>
                            <input id="asunto" name="asunto" type="text" value="<?php echo h($formData['asunto']); ?>" placeholder="Asunto visible para el socio">
                            <?php if (isset($formErrors['asunto'])): ?><span class="field-error"><?php echo h($formErrors['asunto']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="canal">Canal *</label>
                            <select id="canal" name="canal">
                                <?php foreach (communication_channel_options() as $channel): ?>
                                    <option value="<?php echo h($channel); ?>" <?php echo $formData['canal'] === $channel ? 'selected' : ''; ?>><?php echo h($channel); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['canal'])): ?><span class="field-error"><?php echo h($formErrors['canal']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="audiencia">Audiencia *</label>
                            <select id="audiencia" name="audiencia">
                                <?php foreach (communication_audience_options() as $audience): ?>
                                    <option value="<?php echo h($audience); ?>" <?php echo $formData['audiencia'] === $audience ? 'selected' : ''; ?>><?php echo h($audience); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['audiencia'])): ?><span class="field-error"><?php echo h($formErrors['audiencia']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="status">Estado *</label>
                            <select id="status" name="status">
                                <option value="Enviado" <?php echo $formData['status'] === 'Enviado' ? 'selected' : ''; ?>>Enviar ahora</option>
                                <option value="Borrador" <?php echo $formData['status'] === 'Borrador' ? 'selected' : ''; ?>>Guardar como borrador</option>
                            </select>
                            <?php if (isset($formErrors['status'])): ?><span class="field-error"><?php echo h($formErrors['status']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="cuerpo">Mensaje *</label>
                            <textarea id="cuerpo" name="cuerpo" rows="7" placeholder="Escribe aqui el contenido del comunicado..."><?php echo h($formData['cuerpo']); ?></textarea>
                            <?php if (isset($formErrors['cuerpo'])): ?><span class="field-error"><?php echo h($formErrors['cuerpo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <div class="communication-preview-box">
                                <strong>Alcance estimado:</strong>
                                <span id="communicationRecipientsCount"><?php echo h((string) $composerRecipientCount); ?></span>
                                <span>destinatarios</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="comunicacion.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-paper-plane-tilt"></i> <?php echo h($composerButtonLabel); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showDetailsModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card request-details-modal communication-details-modal">
                <div class="modal-header">
                    <h2>Detalle del Comunicado</h2>
                    <a href="comunicacion.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <div class="request-details-body">
                    <div class="request-detail-hero">
                        <div class="avatar-initial large"><i class="ph <?php echo $selectedCommunication['canal'] === 'WhatsApp' ? 'ph-whatsapp-logo' : ($selectedCommunication['canal'] === 'Mixto' ? 'ph-megaphone-simple' : 'ph-envelope-simple'); ?>"></i></div>
                        <div>
                            <h3><?php echo h($selectedCommunication['titulo']); ?></h3>
                            <p><?php echo h($selectedCommunication['asunto']); ?></p>
                            <span class="badge-status <?php echo $selectedCommunication['status'] === 'Enviado' ? 'active' : 'info'; ?>"><?php echo h($selectedCommunication['status']); ?></span>
                        </div>
                    </div>

                    <div class="request-details-grid">
                        <div class="request-detail-item">
                            <span>Canal</span>
                            <strong><?php echo h($selectedCommunication['canal']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Audiencia</span>
                            <strong><?php echo h(communication_audience_label($selectedCommunication['audiencia'])); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Destinatarios</span>
                            <strong><?php echo h((string) $selectedCommunication['destinatarios_count']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Abiertos</span>
                            <strong><?php echo h($selectedCommunication['abiertos_count'] . ' (' . communication_open_rate($selectedCommunication) . '%)'); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Clicks</span>
                            <strong><?php echo h($selectedCommunication['clicks_count'] . ' (' . communication_click_rate($selectedCommunication) . '%)'); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Fecha</span>
                            <strong><?php echo h(format_datetime_label($selectedCommunication['sent_at'] ?: $selectedCommunication['created_at'])); ?></strong>
                        </div>
                    </div>

                    <div class="request-notes-box">
                        <h4>Contenido</h4>
                        <p><?php echo nl2br(h($selectedCommunication['cuerpo'])); ?></p>
                    </div>

                    <div class="communication-recipient-box">
                        <h4>Vista previa de destinatarios</h4>
                        <?php if ($detailRecipients === []): ?>
                            <p>No hay destinatarios disponibles para esta audiencia.</p>
                        <?php else: ?>
                            <div class="communication-recipient-list">
                                <?php foreach ($detailRecipients as $recipient): ?>
                                    <span><?php echo h($recipient['nombre_completo'] . ' · ' . $recipient['email']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="modal-footer">
                        <a href="comunicacion.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'duplicate-communication', 'id' => (int) $selectedCommunication['id']]))); ?>" class="btn btn-outline">
                            <i class="ph ph-copy"></i> Duplicar
                        </a>
                        <a href="comunicacion.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'edit-communication', 'id' => (int) $selectedCommunication['id']]))); ?>" class="btn btn-outline">
                            <i class="ph ph-pencil-simple"></i> Editar
                        </a>
                        <a href="comunicacion.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cerrar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const audienceInput = document.getElementById('audiencia');
        const recipientsCount = document.getElementById('communicationRecipientsCount');
        const audienceCounts = <?php echo json_encode($communicationCounts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        userProfileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !userProfileBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });

        if (audienceInput && recipientsCount) {
            audienceInput.addEventListener('change', function () {
                recipientsCount.textContent = String(audienceCounts[audienceInput.value] || 0);
            });
        }
    </script>
</body>
</html>
