<?php
// eventos.php
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
    <title>Eventos y Capacitaciones - AsociaPro</title>
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
                <a href="eventos.php" class="nav-item active">
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
                            <h1>Eventos y Capacitaciones</h1>
                            <p>3 eventos programados</p>
                        </div>
                        <button class="btn btn-primary">
                            <i class="ph ph-plus"></i> Crear Evento
                        </button>
                    </div>

                    <!-- Filters Bar -->
                    <div class="filters-bar">
                        <div class="search-input-wrapper" style="flex: 1;">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" placeholder="Buscar eventos...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select">
                                <option>Todos los tipos</option>
                                <option>Congreso</option>
                                <option>Workshop</option>
                                <option>Networking</option>
                            </select>
                            <button class="btn btn-outline">
                                <i class="ph ph-funnel"></i> Filtros
                            </button>
                        </div>
                    </div>

                    <!-- Eventos Grid -->
                    <div class="eventos-grid">
                        
                        <!-- Evento 1 -->
                        <div class="evento-card">
                            <div class="evento-image-wrapper">
                                <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80" alt="Congreso">
                                <span class="badge-proximo">Próximo</span>
                            </div>
                            <div class="evento-body">
                                <div class="evento-title-row">
                                    <h3>Congreso Anual de Educación Virtual 2024</h3>
                                    <span class="badge-tipo">Congreso</span>
                                </div>
                                <p class="evento-desc">El evento más importante del año para profesionales de la educación virtual.</p>
                                <div class="evento-details">
                                    <div class="evento-detail-item">
                                        <i class="ph ph-calendar-blank"></i>
                                        <span>14 de mayo de 2024 - 09:00</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-map-pin"></i>
                                        <span>Centro de Convenciones Monterrey</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-users"></i>
                                        <span>342 / 500 registrados</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-currency-dollar"></i>
                                        <span>$ 2,500</span>
                                    </div>
                                </div>
                                <div class="evento-progress-container">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 68%;"></div>
                                    </div>
                                    <span class="progress-text">68%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Evento 2 -->
                        <div class="evento-card">
                            <div class="evento-image-wrapper">
                                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&q=80" alt="Workshop">
                                <span class="badge-proximo">Próximo</span>
                            </div>
                            <div class="evento-body">
                                <div class="evento-title-row">
                                    <h3>Workshop: Innovación en Metodologías Digitales</h3>
                                    <span class="badge-tipo">Workshop</span>
                                </div>
                                <p class="evento-desc">Aprende las últimas tendencias en metodologías de enseñanza digital.</p>
                                <div class="evento-details">
                                    <div class="evento-detail-item">
                                        <i class="ph ph-calendar-blank"></i>
                                        <span>27 de abril de 2024 - 16:00</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-map-pin"></i>
                                        <span>Virtual - Zoom</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-users"></i>
                                        <span>87 / 100 registrados</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-currency-dollar"></i>
                                        <span>$ 500</span>
                                    </div>
                                </div>
                                <div class="evento-progress-container">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 87%;"></div>
                                    </div>
                                    <span class="progress-text">87%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Evento 3 -->
                        <div class="evento-card">
                            <div class="evento-image-wrapper">
                                <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&q=80" alt="Networking">
                                <span class="badge-proximo">Próximo</span>
                            </div>
                            <div class="evento-body">
                                <div class="evento-title-row">
                                    <h3>Networking: Encuentro de Directivos</h3>
                                    <span class="badge-tipo">Networking</span>
                                </div>
                                <p class="evento-desc">Exclusivo para miembros premium. Cena de networking con líderes del sector.</p>
                                <div class="evento-details">
                                    <div class="evento-detail-item">
                                        <i class="ph ph-calendar-blank"></i>
                                        <span>24 de abril de 2024 - 19:00</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-map-pin"></i>
                                        <span>Hotel Quinta Real, Monterrey</span>
                                    </div>
                                    <div class="evento-detail-item">
                                        <i class="ph ph-users"></i>
                                        <span>48 / 50 registrados</span>
                                    </div>
                                </div>
                                <div class="evento-progress-container">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: 96%;"></div>
                                    </div>
                                    <span class="progress-text">96%</span>
                                </div>
                            </div>
                        </div>

                    </div> <!-- Fin eventos-grid -->

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
