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
$activeTab = users_tab_from_request($_GET);
$search = trim((string) ($_GET['search'] ?? ''));
$pendingRequests = count_pending_requests($pdo);

function redirect_to_users(array $extra = []): void
{
    $query = build_query_string($extra);
    header('Location: usuarios.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['form_action'] ?? '') === 'create_user')) {
    $formData = normalize_user_form($_POST);
    $avatarUpload = $_FILES['avatar'] ?? null;
    $errors = validate_user_form($formData, $pdo, $avatarUpload);

    if ($errors !== []) {
        store_form_state($formData, $errors);
        flash_message('error', 'Corrige los errores del formulario para crear el usuario.');
        redirect_to_users(['tab' => 'users', 'action' => 'new-user', 'search' => trim((string) ($_POST['return_search'] ?? ''))]);
    }

    $userId = save_user($pdo, $formData, $avatarUpload);
    flash_message('success', 'El usuario se creo correctamente.');
    redirect_to_users(['tab' => 'users', 'highlight' => $userId]);
}

$flash = pull_flash_message();
$formState = pull_form_state();
$showCreateModal = (($_GET['action'] ?? '') === 'new-user');
$highlightUserId = (int) ($_GET['highlight'] ?? 0);
$users = fetch_users($pdo, $search);
$roles = fetch_roles($pdo);
$roleCards = roles_catalog();
$formData = array_merge(user_form_defaults(), $formState['data'] ?? []);
$formErrors = $formState['errors'] ?? [];
$hasExistingAvatarPreview = !empty($formData['avatar_url']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y Roles - AsociaPro</title>
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
                <a href="usuarios.php" class="nav-item active">
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
                    <a href="usuarios.php?tab=roles" class="header-icon-btn" aria-label="Configuracion">
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
                            <a href="usuarios.php?tab=roles" class="dropdown-item">
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
                            <h1>Usuarios y Roles</h1>
                            <p>Gestion de accesos y permisos</p>
                        </div>
                        <?php if ($activeTab === 'users'): ?>
                            <a href="usuarios.php?<?php echo h(build_query_string(['tab' => 'users', 'action' => 'new-user', 'search' => $search])); ?>" class="btn btn-primary">
                                <i class="ph ph-plus"></i> Nuevo Usuario
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if ($flash): ?>
                        <div class="page-alert <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-warning-circle' : 'ph-check-circle'; ?>"></i>
                            <span><?php echo h($flash['message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="tabs-container">
                        <a href="usuarios.php?tab=users" class="tab-item <?php echo $activeTab === 'users' ? 'active' : ''; ?>">Usuarios</a>
                        <a href="usuarios.php?tab=roles" class="tab-item <?php echo $activeTab === 'roles' ? 'active' : ''; ?>">Roles y Permisos</a>
                    </div>

                    <?php if ($activeTab === 'users'): ?>
                        <form method="get" class="search-input-wrapper search-full-width">
                            <input type="hidden" name="tab" value="users">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Buscar usuarios...">
                        </form>

                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Rol</th>
                                        <th>Ultimo acceso</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($users === []): ?>
                                        <tr>
                                            <td colspan="5">
                                                <div class="inline-empty-state table-empty-state">No se encontraron usuarios con esos filtros.</div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                            <?php
                                            $roleData = role_meta($user['rol_nombre']);
                                            $rowClass = ((int) $user['id'] === $highlightUserId) ? 'is-highlighted-row' : '';
                                            ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td>
                                                    <div class="user-cell">
                                                        <img src="<?php echo h($user['avatar_url'] ?: member_avatar_for_email($user['email'])); ?>" alt="<?php echo h($user['nombre']); ?>">
                                                        <div class="user-cell-info">
                                                            <span class="user-cell-name"><?php echo h($user['nombre']); ?></span>
                                                            <span class="user-cell-email"><?php echo h($user['email']); ?></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge-role role-tone-<?php echo h($roleData['tone']); ?>">
                                                        <i class="ph <?php echo h($roleData['icon']); ?>"></i> <?php echo h($user['rol_nombre']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo h(format_last_access($user['ultimo_acceso'])); ?></td>
                                                <td><span class="badge-status <?php echo ((int) $user['activo'] === 1) ? 'active' : 'inactive'; ?>"><?php echo ((int) $user['activo'] === 1) ? 'Activo' : 'Inactivo'; ?></span></td>
                                                <td>
                                                    <div class="table-actions">
                                                        <button class="action-icon-btn" type="button" title="Enviar correo"><i class="ph ph-envelope-simple"></i></button>
                                                        <button class="action-icon-btn" type="button" title="Editar"><i class="ph ph-pencil-simple"></i></button>
                                                        <button class="action-icon-btn danger" type="button" title="Eliminar"><i class="ph ph-trash"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="roles-grid">
                            <?php foreach ($roles as $role): ?>
                                <?php $roleData = role_meta($role['nombre']); ?>
                                <div class="role-card">
                                    <div class="role-card-header">
                                        <div class="role-card-info">
                                            <div class="role-icon role-tone-<?php echo h($roleData['tone']); ?>">
                                                <i class="ph <?php echo h($roleData['icon']); ?>"></i>
                                            </div>
                                            <div>
                                                <h3><?php echo h($role['nombre']); ?></h3>
                                                <p><?php echo h($roleData['description']); ?></p>
                                            </div>
                                        </div>
                                        <button class="action-icon-btn" type="button" title="Editar rol"><i class="ph ph-pencil-simple"></i></button>
                                    </div>
                                    <div class="role-card-body">
                                        <span>Permisos</span>
                                        <strong><?php echo h($roleData['permissions']); ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($showCreateModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal">
                <div class="modal-header">
                    <h2>Nuevo Usuario</h2>
                    <a href="usuarios.php?<?php echo h(build_query_string(['tab' => 'users', 'search' => $search])); ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" enctype="multipart/form-data" class="modal-form">
                    <input type="hidden" name="form_action" value="create_user">
                    <input type="hidden" name="return_search" value="<?php echo h($search); ?>">

                    <?php if (!empty($formErrors)): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Hay campos pendientes o invalidos. Revisa el formulario.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input id="nombre" name="nombre" type="text" value="<?php echo h($formData['nombre']); ?>" placeholder="Ej: Andrea Torres Ruiz">
                            <?php if (isset($formErrors['nombre'])): ?><span class="field-error"><?php echo h($formErrors['nombre']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo electronico *</label>
                            <input id="email" name="email" type="email" value="<?php echo h($formData['email']); ?>" placeholder="correo@amuvie.org">
                            <?php if (isset($formErrors['email'])): ?><span class="field-error"><?php echo h($formErrors['email']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="rol_id">Rol *</label>
                            <select id="rol_id" name="rol_id">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo (int) $role['id']; ?>" <?php echo $formData['rol_id'] === (string) $role['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($role['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['rol_id'])): ?><span class="field-error"><?php echo h($formErrors['rol_id']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="activo">Estado</label>
                            <select id="activo" name="activo">
                                <option value="1" <?php echo $formData['activo'] === '1' ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo $formData['activo'] === '0' ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password">Contrasena temporal *</label>
                            <input id="password" name="password" type="password" value="<?php echo h($formData['password']); ?>" placeholder="Minimo 8 caracteres">
                            <?php if (isset($formErrors['password'])): ?><span class="field-error"><?php echo h($formErrors['password']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmar contrasena *</label>
                            <input id="password_confirm" name="password_confirm" type="password" value="<?php echo h($formData['password_confirm']); ?>" placeholder="Repite la contrasena">
                            <?php if (isset($formErrors['password_confirm'])): ?><span class="field-error"><?php echo h($formErrors['password_confirm']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
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
                    </div>

                    <div class="modal-footer">
                        <a href="usuarios.php?<?php echo h(build_query_string(['tab' => 'users', 'search' => $search])); ?>" class="btn btn-outline">
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

        userProfileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !userProfileBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
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
