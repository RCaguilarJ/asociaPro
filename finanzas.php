<?php
// finanzas.php
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
    <title>Finanzas - AsociaPro</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- CSS del Dashboard (Evitar caché) -->
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?php echo time(); ?>">
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
                <a href="finanzas.php" class="nav-item active">
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
                    <div class="page-header" style="justify-content: flex-start; margin-bottom: 2rem;">
                        <div class="page-title">
                            <h1>Finanzas</h1>
                            <p>Control financiero y reportes</p>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="charts-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
                        <div class="finanzas-stat-card">
                            <div class="finanzas-stat-header">
                                <div class="icon-wrapper green">
                                    <i class="ph ph-trend-up"></i>
                                </div>
                                Ingresos Totales
                            </div>
                            <div class="finanzas-stat-value">$125,000</div>
                            <div class="finanzas-stat-subtext positive">+ 12.5% vs mes anterior</div>
                        </div>
                        
                        <div class="finanzas-stat-card">
                            <div class="finanzas-stat-header">
                                <div class="icon-wrapper red">
                                    <i class="ph ph-trend-down"></i>
                                </div>
                                Egresos Totales
                            </div>
                            <div class="finanzas-stat-value">$51,000</div>
                            <div class="finanzas-stat-subtext">vs presupuesto</div>
                        </div>
                        
                        <div class="finanzas-stat-card">
                            <div class="finanzas-stat-header">
                                <div class="icon-wrapper blue">
                                    <i class="ph ph-currency-dollar"></i>
                                </div>
                                Balance
                            </div>
                            <div class="finanzas-stat-value">$74,000</div>
                            <div class="finanzas-stat-subtext positive">Positivo</div>
                        </div>
                        
                        <div class="finanzas-stat-card">
                            <div class="finanzas-stat-header">
                                <div class="icon-wrapper purple">
                                    <i class="ph ph-chart-pie-slice"></i>
                                </div>
                                Margen
                            </div>
                            <div class="finanzas-stat-value">59%</div>
                            <div class="finanzas-stat-subtext">de utilidad</div>
                        </div>
                    </div>

                    <!-- Charts Grid -->
                    <div class="finanzas-charts-grid">
                        
                        <!-- Bar Chart -->
                        <div class="chart-card">
                            <h3>Ingresos vs Egresos</h3>
                            <div class="bar-chart-wrapper">
                                <div class="bar-chart-main">
                                    <div class="bar-chart-y-axis">
                                        <span>140000</span>
                                        <span>105000</span>
                                        <span>70000</span>
                                        <span>35000</span>
                                        <span>0</span>
                                    </div>
                                    <div class="bar-chart-grid-lines">
                                        <div class="grid-line"></div>
                                        <div class="grid-line"></div>
                                        <div class="grid-line"></div>
                                        <div class="grid-line"></div>
                                        <div class="grid-line" style="border-bottom: 1px solid #cbd5e1;"></div>
                                    </div>
                                    <div class="bar-chart-bars">
                                        <div class="bar-group">
                                            <div class="bar ingreso" style="height: 70%;"></div>
                                            <div class="bar egreso" style="height: 35%;"></div>
                                        </div>
                                        <div class="bar-group">
                                            <div class="bar ingreso" style="height: 75%;"></div>
                                            <div class="bar egreso" style="height: 38%;"></div>
                                        </div>
                                        <div class="bar-group">
                                            <div class="bar ingreso" style="height: 85%;"></div>
                                            <div class="bar egreso" style="height: 42%;"></div>
                                        </div>
                                        <div class="bar-group">
                                            <div class="bar ingreso" style="height: 88%;"></div>
                                            <div class="bar egreso" style="height: 40%;"></div>
                                        </div>
                                        <div class="bar-group">
                                            <div class="bar ingreso" style="height: 90%;"></div>
                                            <div class="bar egreso" style="height: 41%;"></div>
                                        </div>
                                        <div class="bar-group">
                                            <div class="bar ingreso" style="height: 95%;"></div>
                                            <div class="bar egreso" style="height: 42%;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bar-chart-x-axis">
                                    <span>Oct</span>
                                    <span>Nov</span>
                                    <span>Dic</span>
                                    <span>Ene</span>
                                    <span>Feb</span>
                                    <span>Mar</span>
                                </div>
                                <div class="chart-legend">
                                    <div class="legend-item"><div class="legend-color ingreso"></div> Ingresos</div>
                                    <div class="legend-item"><div class="legend-color egreso"></div> Egresos</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pie Chart -->
                        <div class="chart-card">
                            <h3>Desglose de Ingresos</h3>
                            <div class="pie-chart-container">
                                <div class="pie-chart"></div>
                            </div>
                        </div>

                    </div> <!-- Fin charts grid -->

                    <!-- Table Section -->
                    <div class="crm-table-section">
                        <h2 class="crm-section-title" style="margin-bottom: 1.5rem;">Transacciones Recientes</h2>
                        <div class="table-responsive">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th style="text-align: right;">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="color: var(--text-muted);">24 Abr 2024</td>
                                        <td style="color: var(--text-main); font-weight: 500;">Cuota anual - María González</td>
                                        <td style="color: var(--text-muted);">Membresías</td>
                                        <td><span class="badge-fin-ingreso">Ingreso</span></td>
                                        <td style="text-align: right; font-weight: 600; color: var(--text-main);">+$5,000</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">23 Abr 2024</td>
                                        <td style="color: var(--text-main); font-weight: 500;">Pago proveedor evento</td>
                                        <td style="color: var(--text-muted);">Eventos</td>
                                        <td><span class="badge-fin-egreso">Egreso</span></td>
                                        <td style="text-align: right; font-weight: 600; color: var(--text-main);">-$8,500</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">22 Abr 2024</td>
                                        <td style="color: var(--text-main); font-weight: 500;">Inscripción congreso - 15 personas</td>
                                        <td style="color: var(--text-muted);">Eventos</td>
                                        <td><span class="badge-fin-ingreso">Ingreso</span></td>
                                        <td style="text-align: right; font-weight: 600; color: var(--text-main);">+$37,500</td>
                                    </tr>
                                </tbody>
                            </table>
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
