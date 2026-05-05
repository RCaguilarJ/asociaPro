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

function redirect_to_members(array $filters = [], array $extra = []): void
{
    $query = build_query_string(array_merge($filters, $extra));
    $location = 'socios.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$filters = member_filters_from_request($_GET);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $returnQuery = [];
    parse_str((string) ($_POST['return_query'] ?? ''), $returnQuery);
    $returnFilters = member_filters_from_request($returnQuery);

    if (($_POST['form_action'] ?? '') === 'save_member') {
        $formData = normalize_member_form($_POST);
        $avatarUpload = $_FILES['avatar'] ?? null;
        $errors = validate_member_form($formData, $pdo, $avatarUpload);
        $redirectState = $formData['id'] !== ''
            ? ['action' => 'edit', 'id' => $formData['id']]
            : ['action' => 'new'];

        if ($errors !== []) {
            store_form_state($formData, $errors);
            flash_message('error', 'Corrige los errores del formulario para continuar.');
            redirect_to_members($returnFilters, $redirectState);
        }

        $memberId = save_member($pdo, $formData, $avatarUpload);
        flash_message(
            'success',
            $formData['id'] !== ''
                ? 'El socio se actualizo correctamente.'
                : 'El socio se agrego correctamente.'
        );

        redirect_to_members($returnFilters, ['highlight' => $memberId, 'sort' => $returnFilters['sort'] ?: 'recent']);
    }

    if (($_POST['form_action'] ?? '') === 'delete_member') {
        $memberId = (int) ($_POST['member_id'] ?? 0);

        if ($memberId > 0) {
            delete_member($pdo, $memberId);
            flash_message('success', 'El socio se elimino correctamente.');
        }

        redirect_to_members($returnFilters);
    }
}

if (($_GET['export'] ?? '') === 'csv') {
    export_members_csv(fetch_members($pdo, $filters));
    exit;
}

$flash = pull_flash_message();
$formState = pull_form_state();
$members = fetch_members($pdo, $filters);
$totalMembers = (int) $pdo->query("SELECT COUNT(*) FROM socios")->fetchColumn();
$states = fetch_member_states($pdo);
$industries = industry_options();
$membershipOptions = membership_options();
$highlightMemberId = (int) ($_GET['highlight'] ?? 0);
$modalMode = ($_GET['action'] ?? '') === 'edit' ? 'edit' : ((($_GET['action'] ?? '') === 'new') ? 'new' : '');
$currentQuery = build_query_string($filters);
$exportQuery = build_query_string(array_merge($filters, ['export' => 'csv']));

$formData = member_form_defaults();
$formErrors = $formState['errors'] ?? [];
$hasExistingAvatarPreview = false;

if ($modalMode === 'edit') {
    $memberToEdit = find_member_by_id($pdo, (int) ($_GET['id'] ?? 0));
    if ($memberToEdit) {
        $formData = [
            'id' => (string) $memberToEdit['id'],
            'nombre_completo' => $memberToEdit['nombre_completo'],
            'email' => $memberToEdit['email'],
            'telefono' => $memberToEdit['telefono'],
            'empresa' => $memberToEdit['empresa'],
            'puesto' => $memberToEdit['puesto'],
            'industria' => $memberToEdit['industria'],
            'ciudad' => $memberToEdit['ciudad'],
            'estado' => $memberToEdit['estado'],
            'tipo_membresia' => $memberToEdit['tipo_membresia'],
            'avatar_url' => $memberToEdit['avatar_url'],
        ];
        $hasExistingAvatarPreview = !empty($memberToEdit['avatar_url']);
    } else {
        $modalMode = '';
        flash_message('error', 'No se encontro el socio solicitado.');
        redirect_to_members($filters);
    }
}

if (!empty($formState['data'])) {
    $formData = array_merge($formData, $formState['data']);
}

if (!empty($formData['avatar_url'])) {
    $hasExistingAvatarPreview = true;
}

$pageSummary = count($members) === $totalMembers || $filters === member_filters_from_request([])
    ? $totalMembers . ' socios en total'
    : count($members) . ' socios encontrados de ' . $totalMembers;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Socios - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="<?php echo $modalMode !== '' ? 'modal-open' : ''; ?>">
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
                <a href="socios.php" class="nav-item active">
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
                    <input
                        type="text"
                        name="search"
                        value="<?php echo h($filters['search']); ?>"
                        placeholder="Buscar socios, eventos, documentos..."
                    >
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
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Gestion de Socios</h1>
                            <p><?php echo h($pageSummary); ?></p>
                        </div>
                        <a href="socios.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'new']))); ?>" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Agregar Socio
                        </a>
                    </div>

                    <?php if ($flash): ?>
                        <div class="page-alert <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-warning-circle' : 'ph-check-circle'; ?>"></i>
                            <span><?php echo h($flash['message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="get" class="filters-panel">
                        <div class="filters-bar">
                            <div class="search-input-wrapper">
                                <i class="ph ph-magnifying-glass"></i>
                                <input
                                    type="text"
                                    name="search"
                                    value="<?php echo h($filters['search']); ?>"
                                    placeholder="Buscar por nombre, email o empresa..."
                                >
                            </div>
                            <div class="filters-actions">
                                <select class="filter-select" name="status" onchange="this.form.submit()">
                                    <option value="">Todos los estatus</option>
                                    <option value="Activo" <?php echo $filters['status'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Vencido" <?php echo $filters['status'] === 'Vencido' ? 'selected' : ''; ?>>Vencido</option>
                                </select>
                                <select class="filter-select" name="sort" onchange="this.form.submit()">
                                    <option value="recent" <?php echo $filters['sort'] === 'recent' ? 'selected' : ''; ?>>Recien agregados</option>
                                    <option value="alpha" <?php echo $filters['sort'] === 'alpha' ? 'selected' : ''; ?>>Alfabetico (A-Z)</option>
                                </select>
                                <button type="button" class="btn btn-outline" id="toggleAdvancedFilters">
                                    <i class="ph ph-funnel"></i> Mas filtros
                                </button>
                                <a href="socios.php<?php echo $exportQuery !== '' ? '?' . h($exportQuery) : '?export=csv'; ?>" class="btn btn-outline">
                                    <i class="ph ph-download-simple"></i> Exportar
                                </a>
                            </div>
                        </div>

                        <div class="advanced-filters" id="advancedFilters">
                            <div class="form-group">
                                <label for="filter_tipo">Tipo de membresia</label>
                                <select id="filter_tipo" name="tipo" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($membershipOptions as $key => $label): ?>
                                        <option value="<?php echo h($key); ?>" <?php echo $filters['tipo'] === $key ? 'selected' : ''; ?>>
                                            <?php echo h($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="filter_industria">Industria</label>
                                <select id="filter_industria" name="industria" class="form-select">
                                    <option value="">Todas</option>
                                    <?php foreach ($industries as $industry): ?>
                                        <option value="<?php echo h($industry); ?>" <?php echo $filters['industria'] === $industry ? 'selected' : ''; ?>>
                                            <?php echo h($industry); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado" class="form-select">
                                    <option value="">Todos</option>
                                    <?php foreach ($states as $state): ?>
                                        <option value="<?php echo h($state); ?>" <?php echo $filters['estado'] === $state ? 'selected' : ''; ?>>
                                            <?php echo h($state); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="advanced-filters-actions">
                                <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                                <a href="socios.php" class="btn btn-outline">Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <?php if ($members === []): ?>
                        <div class="empty-state">
                            <i class="ph ph-users-three"></i>
                            <h3>No hay socios para mostrar</h3>
                            <p>Ajusta los filtros o agrega un nuevo socio desde esta misma pantalla.</p>
                            <a href="socios.php?action=new" class="btn btn-primary">Agregar Socio</a>
                        </div>
                    <?php else: ?>
                        <div class="socios-grid">
                            <?php foreach ($members as $member): ?>
                                <?php
                                $statusClass = $member['estatus'] === 'Activo' ? 'active' : 'inactive';
                                $cardClasses = 'socio-card';
                                if ($highlightMemberId === (int) $member['id']) {
                                    $cardClasses .= ' is-highlighted';
                                }
                                $memberEditQuery = build_query_string(array_merge($filters, ['action' => 'edit', 'id' => $member['id']]));
                                ?>
                                <div class="<?php echo $cardClasses; ?>">
                                    <div class="socio-header">
                                        <div class="socio-profile">
                                            <img src="<?php echo h($member['avatar_url']); ?>" alt="<?php echo h($member['nombre_completo']); ?>" class="socio-avatar">
                                            <div class="socio-info">
                                                <h3><?php echo h($member['nombre_completo']); ?></h3>
                                                <span class="badge-status <?php echo $statusClass; ?>"><?php echo h($member['estatus']); ?></span>
                                            </div>
                                        </div>
                                        <div class="card-menu-wrapper">
                                            <button type="button" class="icon-button card-menu-trigger" aria-label="Opciones del socio">
                                                <i class="ph ph-dots-three-vertical socio-options"></i>
                                            </button>
                                            <div class="card-menu">
                                                <a href="socios.php?<?php echo h($memberEditQuery); ?>">Editar</a>
                                                <form method="post" onsubmit="return confirm('Se eliminara este socio. Deseas continuar?');">
                                                    <input type="hidden" name="form_action" value="delete_member">
                                                    <input type="hidden" name="member_id" value="<?php echo (int) $member['id']; ?>">
                                                    <input type="hidden" name="return_query" value="<?php echo h($currentQuery); ?>">
                                                    <button type="submit">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="socio-details">
                                        <div class="detail-item">
                                            <i class="ph ph-buildings"></i>
                                            <span><?php echo h($member['empresa']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="ph ph-envelope-simple"></i>
                                            <span><?php echo h($member['email']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="ph ph-phone"></i>
                                            <span><?php echo h($member['telefono']); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="ph ph-map-pin"></i>
                                            <span><?php echo h($member['ciudad'] . ', ' . $member['estado']); ?></span>
                                        </div>
                                    </div>

                                    <div class="socio-meta">
                                        <div class="meta-group">
                                            <span class="meta-label">Tipo de membresia</span>
                                            <span class="meta-value"><?php echo h($member['tipo_membresia']); ?></span>
                                        </div>
                                        <div class="meta-group right">
                                            <span class="meta-label">Vence</span>
                                            <span class="meta-value"><?php echo h(format_short_date($member['fecha_vencimiento'])); ?></span>
                                        </div>
                                    </div>

                                    <div class="socio-actions">
                                        <a href="socios.php?<?php echo h($memberEditQuery); ?>" class="btn-card edit">
                                            <i class="ph ph-pencil-simple"></i> Editar
                                        </a>
                                        <form method="post" onsubmit="return confirm('Se eliminara este socio. Deseas continuar?');">
                                            <input type="hidden" name="form_action" value="delete_member">
                                            <input type="hidden" name="member_id" value="<?php echo (int) $member['id']; ?>">
                                            <input type="hidden" name="return_query" value="<?php echo h($currentQuery); ?>">
                                            <button type="submit" class="btn-card delete">
                                                <i class="ph ph-trash"></i> Eliminar
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

    <?php if ($modalMode !== ''): ?>
        <?php $closeModalUrl = 'socios.php' . ($currentQuery !== '' ? '?' . $currentQuery : ''); ?>
        <div class="modal-overlay is-visible" id="memberModalOverlay">
            <div class="modal-card member-modal">
                <div class="modal-header">
                    <h2><?php echo $modalMode === 'edit' ? 'Editar Socio' : 'Agregar Nuevo Socio'; ?></h2>
                    <a href="<?php echo h($closeModalUrl); ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="modal-form" enctype="multipart/form-data">
                    <input type="hidden" name="form_action" value="save_member">
                    <input type="hidden" name="id" value="<?php echo h($formData['id']); ?>">
                    <input type="hidden" name="return_query" value="<?php echo h($currentQuery); ?>">

                    <?php if (!empty($formErrors)): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Hay campos pendientes o invalidos. Revisa el formulario.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group">
                            <label for="nombre_completo">Nombre completo *</label>
                            <input id="nombre_completo" name="nombre_completo" type="text" value="<?php echo h($formData['nombre_completo']); ?>" placeholder="Ej: Juan Perez Lopez">
                            <?php if (isset($formErrors['nombre_completo'])): ?><span class="field-error"><?php echo h($formErrors['nombre_completo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo electronico *</label>
                            <input id="email" name="email" type="email" value="<?php echo h($formData['email']); ?>" placeholder="correo@ejemplo.com">
                            <?php if (isset($formErrors['email'])): ?><span class="field-error"><?php echo h($formErrors['email']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Telefono *</label>
                            <input id="telefono" name="telefono" type="text" value="<?php echo h($formData['telefono']); ?>" placeholder="(55) 1234-5678">
                            <?php if (isset($formErrors['telefono'])): ?><span class="field-error"><?php echo h($formErrors['telefono']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="avatar">Foto de perfil</label>
                            <div class="avatar-upload-field">
                                <div class="avatar-upload-preview<?php echo $hasExistingAvatarPreview ? ' has-image' : ''; ?>">
                                    <img
                                        src="<?php echo $hasExistingAvatarPreview ? h($formData['avatar_url']) : ''; ?>"
                                        alt="Vista previa del avatar"
                                        <?php echo $hasExistingAvatarPreview ? '' : 'hidden'; ?>
                                    >
                                    <span class="avatar-upload-placeholder" <?php echo $hasExistingAvatarPreview ? 'hidden' : ''; ?>>
                                        <i class="ph ph-user"></i>
                                    </span>
                                </div>
                                <div class="avatar-upload-inputs">
                                    <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.webp,.gif">
                                    <p>Sube una imagen JPG, PNG, WEBP o GIF de hasta 2 MB.</p>
                                </div>
                            </div>
                            <?php if (isset($formErrors['avatar'])): ?><span class="field-error"><?php echo h($formErrors['avatar']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="empresa">Empresa *</label>
                            <input id="empresa" name="empresa" type="text" value="<?php echo h($formData['empresa']); ?>" placeholder="Nombre de la empresa">
                            <?php if (isset($formErrors['empresa'])): ?><span class="field-error"><?php echo h($formErrors['empresa']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="puesto">Puesto</label>
                            <input id="puesto" name="puesto" type="text" value="<?php echo h($formData['puesto']); ?>" placeholder="Ej: Director General">
                        </div>

                        <div class="form-group">
                            <label for="modal_industria">Industria *</label>
                            <select id="modal_industria" name="industria">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($industries as $industry): ?>
                                    <option value="<?php echo h($industry); ?>" <?php echo $formData['industria'] === $industry ? 'selected' : ''; ?>>
                                        <?php echo h($industry); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['industria'])): ?><span class="field-error"><?php echo h($formErrors['industria']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="ciudad">Ciudad *</label>
                            <input id="ciudad" name="ciudad" type="text" value="<?php echo h($formData['ciudad']); ?>" placeholder="Ciudad">
                            <?php if (isset($formErrors['ciudad'])): ?><span class="field-error"><?php echo h($formErrors['ciudad']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="estado_modal">Estado *</label>
                            <input id="estado_modal" name="estado" type="text" value="<?php echo h($formData['estado']); ?>" placeholder="Estado">
                            <?php if (isset($formErrors['estado'])): ?><span class="field-error"><?php echo h($formErrors['estado']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="tipo_membresia">Tipo de Membresia *</label>
                            <select id="tipo_membresia" name="tipo_membresia">
                                <?php foreach ($membershipOptions as $key => $label): ?>
                                    <option value="<?php echo h($key); ?>" <?php echo $formData['tipo_membresia'] === $key ? 'selected' : ''; ?>>
                                        <?php echo h($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['tipo_membresia'])): ?><span class="field-error"><?php echo h($formErrors['tipo_membresia']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="<?php echo h($closeModalUrl); ?>" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <script>
        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        const advancedFilters = document.getElementById('advancedFilters');
        const toggleAdvancedFilters = document.getElementById('toggleAdvancedFilters');
        const shouldShowAdvancedFilters = <?php echo json_encode(
            $filters['tipo'] !== '' || $filters['estado'] !== '' || $filters['industria'] !== ''
        ); ?>;

        userProfileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !userProfileBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });

        if (shouldShowAdvancedFilters) {
            advancedFilters.classList.add('is-visible');
        }

        toggleAdvancedFilters.addEventListener('click', function () {
            advancedFilters.classList.toggle('is-visible');
        });

        document.querySelectorAll('.card-menu-trigger').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.stopPropagation();
                const wrapper = button.closest('.card-menu-wrapper');
                document.querySelectorAll('.card-menu-wrapper').forEach(function (item) {
                    if (item !== wrapper) {
                        item.classList.remove('is-open');
                    }
                });
                wrapper.classList.toggle('is-open');
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.card-menu-wrapper').forEach(function (item) {
                item.classList.remove('is-open');
            });
        });

        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.querySelector('.avatar-upload-preview img');
        const avatarPlaceholder = document.querySelector('.avatar-upload-placeholder');
        const avatarPreviewWrapper = document.querySelector('.avatar-upload-preview');

        if (avatarInput && avatarPreview && avatarPlaceholder && avatarPreviewWrapper) {
            avatarInput.addEventListener('change', function () {
                const file = avatarInput.files && avatarInput.files[0];
                if (!file) {
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                avatarPreview.src = objectUrl;
                avatarPreview.hidden = false;
                avatarPlaceholder.hidden = true;
                avatarPreviewWrapper.classList.add('has-image');
                avatarPreview.onload = function () {
                    URL.revokeObjectURL(objectUrl);
                };
            });
        }
    </script>
</body>
</html>
