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

function redirect_to_news(array $params = []): void
{
    $query = build_query_string($params);
    header('Location: noticias.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = (string) ($_POST['form_action'] ?? '');

    if ($formAction === 'save_news') {
        $formData = normalize_news_form($_POST);
        $errors = validate_news_form($formData);

        if ($errors !== []) {
            store_form_state($formData, $errors);
            flash_message('error', 'Corrige los campos de la noticia para continuar.');
            $redirectAction = ((int) ($formData['id'] ?? 0) > 0) ? 'edit-news' : 'new-news';
            $redirectParams = ['action' => $redirectAction];
            if ((int) ($formData['id'] ?? 0) > 0) {
                $redirectParams['id'] = (int) $formData['id'];
            }
            redirect_to_news($redirectParams);
        }

        $newsId = save_news($pdo, $formData);
        flash_message('success', ((int) ($formData['id'] ?? 0) > 0) ? 'Noticia actualizada correctamente.' : 'Noticia publicada correctamente.');
        redirect_to_news(['highlight' => $newsId]);
    }

    if ($formAction === 'delete_news') {
        $newsId = (int) ($_POST['news_id'] ?? 0);
        delete_news($pdo, $newsId);
        flash_message('success', 'Noticia eliminada correctamente.');
        redirect_to_news();
    }
}

$flash = pull_flash_message();
$formState = pull_form_state();
$action = trim((string) ($_GET['action'] ?? ''));
$selectedNewsId = (int) ($_GET['id'] ?? ($formState['data']['id'] ?? 0));
$selectedNews = $selectedNewsId > 0 ? find_news_by_id($pdo, $selectedNewsId) : null;
$newsItems = fetch_news($pdo);
$highlightNewsId = (int) ($_GET['highlight'] ?? 0);

$showCreateModal = $action === 'new-news' || ($action === '' && !empty($formState['data']) && (int) ($formState['data']['id'] ?? 0) === 0);
$showEditModal = $action === 'edit-news' && $selectedNews !== null;
$showViewModal = $action === 'view-news' && $selectedNews !== null;
$showAnyModal = $showCreateModal || $showEditModal || $showViewModal;

$formData = news_form_defaults();
if ($showEditModal && $selectedNews) {
    $formData = array_merge($formData, [
        'id' => (string) $selectedNews['id'],
        'titulo' => (string) $selectedNews['titulo'],
        'categoria' => (string) $selectedNews['categoria'],
        'resumen' => (string) $selectedNews['resumen'],
        'contenido' => (string) $selectedNews['contenido'],
    ]);
}
if (!empty($formState['data'])) {
    $formData = array_merge($formData, $formState['data']);
}
$formErrors = $formState['errors'] ?? [];
$editorPreviewTitle = $formData['titulo'] !== '' ? $formData['titulo'] : 'Titulo de la noticia';
$editorPreviewSummary = $formData['resumen'] !== '' ? $formData['resumen'] : 'Breve descripcion que aparecera en el listado.';
$editorPreviewContent = $formData['contenido'] !== '' ? $formData['contenido'] : 'Aqui veras una vista previa del cuerpo del articulo para validar tono, claridad y estructura.';
$editorPreviewImage = news_image_for_category($formData['categoria'] !== '' ? $formData['categoria'] : 'Noticias');
$editorPreviewExcerpt = strlen($editorPreviewContent) > 260 ? substr($editorPreviewContent, 0, 257) . '...' : $editorPreviewContent;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias y Blog - AsociaPro</title>
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
                <a href="documentos.php" class="nav-item">
                    <i class="ph ph-file-doc nav-icon"></i>
                    Documentos
                </a>
                <a href="noticias.php" class="nav-item active">
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
                            <h1>Noticias y Blog</h1>
                            <p>Publicaciones y comunicados oficiales</p>
                        </div>
                        <a href="noticias.php?action=new-news" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Nueva Noticia
                        </a>
                    </div>

                    <div class="noticias-list">
                        <?php if ($newsItems === []): ?>
                            <div class="empty-state">
                                <i class="ph ph-newspaper"></i>
                                <h3>Sin publicaciones</h3>
                                <p>Aun no hay noticias publicadas.</p>
                                <a href="noticias.php?action=new-news" class="btn btn-primary">Crear noticia</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($newsItems as $newsItem): ?>
                                <?php $rowClass = ((int) $newsItem['id'] === $highlightNewsId) ? ' is-highlighted-row-card' : ''; ?>
                                <div class="noticia-card<?php echo $rowClass; ?>">
                                    <div class="noticia-image">
                                        <img src="<?php echo h($newsItem['imagen_url'] ?: news_image_for_category((string) $newsItem['categoria'])); ?>" alt="<?php echo h($newsItem['titulo']); ?>">
                                    </div>
                                    <div class="noticia-body">
                                        <div class="noticia-badges">
                                            <span class="badge-tipo"><?php echo h($newsItem['categoria']); ?></span>
                                            <span class="badge-status active"><?php echo h($newsItem['status']); ?></span>
                                        </div>
                                        <h3><?php echo h($newsItem['titulo']); ?></h3>
                                        <p class="noticia-desc"><?php echo h($newsItem['resumen']); ?></p>
                                        <span class="noticia-meta">Por <?php echo h($newsItem['autor']); ?> &bull; <?php echo h(format_news_publish_date((string) $newsItem['created_at'])); ?></span>

                                        <div class="noticia-actions">
                                            <a href="noticias.php?action=view-news&id=<?php echo (int) $newsItem['id']; ?>" class="btn-noticia">
                                                <i class="ph ph-eye"></i> Ver
                                            </a>
                                            <a href="noticias.php?action=edit-news&id=<?php echo (int) $newsItem['id']; ?>" class="btn-noticia">
                                                <i class="ph ph-pencil-simple"></i> Editar
                                            </a>
                                            <form method="post" onsubmit="return confirm('Se eliminara esta noticia. Deseas continuar?');">
                                                <input type="hidden" name="form_action" value="delete_news">
                                                <input type="hidden" name="news_id" value="<?php echo (int) $newsItem['id']; ?>">
                                                <button type="submit" class="btn-noticia btn-noticia-danger">
                                                    <i class="ph ph-trash"></i> Eliminar
                                                </button>
                                            </form>
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

    <?php if ($flash): ?>
        <div class="toast-notification <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-x-circle' : 'ph-check-circle'; ?>"></i>
            <span><?php echo h($flash['message']); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($showCreateModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal news-create-modal">
                <div class="modal-header">
                    <h2>Nueva Noticia</h2>
                    <a href="noticias.php" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="member-form">
                    <input type="hidden" name="form_action" value="save_news">
                    <input type="hidden" name="id" value="<?php echo h($formData['id']); ?>">

                    <?php if ($formErrors !== []): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Corrige los campos marcados para guardar la noticia.</span>
                        </div>
                    <?php endif; ?>

                    <div class="news-create-form">
                        <div class="form-group">
                            <label for="titulo">Titulo *</label>
                            <input id="titulo" name="titulo" type="text" value="<?php echo h($formData['titulo']); ?>" placeholder="Titulo de la noticia">
                            <?php if (isset($formErrors['titulo'])): ?><span class="form-error"><?php echo h($formErrors['titulo']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoria *</label>
                            <select id="categoria" name="categoria">
                                <?php foreach (news_category_options() as $category): ?>
                                    <option value="<?php echo h($category); ?>" <?php echo $formData['categoria'] === $category ? 'selected' : ''; ?>><?php echo h($category); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['categoria'])): ?><span class="form-error"><?php echo h($formErrors['categoria']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="resumen">Resumen *</label>
                            <textarea id="resumen" name="resumen" rows="4" placeholder="Breve descripcion que aparecera en el listado..."><?php echo h($formData['resumen']); ?></textarea>
                            <?php if (isset($formErrors['resumen'])): ?><span class="form-error"><?php echo h($formErrors['resumen']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="contenido">Contenido *</label>
                            <textarea id="contenido" name="contenido" rows="8" placeholder="Contenido completo del articulo..."><?php echo h($formData['contenido']); ?></textarea>
                            <?php if (isset($formErrors['contenido'])): ?><span class="form-error"><?php echo h($formErrors['contenido']); ?></span><?php endif; ?>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="noticias.php" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk"></i> Publicar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showEditModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal news-editor-modal">
                <div class="modal-header">
                    <h2>Editar Noticia</h2>
                    <a href="noticias.php" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="member-form">
                    <input type="hidden" name="form_action" value="save_news">
                    <input type="hidden" name="id" value="<?php echo h($formData['id']); ?>">

                    <?php if ($formErrors !== []): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Corrige los campos marcados para guardar la noticia.</span>
                        </div>
                    <?php endif; ?>

                    <div class="news-editor-layout">
                        <div class="news-editor-main">
                            <div class="modal-grid">
                                <div class="form-group full-width">
                                    <label for="titulo">Titulo *</label>
                                    <input id="titulo" name="titulo" type="text" value="<?php echo h($formData['titulo']); ?>" placeholder="Titulo de la noticia">
                                    <?php if (isset($formErrors['titulo'])): ?><span class="form-error"><?php echo h($formErrors['titulo']); ?></span><?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="categoria">Categoria *</label>
                                    <select id="categoria" name="categoria">
                                        <?php foreach (news_category_options() as $category): ?>
                                            <option value="<?php echo h($category); ?>" <?php echo $formData['categoria'] === $category ? 'selected' : ''; ?>><?php echo h($category); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($formErrors['categoria'])): ?><span class="form-error"><?php echo h($formErrors['categoria']); ?></span><?php endif; ?>
                                </div>

                                <div class="news-editor-meta-card">
                                    <span class="news-editor-meta-label">Estado</span>
                                    <strong>Publicado</strong>
                                    <span class="news-editor-meta-label">Autor</span>
                                    <strong><?php echo h($showEditModal && $selectedNews ? (string) $selectedNews['autor'] : current_user_name()); ?></strong>
                                </div>

                                <div class="form-group full-width">
                                    <label for="resumen">Resumen *</label>
                                    <textarea id="resumen" name="resumen" rows="4" placeholder="Breve descripcion que aparecera en el listado..."><?php echo h($formData['resumen']); ?></textarea>
                                    <?php if (isset($formErrors['resumen'])): ?><span class="form-error"><?php echo h($formErrors['resumen']); ?></span><?php endif; ?>
                                </div>

                                <div class="form-group full-width">
                                    <label for="contenido">Contenido *</label>
                                    <textarea id="contenido" name="contenido" rows="10" placeholder="Contenido completo del articulo..."><?php echo h($formData['contenido']); ?></textarea>
                                    <?php if (isset($formErrors['contenido'])): ?><span class="form-error"><?php echo h($formErrors['contenido']); ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <aside class="news-editor-side">
                            <div class="news-editor-preview">
                                <div class="news-editor-preview-image">
                                    <img src="<?php echo h($editorPreviewImage); ?>" alt="<?php echo h($editorPreviewTitle); ?>">
                                </div>
                                <div class="noticia-badges">
                                    <span class="badge-tipo"><?php echo h($formData['categoria']); ?></span>
                                    <span class="badge-status active">Publicado</span>
                                </div>
                                <h3><?php echo h($editorPreviewTitle); ?></h3>
                                <p><?php echo h($editorPreviewSummary); ?></p>
                                <div class="news-editor-preview-content">
                                    <?php echo nl2br(h($editorPreviewExcerpt)); ?>
                                </div>
                            </div>

                            <div class="news-editor-guidance">
                                <h4>Checklist editorial</h4>
                                <ul>
                                    <li>Usa un titulo corto y claro.</li>
                                    <li>Resume el beneficio principal en 1 o 2 lineas.</li>
                                    <li>Abre el contenido con el dato mas importante.</li>
                                </ul>
                            </div>
                        </aside>
                    </div>

                    <div class="modal-footer">
                        <a href="noticias.php" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk"></i> Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showViewModal && $selectedNews): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card request-details-modal news-details-modal">
                <div class="modal-header">
                    <h2>Detalle de noticia</h2>
                    <a href="noticias.php" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <div class="request-details-body news-details-body">
                    <img class="news-details-image" src="<?php echo h($selectedNews['imagen_url'] ?: news_image_for_category((string) $selectedNews['categoria'])); ?>" alt="<?php echo h($selectedNews['titulo']); ?>">
                    <div class="noticia-badges">
                        <span class="badge-tipo"><?php echo h($selectedNews['categoria']); ?></span>
                        <span class="badge-status active"><?php echo h($selectedNews['status']); ?></span>
                    </div>
                    <h3 class="news-details-title"><?php echo h($selectedNews['titulo']); ?></h3>
                    <p class="news-details-meta">Por <?php echo h($selectedNews['autor']); ?> &bull; <?php echo h(format_news_publish_date((string) $selectedNews['created_at'])); ?></p>
                    <p class="news-details-summary"><?php echo h($selectedNews['resumen']); ?></p>
                    <div class="news-details-content"><?php echo nl2br(h((string) $selectedNews['contenido'])); ?></div>
                </div>

                <div class="modal-footer">
                    <a href="noticias.php" class="btn btn-outline">Cerrar</a>
                    <a href="noticias.php?action=edit-news&id=<?php echo (int) $selectedNews['id']; ?>" class="btn btn-primary">
                        <i class="ph ph-pencil-simple"></i> Editar
                    </a>
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
