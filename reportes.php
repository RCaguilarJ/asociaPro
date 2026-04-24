<?php
// reportes.php
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
    <title>Reportes - AsociaPro</title>
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
                <a href="reportes.php" class="nav-item active">
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
                    <div class="page-header" style="justify-content: flex-start;">
                        <div class="page-title">
                            <h1>Reportes</h1>
                            <p>Generación y descarga de reportes</p>
                        </div>
                    </div>

                    <!-- Generar Nuevo Reporte -->
                    <h2 class="crm-section-title" style="margin-top: 1rem;">Generar Nuevo Reporte</h2>
                    <div class="reportes-types-grid">
                        <div class="reporte-type-card">
                            <div class="reporte-icon blue"><i class="ph ph-users"></i></div>
                            <h3>Reporte de Socios</h3>
                            <p>Listado completo de socios activos, vencidos y estadísticas</p>
                            <button class="btn btn-primary" style="width: 100%;"><i class="ph ph-file-text"></i> Generar</button>
                        </div>
                        <div class="reporte-type-card">
                            <div class="reporte-icon green"><i class="ph ph-currency-dollar"></i></div>
                            <h3>Reporte Financiero</h3>
                            <p>Ingresos, egresos y balance del período</p>
                            <button class="btn btn-primary" style="width: 100%;"><i class="ph ph-file-text"></i> Generar</button>
                        </div>
                        <div class="reporte-type-card">
                            <div class="reporte-icon purple"><i class="ph ph-chart-bar"></i></div>
                            <h3>Reporte de Eventos</h3>
                            <p>Asistencia, ingresos y métricas de eventos</p>
                            <button class="btn btn-primary" style="width: 100%;"><i class="ph ph-file-text"></i> Generar</button>
                        </div>
                        <div class="reporte-type-card">
                            <div class="reporte-icon orange"><i class="ph ph-file-text"></i></div>
                            <h3>Reporte de Pagos</h3>
                            <p>Estado de cobranza y pagos pendientes</p>
                            <button class="btn btn-primary" style="width: 100%;"><i class="ph ph-file-text"></i> Generar</button>
                        </div>
                    </div>

                    <!-- Reporte Personalizado -->
                    <h2 class="crm-section-title">Reporte Personalizado</h2>
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="reporte-form-grid">
                            <div class="form-group">
                                <label>Tipo de reporte</label>
                                <select class="form-select">
                                    <option>Socios</option>
                                    <option>Financiero</option>
                                    <option>Eventos</option>
                                    <option>Pagos</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Formato</label>
                                <select class="form-select">
                                    <option>Excel (.xlsx)</option>
                                    <option>PDF (.pdf)</option>
                                    <option>CSV (.csv)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Período</label>
                                <select class="form-select">
                                    <option>Mes actual</option>
                                    <option>Mes anterior</option>
                                    <option>Trimestre actual</option>
                                    <option>Año actual</option>
                                    <option>Personalizado</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <button class="btn btn-primary" style="flex-grow: 1;"><i class="ph ph-download-simple"></i> Generar y Descargar</button>
                            <button class="btn btn-outline"><i class="ph ph-funnel"></i> Filtros Avanzados</button>
                        </div>
                    </div>

                    <!-- Reportes Recientes -->
                    <h2 class="crm-section-title">Reportes Recientes</h2>
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="reportes-recent-item">
                            <div class="reporte-file-info">
                                <div class="reporte-file-icon"><i class="ph ph-file-text"></i></div>
                                <div>
                                    <h4 style="font-size: 0.95rem; font-weight: 500; color: var(--text-main);">Reporte_Socios_Marzo_2024.xlsx</h4>
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">Socios &bull; Generado el 30 de marzo de 2024 &bull; 245 KB</p>
                                </div>
                            </div>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem;"><i class="ph ph-download-simple"></i> Descargar</button>
                        </div>
                        
                        <div class="reportes-recent-item">
                            <div class="reporte-file-info">
                                <div class="reporte-file-icon"><i class="ph ph-file-pdf"></i></div>
                                <div>
                                    <h4 style="font-size: 0.95rem; font-weight: 500; color: var(--text-main);">Financiero_Q1_2024.pdf</h4>
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">Financiero &bull; Generado el 30 de marzo de 2024 &bull; 1.2 MB</p>
                                </div>
                            </div>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem;"><i class="ph ph-download-simple"></i> Descargar</button>
                        </div>
                        
                        <div class="reportes-recent-item">
                            <div class="reporte-file-info">
                                <div class="reporte-file-icon"><i class="ph ph-file-text"></i></div>
                                <div>
                                    <h4 style="font-size: 0.95rem; font-weight: 500; color: var(--text-main);">Eventos_Febrero_2024.xlsx</h4>
                                    <p style="font-size: 0.8rem; color: var(--text-muted);">Eventos &bull; Generado el 28 de febrero de 2024 &bull; 189 KB</p>
                                </div>
                            </div>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.5rem 1rem;"><i class="ph ph-download-simple"></i> Descargar</button>
                        </div>
                    </div>

                    <!-- Reportes Automatizados -->
                    <div class="automatizacion-box" style="margin-bottom: 2rem;">
                        <div class="automatizacion-icon"><i class="ph ph-chart-line-up"></i></div>
                        <div style="flex-grow: 1;">
                            <h3 style="font-size: 1.05rem; font-weight: 600; color: #1e3a8a; margin-bottom: 0.25rem;">Reportes Automatizados</h3>
                            <p style="font-size: 0.9rem; color: #3b82f6;">Configura reportes recurrentes que se generen y envíen automáticamente por email.</p>
                        </div>
                        <button class="btn btn-primary">Configurar automatización</button>
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
