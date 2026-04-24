<?php
// crm.php
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
    <title>CRM / Seguimiento - AsociaPro</title>
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
                <a href="crm.php" class="nav-item active">
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
                            <h1>CRM / Seguimiento</h1>
                            <p>Gestión de prospectos y oportunidades</p>
                        </div>
                        <button class="btn btn-primary">
                            <i class="ph ph-plus"></i> Nuevo Prospecto
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="charts-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-users" style="color: #3b82f6;"></i> Total Prospectos
                            </div>
                            <div class="crm-stat-value">23</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-trend-up" style="color: #10b981;"></i> En negociación
                            </div>
                            <div class="crm-stat-value">5</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-clock" style="color: #ca8a04;"></i> Seguimiento hoy
                            </div>
                            <div class="crm-stat-value">8</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-check-circle" style="color: #9333ea;"></i> Convertidos
                            </div>
                            <div class="crm-stat-value">12</div>
                        </div>
                    </div>

                    <!-- Pipeline -->
                    <div class="crm-pipeline">
                        <h2 class="crm-section-title">Pipeline de Ventas</h2>
                        <div class="kanban-board">
                            <!-- Column 1 -->
                            <div class="kanban-column">
                                <div class="column-header">
                                    <span>Lead</span>
                                    <span class="column-count">0</span>
                                </div>
                            </div>
                            
                            <!-- Column 2 -->
                            <div class="kanban-column">
                                <div class="column-header">
                                    <span>Contactado</span>
                                    <span class="column-count">1</span>
                                </div>
                                <div class="kanban-card">
                                    <h4>Tech Innovations SA</h4>
                                    <p>Jorge Ramírez</p>
                                    <span class="badge-category">Premium</span>
                                </div>
                            </div>
                            
                            <!-- Column 3 -->
                            <div class="kanban-column">
                                <div class="column-header">
                                    <span>Propuesta</span>
                                    <span class="column-count">1</span>
                                </div>
                                <div class="kanban-card">
                                    <h4>Consultoría Empresarial</h4>
                                    <p>Andrea López</p>
                                    <span class="badge-category">Básica</span>
                                </div>
                            </div>
                            
                            <!-- Column 4 -->
                            <div class="kanban-column">
                                <div class="column-header">
                                    <span>Negociación</span>
                                    <span class="column-count">1</span>
                                </div>
                                <div class="kanban-card">
                                    <h4>Grupo Educativo del Norte</h4>
                                    <p>Miguel Sánchez</p>
                                    <span class="badge-category">Corporativa</span>
                                </div>
                            </div>
                            
                            <!-- Column 5 -->
                            <div class="kanban-column" style="background-color: #f1f5f9;">
                                <div class="column-header">
                                    <span>Ganado</span>
                                    <span class="column-count">0</span>
                                </div>
                            </div>
                            
                            <!-- Column 6 -->
                            <div class="kanban-column" style="background-color: #f1f5f9;">
                                <div class="column-header">
                                    <span>Perdido</span>
                                    <span class="column-count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table Section -->
                    <div class="crm-table-section">
                        <h2 class="crm-section-title">Todos los Prospectos</h2>
                        <div class="table-responsive">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Contacto</th>
                                        <th>Interés</th>
                                        <th>Estado</th>
                                        <th>Último contacto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: var(--text-main);">Tech Innovations SA</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">jorge@techinnovations.com</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 500; color: var(--text-main);">Jorge Ramírez</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">(55) 1234-5678</div>
                                        </td>
                                        <td><span class="badge-category">Premium</span></td>
                                        <td><span class="badge-crm-contactado">Contactado</span></td>
                                        <td style="color: var(--text-muted);">19/4/2024</td>
                                        <td><a href="#" style="color: var(--primary-color); font-weight: 500; font-size: 0.85rem; text-decoration: none;">Ver detalles</a></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: var(--text-main);">Consultoría Empresarial</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">andrea@consultoria.mx</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 500; color: var(--text-main);">Andrea López</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">(33) 9876-5432</div>
                                        </td>
                                        <td><span class="badge-category">Básica</span></td>
                                        <td><span class="badge-crm-propuesta">Propuesta</span></td>
                                        <td style="color: var(--text-muted);">21/4/2024</td>
                                        <td><a href="#" style="color: var(--primary-color); font-weight: 500; font-size: 0.85rem; text-decoration: none;">Ver detalles</a></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500; color: var(--text-main);">Grupo Educativo del Norte</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">miguel@grupoedu.mx</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 500; color: var(--text-main);">Miguel Sánchez</div>
                                            <div style="font-size: 0.8rem; color: var(--text-muted);">(81) 5555-1111</div>
                                        </td>
                                        <td><span class="badge-category">Corporativa</span></td>
                                        <td><span class="badge-crm-negociacion">Negociación</span></td>
                                        <td style="color: var(--text-muted);">22/4/2024</td>
                                        <td><a href="#" style="color: var(--primary-color); font-weight: 500; font-size: 0.85rem; text-decoration: none;">Ver detalles</a></td>
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
