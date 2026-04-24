<?php
// portal.php
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
    <title>Portal del Socio - AsociaPro</title>
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
                <a href="finanzas.php" class="nav-item">
                    <i class="ph ph-currency-dollar nav-icon"></i>
                    Finanzas
                </a>
                <a href="patrocinadores.php" class="nav-item">
                    <i class="ph ph-medal nav-icon"></i>
                    Patrocinadores
                </a>
                <a href="portal.php" class="nav-item active">
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
                    
                    <!-- Top Blue Card -->
                    <div class="portal-header-card">
                        <div class="portal-user-info">
                            <img src="https://i.pravatar.cc/150?img=11" alt="Carlos Mendoza">
                            <div>
                                <h2>Carlos Mendoza</h2>
                                <p>Socio Premium - AMUVIE</p>
                            </div>
                        </div>
                        <div class="badge-portal-status">
                            Activo
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="charts-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 2rem;">
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-user" style="color: #3b82f6;"></i> Tipo de Membresía
                            </div>
                            <div class="crm-stat-value" style="font-size: 1.1rem; padding-top: 0.25rem;">Premium</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-calendar-blank" style="color: #10b981;"></i> Vigencia hasta
                            </div>
                            <div class="crm-stat-value" style="font-size: 1.1rem; padding-top: 0.25rem;">15 Ene 2025</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-check-circle" style="color: #9333ea;"></i> Eventos asistidos
                            </div>
                            <div class="crm-stat-value" style="font-size: 1.1rem; padding-top: 0.25rem;">8</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-credit-card" style="color: #f59e0b;"></i> Estado de pago
                            </div>
                            <div class="crm-stat-value" style="font-size: 1.1rem; color: #16a34a; padding-top: 0.25rem;">Al corriente</div>
                        </div>
                    </div>

                    <!-- Main Portal Content -->
                    <div class="portal-grid">
                        
                        <!-- Left Column -->
                        <div class="portal-main">
                            <!-- Próximos Eventos -->
                            <h2 class="crm-section-title" style="margin-bottom: 1rem; font-size: 1.05rem;">Próximos Eventos</h2>
                            <div class="card" style="margin-bottom: 2rem; padding: 0;">
                                <div class="events-list" style="padding: 1.5rem;">
                                    <div class="event-item" style="background-color: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-color);">
                                        <div class="event-date">
                                            <span>MAY</span>
                                            <span>15</span>
                                        </div>
                                        <div class="event-content">
                                            <h4>Congreso Anual de Educación Virtual 2024</h4>
                                            <p>Centro de Convenciones Monterrey</p>
                                            <a href="#" style="display: block; margin-top: 0.5rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; font-weight: 500;">Registrarme &rarr;</a>
                                        </div>
                                    </div>
                                    
                                    <div class="event-item" style="border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem;">
                                        <div class="event-date">
                                            <span>MAY</span>
                                            <span>15</span>
                                        </div>
                                        <div class="event-content">
                                            <h4>Workshop: Innovación en Metodologías Digitales</h4>
                                            <p>Virtual - Zoom</p>
                                            <a href="#" style="display: block; margin-top: 0.5rem; color: #3b82f6; text-decoration: none; font-size: 0.85rem; font-weight: 500;">Registrarme &rarr;</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Historial de Pagos -->
                            <h2 class="crm-section-title" style="margin-bottom: 1rem; font-size: 1.05rem;">Historial de Pagos</h2>
                            <div class="crm-table-section" style="margin-bottom: 2rem; padding: 0; box-shadow: none;">
                                <div class="table-responsive">
                                    <table class="dashboard-table">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="color: var(--text-muted);">14/1/2024</td>
                                                <td>Cuota anual 2024</td>
                                                <td style="font-weight: 600;">$5,000</td>
                                                <td><span class="badge-audit creacion">Pagado</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color: var(--text-muted);">19/3/2024</td>
                                                <td>Cuota anual 2024</td>
                                                <td style="font-weight: 600;">$3,000</td>
                                                <td><span class="badge-audit creacion">Pagado</span></td>
                                            </tr>
                                            <tr>
                                                <td style="color: var(--text-muted);">9/12/2023</td>
                                                <td>Cuota anual 2024</td>
                                                <td style="font-weight: 600;">$5,000</td>
                                                <td><span class="badge-audit creacion">Pagado</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Documentos Disponibles -->
                            <h2 class="crm-section-title" style="margin-bottom: 1rem; font-size: 1.05rem;">Documentos Disponibles</h2>
                            <div class="card" style="margin-bottom: 2rem; padding: 0 1.5rem;">
                                <div class="doc-list-item">
                                    <div class="doc-info">
                                        <i class="ph ph-file-text doc-icon"></i>
                                        <div class="doc-details">
                                            <h4>Estatutos AMUVIE 2024</h4>
                                            <p>PDF - 2.4 MB</p>
                                        </div>
                                    </div>
                                    <button class="doc-download"><i class="ph ph-download-simple"></i></button>
                                </div>
                                <div class="doc-list-item">
                                    <div class="doc-info">
                                        <i class="ph ph-file-text doc-icon"></i>
                                        <div class="doc-details">
                                            <h4>Reglamento Interno</h4>
                                            <p>PDF - 1.2 MB</p>
                                        </div>
                                    </div>
                                    <button class="doc-download"><i class="ph ph-download-simple"></i></button>
                                </div>
                            </div>
                        </div> <!-- End Left Column -->

                        <!-- Right Column (Sidebar) -->
                        <div class="portal-sidebar">
                            
                            <!-- Credencial -->
                            <div class="credencial-card" style="margin-bottom: 2rem;">
                                <h3>Credencial Digital</h3>
                                <div class="qr-container">
                                    <i class="ph ph-qr-code"></i>
                                </div>
                                <div class="credencial-no">
                                    <span>Socio No.</span>
                                    <strong>#000001</strong>
                                </div>
                                <button class="btn-credencial">Descargar Credencial</button>
                            </div>

                            <!-- Acciones Rápidas -->
                            <h2 class="crm-section-title" style="margin-bottom: 1rem; font-size: 1.05rem;">Acciones Rápidas</h2>
                            <div class="portal-actions-list" style="margin-bottom: 2rem;">
                                <a href="#" class="portal-action-btn">
                                    <i class="ph ph-user"></i> Actualizar perfil
                                </a>
                                <a href="#" class="portal-action-btn">
                                    <i class="ph ph-credit-card"></i> Renovar membresía
                                </a>
                                <a href="#" class="portal-action-btn">
                                    <i class="ph ph-calendar-blank"></i> Ver mis eventos
                                </a>
                                <a href="#" class="portal-action-btn">
                                    <i class="ph ph-file-doc"></i> Mis documentos
                                </a>
                            </div>

                            <!-- Support -->
                            <div class="portal-help-box">
                                <h4>¿Necesitas ayuda?</h4>
                                <p>Contacta a nuestro equipo de soporte para cualquier duda.</p>
                                <button class="btn btn-primary" style="width: 100%;">Contactar soporte</button>
                            </div>

                        </div> <!-- End Right Column -->

                    </div> <!-- End portal-grid -->

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
