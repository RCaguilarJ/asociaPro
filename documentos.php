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
$filters = document_filters_from_request($_GET);

function redirect_to_documents(array $params = []): void
{
    $query = build_query_string($params);
    header('Location: documentos.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['form_action'] ?? '') === 'save_document')) {
    $formData = normalize_document_form($_POST);
    $documentUpload = $_FILES['archivo'] ?? null;
    $errors = validate_document_form($formData, $documentUpload);
    $returnFilters = document_filters_from_request($_POST);

    if ($errors !== []) {
        store_form_state($formData, $errors);
        flash_message('error', 'Corrige los errores del documento para continuar.');
        redirect_to_documents(array_merge($returnFilters, ['action' => 'new-document']));
    }

    $documentId = save_document($pdo, $formData, $documentUpload);
    flash_message('success', 'El documento se subio correctamente.');
    redirect_to_documents(array_merge($returnFilters, ['highlight' => $documentId]));
}

if (isset($_GET['view']) || isset($_GET['download'])) {
    $documentId = (int) ($_GET['view'] ?? $_GET['download'] ?? 0);
    $document = find_document_by_id($pdo, $documentId);

    if (!$document) {
        flash_message('error', 'No se encontro el documento solicitado.');
        redirect_to_documents($filters);
    }

    output_document_response($document, isset($_GET['download']));
}

$flash = pull_flash_message();
$formState = pull_form_state();
$documents = fetch_documents($pdo, $filters);
$showCreateModal = (($_GET['action'] ?? '') === 'new-document') || !empty($formState['data']);
$highlightDocumentId = (int) ($_GET['highlight'] ?? 0);
$formData = array_merge(document_form_defaults(), $formState['data'] ?? []);
$formErrors = $formState['errors'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Documentos - AsociaPro</title>
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
                <a href="eventos.php" class="nav-item">
                    <i class="ph ph-calendar-blank nav-icon"></i>
                    Eventos
                </a>
                <a href="documentos.php" class="nav-item active">
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
                            <h1>Biblioteca de Documentos</h1>
                            <p>Estatutos, actas, reglamentos y mas</p>
                        </div>
                        <a href="documentos.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'new-document']))); ?>" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Subir Documento
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
                            <input type="text" name="search" value="<?php echo h($filters['search']); ?>" placeholder="Buscar documentos...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select" name="category" onchange="this.form.submit()">
                                <option value="">Todas las categorias</option>
                                <?php foreach (document_category_options() as $category): ?>
                                    <option value="<?php echo h($category); ?>" <?php echo $filters['category'] === $category ? 'selected' : ''; ?>><?php echo h($category); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline" type="submit">
                                <i class="ph ph-funnel"></i> Filtros
                            </button>
                        </div>
                    </form>

                    <div class="docs-grid">
                        <?php if ($documents === []): ?>
                            <div class="empty-state doc-empty-state">
                                <i class="ph ph-file-search"></i>
                                <h3>Sin documentos</h3>
                                <p>No se encontraron documentos con esos filtros.</p>
                                <a href="documentos.php?action=new-document" class="btn btn-primary">Subir documento</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($documents as $document): ?>
                                <?php $rowClass = ((int) $document['id'] === $highlightDocumentId) ? 'is-highlighted-row-card' : ''; ?>
                                <div class="doc-card <?php echo $rowClass; ?>">
                                    <div class="doc-header">
                                        <div class="doc-icon">
                                            <i class="ph <?php echo strtolower((string) $document['file_ext']) === 'pdf' ? 'ph-file-pdf' : 'ph-file-doc'; ?>"></i>
                                        </div>
                                        <div class="doc-info">
                                            <h3><?php echo h($document['titulo']); ?></h3>
                                            <span class="badge-category"><?php echo h($document['categoria']); ?></span>
                                        </div>
                                    </div>

                                    <div class="doc-properties">
                                        <div class="doc-prop">
                                            <span class="doc-prop-label">Formato</span>
                                            <span class="doc-prop-value"><?php echo h(strtoupper((string) $document['file_ext'])); ?></span>
                                        </div>
                                        <div class="doc-prop">
                                            <span class="doc-prop-label">Tamano</span>
                                            <span class="doc-prop-value"><?php echo h(format_file_size((int) $document['file_size_bytes'])); ?></span>
                                        </div>
                                        <div class="doc-prop">
                                            <span class="doc-prop-label">Subido</span>
                                            <span class="doc-prop-value"><?php echo h(format_short_date(substr((string) $document['created_at'], 0, 10))); ?></span>
                                        </div>
                                        <div class="doc-prop">
                                            <span class="doc-prop-label">Acceso</span>
                                            <span class="doc-prop-value"><?php echo h($document['nivel_acceso']); ?></span>
                                        </div>
                                    </div>

                                    <div class="doc-actions">
                                        <a href="documentos.php?<?php echo h(build_query_string(array_merge($filters, ['view' => (int) $document['id']]))); ?>" class="btn-doc btn-doc-outline">
                                            <i class="ph ph-eye"></i> Ver
                                        </a>
                                        <a href="documentos.php?<?php echo h(build_query_string(array_merge($filters, ['download' => (int) $document['id']]))); ?>" class="btn-doc btn-doc-primary">
                                            <i class="ph ph-download-simple"></i> Descargar
                                        </a>
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
                    <h2>Subir Nuevo Documento</h2>
                    <a href="documentos.php<?php echo build_query_string($filters) !== '' ? '?' . h(build_query_string($filters)) : ''; ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="modal-form" enctype="multipart/form-data">
                    <input type="hidden" name="form_action" value="save_document">
                    <input type="hidden" name="search" value="<?php echo h($filters['search']); ?>">
                    <input type="hidden" name="category" value="<?php echo h($filters['category']); ?>">

                    <?php if (!empty($formErrors)): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Hay campos pendientes o invalidos. Revisa el formulario.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group full-width">
                            <label for="titulo">Titulo del documento *</label>
                            <input id="titulo" name="titulo" type="text" value="<?php echo h($formData['titulo']); ?>" placeholder="Ej: Estatutos AMUVIE 2024">
                            <?php if (isset($formErrors['titulo'])): ?><span class="field-error"><?php echo h($formErrors['titulo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="categoria">Categoria *</label>
                            <select id="categoria" name="categoria">
                                <?php foreach (document_category_options() as $category): ?>
                                    <option value="<?php echo h($category); ?>" <?php echo $formData['categoria'] === $category ? 'selected' : ''; ?>><?php echo h($category); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['categoria'])): ?><span class="field-error"><?php echo h($formErrors['categoria']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="nivel_acceso">Nivel de acceso *</label>
                            <select id="nivel_acceso" name="nivel_acceso">
                                <?php foreach (document_access_options() as $access): ?>
                                    <option value="<?php echo h($access); ?>" <?php echo $formData['nivel_acceso'] === $access ? 'selected' : ''; ?>><?php echo h($access); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['nivel_acceso'])): ?><span class="field-error"><?php echo h($formErrors['nivel_acceso']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="archivo">Archivo *</label>
                            <label class="document-upload-box" for="archivo">
                                <div class="document-upload-icon"><i class="ph ph-upload-simple"></i></div>
                                <div class="document-upload-text">
                                    <strong>Haz clic para seleccionar o arrastra el archivo aqui</strong>
                                    <span>PDF, DOC, DOCX hasta 10MB</span>
                                </div>
                                <input id="archivo" name="archivo" type="file" accept=".pdf,.doc,.docx">
                            </label>
                            <?php if (isset($formErrors['archivo'])): ?><span class="field-error"><?php echo h($formErrors['archivo']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="documentos.php<?php echo build_query_string($filters) !== '' ? '?' . h(build_query_string($filters)) : ''; ?>" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-upload-simple"></i> Subir Documento
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
