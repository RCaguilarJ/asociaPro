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
$filters = payment_filters_from_request($_GET);

function redirect_to_payments(array $params = []): void
{
    $query = build_query_string($params);
    header('Location: pagos.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $returnQuery = [];
    parse_str((string) ($_POST['return_query'] ?? ''), $returnQuery);
    $returnFilters = payment_filters_from_request($returnQuery);

    if (($_POST['form_action'] ?? '') === 'save_payment') {
        $formData = normalize_payment_form($_POST);
        $errors = validate_payment_form($formData, $pdo);
        $redirectState = $formData['id'] !== ''
            ? ['action' => 'edit-payment', 'id' => $formData['id']]
            : ['action' => 'new-payment'];

        if ($errors !== []) {
            store_form_state($formData, $errors);
            flash_message('error', 'Corrige los errores del formulario para continuar.');
            redirect_to_payments(array_merge($returnFilters, $redirectState));
        }

        $paymentId = save_payment($pdo, $formData);
        flash_message(
            'success',
            $formData['id'] !== ''
                ? 'El pago se actualizo correctamente.'
                : 'Pago registrado correctamente'
        );
        redirect_to_payments(array_merge($returnFilters, ['highlight' => $paymentId]));
    }

    if (($_POST['form_action'] ?? '') === 'delete_payment') {
        $paymentId = (int) ($_POST['payment_id'] ?? 0);

        if ($paymentId > 0) {
            delete_payment($pdo, $paymentId);
            flash_message('success', 'El pago se elimino correctamente.');
        }

        redirect_to_payments($returnFilters);
    }
}

if (($_GET['export'] ?? '') === 'csv') {
    export_payments_csv(fetch_payments($pdo, $filters));
    exit;
}

if (isset($_GET['download'])) {
    $payment = find_payment_by_id($pdo, (int) $_GET['download']);

    if (!$payment) {
        flash_message('error', 'No se encontro el pago solicitado para descargar.');
        redirect_to_payments($filters);
    }

    download_payment_receipt($payment);
}

$flash = pull_flash_message();
$formState = pull_form_state();
$modalMode = ($_GET['action'] ?? '') === 'edit-payment'
    ? 'edit'
    : ((($_GET['action'] ?? '') === 'new-payment') ? 'new' : '');
$highlightPaymentId = (int) ($_GET['highlight'] ?? 0);
$selectedPayment = isset($_GET['view']) ? find_payment_by_id($pdo, (int) $_GET['view']) : null;
$showDetailsModal = $selectedPayment !== null;
$payments = fetch_payments($pdo, $filters);
$members = fetch_members($pdo, ['search' => '', 'status' => '', 'sort' => 'alpha', 'tipo' => '', 'estado' => '', 'industria' => '']);
$metrics = payment_metrics($pdo);
$currentQuery = build_query_string($filters);
$exportQuery = build_query_string(array_merge($filters, ['export' => 'csv']));
$formData = payment_form_defaults();
$formErrors = $formState['errors'] ?? [];

if ($modalMode === 'edit') {
    $paymentToEdit = find_payment_by_id($pdo, (int) ($_GET['id'] ?? 0));
    if ($paymentToEdit) {
        $formData = payment_form_data_from_payment($paymentToEdit);
    } else {
        $modalMode = '';
        flash_message('error', 'No se encontro el pago solicitado.');
        redirect_to_payments($filters);
    }
}

if (!empty($formState['data'])) {
    $formData = array_merge($formData, $formState['data']);
}

$showPaymentModal = $modalMode !== '';
$paymentModalTitle = $modalMode === 'edit' ? 'Editar Pago' : 'Registrar Nuevo Pago';
$paymentSubmitLabel = $modalMode === 'edit' ? 'Guardar cambios' : 'Registrar Pago';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos y Cuotas - AsociaPro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="<?php echo ($showPaymentModal || $showDetailsModal) ? 'modal-open' : ''; ?>">
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
                <a href="pagos.php" class="nav-item active">
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
                            <h1>Pagos y Cuotas</h1>
                            <p>Gestion de ingresos y cobranza</p>
                        </div>
                        <a href="pagos.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'new-payment']))); ?>" class="btn btn-primary">
                            <i class="ph ph-plus"></i> Registrar Pago
                        </a>
                    </div>

                    <?php if ($flash): ?>
                        <div class="page-alert <?php echo $flash['type'] === 'error' ? 'error' : 'success'; ?>">
                            <i class="ph <?php echo $flash['type'] === 'error' ? 'ph-warning-circle' : 'ph-check-circle'; ?>"></i>
                            <span><?php echo h($flash['message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="stats-grid">
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon blue"><i class="ph ph-currency-dollar"></i></div>
                                <span class="payment-stat-label">Total Ingresos</span>
                            </div>
                            <div class="payment-stat-value"><?php echo h(format_currency($metrics['total_ingresos'])); ?></div>
                        </div>
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon green"><i class="ph ph-check-circle"></i></div>
                                <span class="payment-stat-label">Pagado</span>
                            </div>
                            <div class="payment-stat-value"><?php echo h(format_currency($metrics['total_pagado'])); ?></div>
                        </div>
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon yellow"><i class="ph ph-clock"></i></div>
                                <span class="payment-stat-label">Pendiente</span>
                            </div>
                            <div class="payment-stat-value"><?php echo h(format_currency($metrics['total_pendiente'])); ?></div>
                        </div>
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon red"><i class="ph ph-x-circle"></i></div>
                                <span class="payment-stat-label">Vencido</span>
                            </div>
                            <div class="payment-stat-value"><?php echo h(format_currency($metrics['total_vencido'])); ?></div>
                        </div>
                    </div>

                    <form method="get" class="filters-panel">
                        <div class="filters-bar">
                            <div class="search-input-wrapper">
                                <i class="ph ph-magnifying-glass"></i>
                                <input type="text" name="search" value="<?php echo h($filters['search']); ?>" placeholder="Buscar por socio, concepto o factura...">
                            </div>
                            <div class="filters-actions">
                                <select class="filter-select" name="status" onchange="this.form.submit()">
                                    <option value="">Todos los estados</option>
                                    <?php foreach (payment_statuses() as $status): ?>
                                        <option value="<?php echo h($status); ?>" <?php echo $filters['status'] === $status ? 'selected' : ''; ?>><?php echo h($status); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-outline">
                                    <i class="ph ph-funnel"></i> Filtros
                                </button>
                                <a href="pagos.php<?php echo $exportQuery !== '' ? '?' . h($exportQuery) : '?export=csv'; ?>" class="btn btn-outline">
                                    <i class="ph ph-download-simple"></i> Exportar
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Socio</th>
                                    <th>Concepto</th>
                                    <th>Metodo</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($payments === []): ?>
                                    <tr>
                                        <td colspan="8">
                                            <div class="inline-empty-state table-empty-state">No se encontraron pagos con esos filtros.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $payment): ?>
                                        <?php
                                        $badgeClass = $payment['status'] === 'Pagado' ? 'active' : ($payment['status'] === 'Pendiente' ? 'info' : 'inactive');
                                        $rowClass = ((int) $payment['id'] === $highlightPaymentId) ? 'is-highlighted-row' : '';
                                        ?>
                                        <tr class="<?php echo $rowClass; ?>">
                                            <td><?php echo h((new DateTimeImmutable($payment['fecha_pago']))->format('j/n/Y')); ?></td>
                                            <td><?php echo h($payment['socio_nombre']); ?></td>
                                            <td><?php echo h($payment['concepto']); ?></td>
                                            <td><?php echo h($payment['metodo_pago']); ?></td>
                                            <td><?php echo h(format_currency((float) $payment['monto'])); ?></td>
                                            <td><span class="badge-status <?php echo $badgeClass; ?>"><i class="ph <?php echo $payment['status'] === 'Pagado' ? 'ph-check-circle' : ($payment['status'] === 'Pendiente' ? 'ph-clock' : 'ph-x-circle'); ?>"></i> <?php echo h($payment['status']); ?></span></td>
                                            <td style="color: var(--text-muted);"><?php echo h($payment['numero_factura'] ?: '-'); ?></td>
                                            <td>
                                                <div class="table-actions">
                                                    <a href="pagos.php?<?php echo h(build_query_string(array_merge($filters, ['view' => (int) $payment['id']]))); ?>" class="card-link">Ver detalles</a>
                                                    <a href="pagos.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'edit-payment', 'id' => (int) $payment['id']]))); ?>" class="card-link">Editar</a>
                                                    <a href="pagos.php?<?php echo h(build_query_string(array_merge($filters, ['download' => (int) $payment['id']]))); ?>" class="card-link">Recibo</a>
                                                    <form method="post" onsubmit="return confirm('Se eliminara este pago del historial. ¿Deseas continuar?');">
                                                        <input type="hidden" name="form_action" value="delete_payment">
                                                        <input type="hidden" name="payment_id" value="<?php echo (int) $payment['id']; ?>">
                                                        <input type="hidden" name="return_query" value="<?php echo h($currentQuery); ?>">
                                                        <button type="submit" class="action-icon-btn danger" title="Eliminar pago">
                                                            <i class="ph ph-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="config-section">
                        <h3 class="config-section-title">Configuracion de Cuotas</h3>
                        <div class="config-grid">
                            <div class="config-card">
                                <div class="config-card-title">Membresia Basica</div>
                                <div class="config-card-price"><?php echo h(format_currency(membership_fee('Basica'))); ?></div>
                                <div class="config-card-period">Anual</div>
                            </div>
                            <div class="config-card">
                                <div class="config-card-title">Membresia Premium</div>
                                <div class="config-card-price"><?php echo h(format_currency(membership_fee('Premium'))); ?></div>
                                <div class="config-card-period">Anual</div>
                            </div>
                            <div class="config-card">
                                <div class="config-card-title">Membresia Corporativa</div>
                                <div class="config-card-price"><?php echo h(format_currency(membership_fee('Corporativa'))); ?></div>
                                <div class="config-card-period">Anual</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($showPaymentModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card member-modal">
                <div class="modal-header">
                    <h2><?php echo h($paymentModalTitle); ?></h2>
                    <a href="pagos.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <form method="post" class="modal-form">
                    <input type="hidden" name="form_action" value="save_payment">
                    <input type="hidden" name="id" value="<?php echo h($formData['id']); ?>">
                    <input type="hidden" name="return_query" value="<?php echo h($currentQuery); ?>">

                    <?php if (!empty($formErrors)): ?>
                        <div class="form-errors-summary">
                            <i class="ph ph-warning-circle"></i>
                            <span>Hay campos pendientes o invalidos. Revisa el formulario.</span>
                        </div>
                    <?php endif; ?>

                    <div class="modal-grid">
                        <div class="form-group full-width">
                            <label for="socio_id">Socio *</label>
                            <select id="socio_id" name="socio_id">
                                <option value="">Seleccionar socio...</option>
                                <?php foreach ($members as $member): ?>
                                    <option value="<?php echo (int) $member['id']; ?>" <?php echo $formData['socio_id'] === (string) $member['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($member['nombre_completo'] . ' - ' . $member['empresa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['socio_id'])): ?><span class="field-error"><?php echo h($formErrors['socio_id']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="concepto">Concepto *</label>
                            <input id="concepto" name="concepto" type="text" value="<?php echo h($formData['concepto']); ?>" placeholder="Cuota anual 2024">
                            <?php if (isset($formErrors['concepto'])): ?><span class="field-error"><?php echo h($formErrors['concepto']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="monto">Monto (MXN) *</label>
                            <input id="monto" name="monto" type="number" min="0" step="0.01" value="<?php echo h($formData['monto']); ?>" placeholder="0">
                            <?php if (isset($formErrors['monto'])): ?><span class="field-error"><?php echo h($formErrors['monto']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="metodo_pago">Metodo de pago *</label>
                            <select id="metodo_pago" name="metodo_pago">
                                <?php foreach (payment_methods() as $method): ?>
                                    <option value="<?php echo h($method); ?>" <?php echo $formData['metodo_pago'] === $method ? 'selected' : ''; ?>><?php echo h($method); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['metodo_pago'])): ?><span class="field-error"><?php echo h($formErrors['metodo_pago']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="fecha_pago">Fecha de pago *</label>
                            <input id="fecha_pago" name="fecha_pago" type="date" value="<?php echo h($formData['fecha_pago']); ?>">
                            <?php if (isset($formErrors['fecha_pago'])): ?><span class="field-error"><?php echo h($formErrors['fecha_pago']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="numero_factura">No. de factura</label>
                            <input id="numero_factura" name="numero_factura" type="text" value="<?php echo h($formData['numero_factura']); ?>" placeholder="FAC-2024-001">
                            <?php if (isset($formErrors['numero_factura'])): ?><span class="field-error"><?php echo h($formErrors['numero_factura']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="status">Estado *</label>
                            <select id="status" name="status">
                                <?php foreach (payment_statuses() as $status): ?>
                                    <option value="<?php echo h($status); ?>" <?php echo $formData['status'] === $status ? 'selected' : ''; ?>><?php echo h($status); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['status'])): ?><span class="field-error"><?php echo h($formErrors['status']); ?></span><?php endif; ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="notas">Notas</label>
                            <textarea id="notas" name="notas" rows="3" placeholder="Observaciones internas, referencia bancaria o comentarios del movimiento..."><?php echo h($formData['notas']); ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <div class="payment-total-box">
                                <strong>Total a registrar:</strong>
                                <span id="paymentTotalValue">$0 MXN</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="pagos.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="btn btn-outline">
                            <i class="ph ph-x"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk"></i> <?php echo h($paymentSubmitLabel); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($showDetailsModal): ?>
        <div class="modal-overlay is-visible">
            <div class="modal-card request-details-modal">
                <div class="modal-header">
                    <h2>Detalle del Pago</h2>
                    <a href="pagos.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="modal-close" aria-label="Cerrar">
                        <i class="ph ph-x"></i>
                    </a>
                </div>

                <div class="request-details-body">
                    <div class="request-detail-hero">
                        <div class="avatar-initial large"><?php echo h(request_initial($selectedPayment['socio_nombre'])); ?></div>
                        <div>
                            <h3><?php echo h($selectedPayment['socio_nombre']); ?></h3>
                            <p><?php echo h($selectedPayment['socio_empresa']); ?></p>
                            <span class="badge-status <?php echo $selectedPayment['status'] === 'Pagado' ? 'active' : ($selectedPayment['status'] === 'Pendiente' ? 'info' : 'inactive'); ?>"><?php echo h($selectedPayment['status']); ?></span>
                        </div>
                    </div>

                    <div class="request-details-grid">
                        <div class="request-detail-item">
                            <span>Concepto</span>
                            <strong><?php echo h($selectedPayment['concepto']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Monto</span>
                            <strong><?php echo h(format_currency((float) $selectedPayment['monto'])); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Metodo</span>
                            <strong><?php echo h($selectedPayment['metodo_pago']); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Fecha de pago</span>
                            <strong><?php echo h((new DateTimeImmutable($selectedPayment['fecha_pago']))->format('d/m/Y')); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Factura</span>
                            <strong><?php echo h($selectedPayment['numero_factura'] ?: 'Sin factura'); ?></strong>
                        </div>
                        <div class="request-detail-item">
                            <span>Correo del socio</span>
                            <strong><?php echo h($selectedPayment['socio_email']); ?></strong>
                        </div>
                    </div>

                    <?php if (!empty($selectedPayment['notas'])): ?>
                        <div class="request-notes-box">
                            <h4>Notas</h4>
                            <p><?php echo h($selectedPayment['notas']); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="modal-footer">
                        <a href="pagos.php?<?php echo h(build_query_string(array_merge($filters, ['download' => (int) $selectedPayment['id']]))); ?>" class="btn btn-outline">
                            <i class="ph ph-download-simple"></i> Descargar recibo
                        </a>
                        <a href="pagos.php?<?php echo h(build_query_string(array_merge($filters, ['action' => 'edit-payment', 'id' => (int) $selectedPayment['id']]))); ?>" class="btn btn-outline">
                            <i class="ph ph-pencil-simple"></i> Editar
                        </a>
                        <form method="post" onsubmit="return confirm('Se eliminara este pago del historial. ¿Deseas continuar?');">
                            <input type="hidden" name="form_action" value="delete_payment">
                            <input type="hidden" name="payment_id" value="<?php echo (int) $selectedPayment['id']; ?>">
                            <input type="hidden" name="return_query" value="<?php echo h($currentQuery); ?>">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="ph ph-trash"></i> Eliminar
                            </button>
                        </form>
                        <a href="pagos.php<?php echo $currentQuery !== '' ? '?' . h($currentQuery) : ''; ?>" class="btn btn-outline">
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
        const amountInput = document.getElementById('monto');
        const paymentTotalValue = document.getElementById('paymentTotalValue');

        userProfileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!profileDropdown.contains(event.target) && !userProfileBtn.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });

        function updatePaymentTotal() {
            if (!amountInput || !paymentTotalValue) {
                return;
            }

            const value = Number(amountInput.value || 0);
            paymentTotalValue.textContent = '$' + new Intl.NumberFormat('es-MX', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(value) + ' MXN';
        }

        if (amountInput && paymentTotalValue) {
            amountInput.addEventListener('input', updatePaymentTotal);
            updatePaymentTotal();
        }
    </script>
</body>
</html>
