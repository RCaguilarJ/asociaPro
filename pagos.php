<?php
// pagos.php
session_start();

/*
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos y Cuotas - AsociaPro</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- CSS del Dashboard -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Izquierdo -->
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
                    <span class="badge">2</span>
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
                    Comunicación
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
                    Auditoría
                </a>
            </nav>
        </aside>

        <!-- Contenido Principal -->
        <div class="main-content">
            
            <!-- Header Superior -->
            <header class="top-header">
                <div class="search-bar">
                    <i class="ph ph-magnifying-glass"></i>
                    <input type="text" placeholder="Buscar socios, eventos, documentos...">
                </div>
                
                <div class="header-actions">
                    <button class="header-icon-btn">
                        <i class="ph ph-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <button class="header-icon-btn">
                        <i class="ph ph-gear"></i>
                    </button>
                    
                    <div class="user-profile-wrapper">
                        <div class="user-profile" id="userProfileBtn">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Carlos Mendoza" class="avatar">
                            <div class="user-info">
                                <span class="user-name">Carlos Mendoza <i class="ph ph-caret-down"></i></span>
                                <span class="user-role">admin</span>
                            </div>
                        </div>

                        <!-- Menú Desplegable -->
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">
                                <strong>Carlos Mendoza</strong>
                                <span>carlos@amuvie.org</span>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="ph ph-user-circle"></i> Mi Perfil
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="ph ph-gear"></i> Configuración
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="dropdown-item text-danger">
                                <i class="ph ph-sign-out"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Área desplazable -->
            <div class="dashboard-scrollable">
                <div class="dashboard-wrapper">
                    
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Pagos y Cuotas</h1>
                            <p>Gestión de ingresos y cobranza</p>
                        </div>
                        <button class="btn btn-primary">
                            <i class="ph ph-plus"></i> Registrar Pago
                        </button>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="stats-grid">
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon blue"><i class="ph ph-currency-dollar"></i></div>
                                <span class="payment-stat-label">Total Ingresos</span>
                            </div>
                            <div class="payment-stat-value">$13,000</div>
                        </div>
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon green"><i class="ph ph-check-circle"></i></div>
                                <span class="payment-stat-label">Pagado</span>
                            </div>
                            <div class="payment-stat-value">$8,000</div>
                        </div>
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon yellow"><i class="ph ph-clock"></i></div>
                                <span class="payment-stat-label">Pendiente</span>
                            </div>
                            <div class="payment-stat-value">$0</div>
                        </div>
                        <div class="payment-stat-card">
                            <div class="payment-stat-header">
                                <div class="payment-stat-icon red"><i class="ph ph-x-circle"></i></div>
                                <span class="payment-stat-label">Vencido</span>
                            </div>
                            <div class="payment-stat-value">$5,000</div>
                        </div>
                    </div>

                    <!-- Filters Bar -->
                    <div class="filters-bar">
                        <div class="search-input-wrapper">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" placeholder="Buscar por socio, concepto o factura...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select">
                                <option>Todos los estados</option>
                                <option>Pagado</option>
                                <option>Pendiente</option>
                                <option>Vencido</option>
                            </select>
                            <button class="btn btn-outline">
                                <i class="ph ph-funnel"></i> Filtros
                            </button>
                            <button class="btn btn-outline">
                                <i class="ph ph-download-simple"></i> Exportar
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Socio</th>
                                    <th>Concepto</th>
                                    <th>Método</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Factura</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>14/1/2024</td>
                                    <td>María González López</td>
                                    <td>Cuota anual 2024</td>
                                    <td>Transferencia</td>
                                    <td>$5,000</td>
                                    <td><span class="badge-status active"><i class="ph ph-check-circle"></i> Pagado</span></td>
                                    <td style="color: var(--text-muted);">FAC-2024-001</td>
                                    <td><a href="#" class="card-link">Ver detalles</a></td>
                                </tr>
                                <tr>
                                    <td>19/3/2024</td>
                                    <td>Roberto Hernández Cruz</td>
                                    <td>Cuota anual 2024</td>
                                    <td>Tarjeta</td>
                                    <td>$3,000</td>
                                    <td><span class="badge-status active"><i class="ph ph-check-circle"></i> Pagado</span></td>
                                    <td style="color: var(--text-muted);">FAC-2024-045</td>
                                    <td><a href="#" class="card-link">Ver detalles</a></td>
                                </tr>
                                <tr>
                                    <td>9/12/2023</td>
                                    <td>Ana Patricia Martínez</td>
                                    <td>Cuota anual 2024</td>
                                    <td>Transferencia</td>
                                    <td>$5,000</td>
                                    <td><span class="badge-status inactive"><i class="ph ph-x-circle"></i> Vencido</span></td>
                                    <td style="color: var(--text-muted);">-</td>
                                    <td><a href="#" class="card-link">Ver detalles</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Configuración de Cuotas -->
                    <div class="config-section">
                        <h3 class="config-section-title">Configuración de Cuotas</h3>
                        <div class="config-grid">
                            <div class="config-card">
                                <div class="config-card-title">Membresía Básica</div>
                                <div class="config-card-price">$3,000</div>
                                <div class="config-card-period">Anual</div>
                            </div>
                            <div class="config-card">
                                <div class="config-card-title">Membresía Premium</div>
                                <div class="config-card-price">$5,000</div>
                                <div class="config-card-period">Anual</div>
                            </div>
                            <div class="config-card">
                                <div class="config-card-title">Membresía Corporativa</div>
                                <div class="config-card-price">$10,000</div>
                                <div class="config-card-period">Anual</div>
                            </div>
                        </div>
                    </div>

                </div> <!-- End dashboard-wrapper -->
            </div> <!-- End dashboard-scrollable -->
        </div> <!-- End main-content -->
    </div> <!-- End app-container -->

    <script>
        // Lógica para mostrar/ocultar el menú del perfil
        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        userProfileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && e.target !== userProfileBtn && !userProfileBtn.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
