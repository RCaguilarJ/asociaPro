<?php
// auditoria.php
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
    <title>Auditoría y Seguridad - AsociaPro</title>
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
                <a href="portal.php" class="nav-item">
                    <i class="ph ph-user-circle nav-icon"></i>
                    Portal del Socio
                </a>
                <a href="reportes.php" class="nav-item">
                    <i class="ph ph-chart-bar nav-icon"></i>
                    Reportes
                </a>
                <a href="auditoria.php" class="nav-item active">
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
                            <h1>Auditoría y Seguridad</h1>
                            <p>Registro de actividad del sistema</p>
                        </div>
                        <button class="btn btn-outline">
                            <i class="ph ph-download-simple"></i> Exportar logs
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="charts-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1.5rem;">
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-shield" style="color: #3b82f6;"></i> Total de eventos
                            </div>
                            <div class="crm-stat-value">1,234</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Este mes</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-users" style="color: #10b981;"></i> Usuarios activos
                            </div>
                            <div class="crm-stat-value">12</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Últimas 24h</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-warning-circle" style="color: #f59e0b;"></i> Cambios críticos
                            </div>
                            <div class="crm-stat-value">5</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Esta semana</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-shield-warning" style="color: #ef4444;"></i> Alertas
                            </div>
                            <div class="crm-stat-value">0</div>
                            <div style="font-size: 0.8rem; color: #10b981; margin-top: 0.25rem;">Todo normal</div>
                        </div>
                    </div>

                    <!-- Filters and Table -->
                    <div class="crm-table-section" style="margin-bottom: 2rem;">
                        <div class="auditoria-filters">
                            <div class="auditoria-search">
                                <i class="ph ph-magnifying-glass"></i>
                                <input type="text" placeholder="Buscar en el registro de auditoría...">
                            </div>
                            <select class="form-select" style="width: auto;">
                                <option>Todos los tipos</option>
                                <option>Creación</option>
                                <option>Actualización</option>
                                <option>Eliminación</option>
                                <option>Lectura</option>
                            </select>
                            <select class="form-select" style="width: auto;">
                                <option>Todos los usuarios</option>
                                <option>Administradores</option>
                                <option>Editores</option>
                            </select>
                            <button class="btn btn-outline"><i class="ph ph-funnel"></i> Filtros</button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Fecha y Hora</th>
                                        <th>Usuario</th>
                                        <th>Acción</th>
                                        <th>Objetivo</th>
                                        <th>Tipo</th>
                                        <th>Dirección IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-24 10:30:15</td>
                                        <td style="font-weight: 500;">Carlos Mendoza</td>
                                        <td>Creó nuevo socio</td>
                                        <td style="color: var(--text-muted);">María González López</td>
                                        <td><span class="badge-audit creacion">Creación</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.100</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-24 09:15:42</td>
                                        <td style="font-weight: 500;">Laura Martínez</td>
                                        <td>Actualizó información de evento</td>
                                        <td style="color: var(--text-muted);">Congreso Anual 2024</td>
                                        <td><span class="badge-audit actualizacion">Actualización</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.101</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-23 16:20:33</td>
                                        <td style="font-weight: 500;">Roberto Silva</td>
                                        <td>Registró pago</td>
                                        <td style="color: var(--text-muted);">Pago FAC-2024-045</td>
                                        <td><span class="badge-audit creacion">Creación</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.102</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-23 14:05:18</td>
                                        <td style="font-weight: 500;">Carlos Mendoza</td>
                                        <td>Eliminó documento</td>
                                        <td style="color: var(--text-muted);">Acta_Borrador_2024.pdf</td>
                                        <td><span class="badge-audit eliminacion">Eliminación</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.100</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-23 11:45:29</td>
                                        <td style="font-weight: 500;">Laura Martínez</td>
                                        <td>Aprobó solicitud de membresía</td>
                                        <td style="color: var(--text-muted);">Luis Fernando Rojas</td>
                                        <td><span class="badge-audit actualizacion">Actualización</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.101</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-22 15:30:11</td>
                                        <td style="font-weight: 500;">Carlos Mendoza</td>
                                        <td>Cambió rol de usuario</td>
                                        <td style="color: var(--text-muted);">Roberto Silva</td>
                                        <td><span class="badge-audit actualizacion">Actualización</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.100</td>
                                    </tr>
                                    <tr>
                                        <td style="color: var(--text-muted);">2024-04-22 10:20:05</td>
                                        <td style="font-weight: 500;">Roberto Silva</td>
                                        <td>Exportó reporte</td>
                                        <td style="color: var(--text-muted);">Reporte_Socios_Marzo_2024.xlsx</td>
                                        <td><span class="badge-audit lectura">Lectura</span></td>
                                        <td style="color: var(--text-muted); font-size: 0.8rem;">192.168.1.102</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recomendaciones -->
                    <div class="seguridad-recom-box">
                        <div class="seguridad-header">
                            <i class="ph ph-shield-check" style="font-size: 1.5rem;"></i> Recomendaciones de Seguridad
                        </div>
                        <ul>
                            <li>Revisa regularmente los logs de auditoría para detectar actividad inusual</li>
                            <li>Configura alertas automáticas para cambios críticos</li>
                            <li>Mantén actualizados los permisos de usuarios y roles</li>
                            <li>Exporta y archiva los logs periódicamente</li>
                        </ul>
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
