<?php
// directorio.php
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
    <title>Directorio de Socios - AsociaPro</title>
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
                <a href="directorio.php" class="nav-item active">
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
                            <h1>Directorio de Socios</h1>
                            <p>Directorio público de miembros activos</p>
                        </div>
                    </div>

                    <!-- Filters Bar -->
                    <div class="filters-bar">
                        <div class="search-input-wrapper">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" placeholder="Buscar por nombre, empresa o ciudad...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select">
                                <option>Todas las industrias</option>
                                <option>Tecnología</option>
                                <option>Educación</option>
                                <option>Finanzas</option>
                            </select>
                            
                            <div class="view-toggle">
                                <button class="view-toggle-btn active">Grid</button>
                                <button class="view-toggle-btn">Lista</button>
                            </div>
                        </div>
                    </div>

                    <!-- Grid de Directorio -->
                    <div class="socios-grid">
                        
                        <!-- Card 1 -->
                        <div class="directorio-card">
                            <div class="directorio-header">
                                <img src="https://i.pravatar.cc/150?img=32" alt="Avatar" class="socio-avatar">
                                <div class="directorio-info">
                                    <h3>María González López</h3>
                                    <div class="directorio-job">Directora General</div>
                                    <span class="badge-membership">Premium</span>
                                </div>
                            </div>
                            
                            <div class="directorio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Universidad Virtual del Norte</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-globe"></i>
                                    <span>Educación</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Monterrey, Nuevo León</span>
                                </div>
                            </div>
                            
                            <div class="directorio-actions">
                                <button class="btn-contact"><i class="ph ph-envelope-simple"></i> Email</button>
                                <button class="btn-contact"><i class="ph ph-phone"></i> Llamar</button>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="directorio-card">
                            <div class="directorio-header">
                                <img src="https://i.pravatar.cc/150?img=12" alt="Avatar" class="socio-avatar">
                                <div class="directorio-info">
                                    <h3>Roberto Hernández Cruz</h3>
                                    <div class="directorio-job">CEO</div>
                                    <span class="badge-membership" style="background-color: #f1f5f9; color: #475569;">Básica</span>
                                </div>
                            </div>
                            
                            <div class="directorio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Tech Solutions SA</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-globe"></i>
                                    <span>Tecnología</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Guadalajara, Jalisco</span>
                                </div>
                            </div>
                            
                            <div class="directorio-actions">
                                <button class="btn-contact"><i class="ph ph-envelope-simple"></i> Email</button>
                                <button class="btn-contact"><i class="ph ph-phone"></i> Llamar</button>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="directorio-card">
                            <div class="directorio-header">
                                <img src="https://i.pravatar.cc/150?img=8" alt="Avatar" class="socio-avatar">
                                <div class="directorio-info">
                                    <h3>Luis Fernando Rojas</h3>
                                    <div class="directorio-job">Director de Tecnología</div>
                                    <span class="badge-membership" style="background-color: #ede9fe; color: #7c3aed;">Corporativa</span>
                                </div>
                            </div>
                            
                            <div class="directorio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Innovación Digital MX</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-globe"></i>
                                    <span>Tecnología</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Ciudad de México, CDMX</span>
                                </div>
                            </div>
                            
                            <div class="directorio-actions">
                                <button class="btn-contact"><i class="ph ph-envelope-simple"></i> Email</button>
                                <button class="btn-contact"><i class="ph ph-phone"></i> Llamar</button>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="directorio-card">
                            <div class="directorio-header">
                                <img src="https://i.pravatar.cc/150?img=41" alt="Avatar" class="socio-avatar">
                                <div class="directorio-info">
                                    <h3>Carmen Sánchez Ruiz</h3>
                                    <div class="directorio-job">Gerente Regional</div>
                                    <span class="badge-membership">Premium</span>
                                </div>
                            </div>
                            
                            <div class="directorio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Grupo Financiero del Norte</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-globe"></i>
                                    <span>Finanzas</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Monterrey, Nuevo León</span>
                                </div>
                            </div>
                            
                            <div class="directorio-actions">
                                <button class="btn-contact"><i class="ph ph-envelope-simple"></i> Email</button>
                                <button class="btn-contact"><i class="ph ph-phone"></i> Llamar</button>
                            </div>
                        </div>

                    </div> <!-- Fin directorio-grid -->
                    
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
