<?php
// socios.php
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
    <title>Gestión de Socios - AsociaPro</title>
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
                <a href="socios.php" class="nav-item active">
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
                            <h1>Gestión de Socios</h1>
                            <p>5 socios en total</p>
                        </div>
                        <button class="btn btn-primary">
                            <i class="ph ph-plus"></i> Agregar Socio
                        </button>
                    </div>

                    <!-- Filters Bar -->
                    <div class="filters-bar">
                        <div class="search-input-wrapper">
                            <i class="ph ph-magnifying-glass"></i>
                            <input type="text" placeholder="Buscar por nombre, email o empresa...">
                        </div>
                        <div class="filters-actions">
                            <select class="filter-select">
                                <option>Todos los estatus</option>
                                <option>Activo</option>
                                <option>Vencido</option>
                            </select>
                            <button class="btn btn-outline">
                                <i class="ph ph-funnel"></i> Más filtros
                            </button>
                            <button class="btn btn-outline">
                                <i class="ph ph-download-simple"></i> Exportar
                            </button>
                        </div>
                    </div>

                    <!-- Socios Grid -->
                    <div class="socios-grid">
                        
                        <!-- Card 1 -->
                        <div class="socio-card">
                            <div class="socio-header">
                                <div class="socio-profile">
                                    <img src="https://i.pravatar.cc/150?img=32" alt="Avatar" class="socio-avatar">
                                    <div class="socio-info">
                                        <h3>María González López</h3>
                                        <span class="badge-status active">Activo</span>
                                    </div>
                                </div>
                                <i class="ph ph-dots-three-vertical socio-options"></i>
                            </div>
                            
                            <div class="socio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Universidad Virtual del Norte</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-envelope-simple"></i>
                                    <span>maria.gonzalez@empresa.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-phone"></i>
                                    <span>(55) 1234-5678</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Monterrey, Nuevo León</span>
                                </div>
                            </div>
                            
                            <div class="socio-meta">
                                <div class="meta-group">
                                    <span class="meta-label">Tipo de membresía</span>
                                    <span class="meta-value">Premium</span>
                                </div>
                                <div class="meta-group right">
                                    <span class="meta-label">Vence</span>
                                    <span class="meta-value">14 ene 2024</span>
                                </div>
                            </div>
                            
                            <div class="socio-actions">
                                <button class="btn-card edit">
                                    <i class="ph ph-pencil-simple"></i> Editar
                                </button>
                                <button class="btn-card delete">
                                    <i class="ph ph-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="socio-card">
                            <div class="socio-header">
                                <div class="socio-profile">
                                    <img src="https://i.pravatar.cc/150?img=12" alt="Avatar" class="socio-avatar">
                                    <div class="socio-info">
                                        <h3>Roberto Hernández Cruz</h3>
                                        <span class="badge-status active">Activo</span>
                                    </div>
                                </div>
                                <i class="ph ph-dots-three-vertical socio-options"></i>
                            </div>
                            
                            <div class="socio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Tech Solutions SA</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-envelope-simple"></i>
                                    <span>roberto.hernandez@tech.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-phone"></i>
                                    <span>(33) 9876-5432</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Guadalajara, Jalisco</span>
                                </div>
                            </div>
                            
                            <div class="socio-meta">
                                <div class="meta-group">
                                    <span class="meta-label">Tipo de membresía</span>
                                    <span class="meta-value">Básica</span>
                                </div>
                                <div class="meta-group right">
                                    <span class="meta-label">Vence</span>
                                    <span class="meta-value">19 mar 2024</span>
                                </div>
                            </div>
                            
                            <div class="socio-actions">
                                <button class="btn-card edit">
                                    <i class="ph ph-pencil-simple"></i> Editar
                                </button>
                                <button class="btn-card delete">
                                    <i class="ph ph-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="socio-card">
                            <div class="socio-header">
                                <div class="socio-profile">
                                    <img src="https://i.pravatar.cc/150?img=5" alt="Avatar" class="socio-avatar">
                                    <div class="socio-info">
                                        <h3>Ana Patricia Martínez</h3>
                                        <span class="badge-status inactive">Vencido</span>
                                    </div>
                                </div>
                                <i class="ph ph-dots-three-vertical socio-options"></i>
                            </div>
                            
                            <div class="socio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Consultoría Estratégica</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-envelope-simple"></i>
                                    <span>ana.martinez@consulting.mx</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-phone"></i>
                                    <span>(81) 5555-1234</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>San Pedro, Nuevo León</span>
                                </div>
                            </div>
                            
                            <div class="socio-meta">
                                <div class="meta-group">
                                    <span class="meta-label">Tipo de membresía</span>
                                    <span class="meta-value">Premium</span>
                                </div>
                                <div class="meta-group right">
                                    <span class="meta-label">Vence</span>
                                    <span class="meta-value">9 dic 2023</span>
                                </div>
                            </div>
                            
                            <div class="socio-actions">
                                <button class="btn-card edit">
                                    <i class="ph ph-pencil-simple"></i> Editar
                                </button>
                                <button class="btn-card delete">
                                    <i class="ph ph-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="socio-card">
                            <div class="socio-header">
                                <div class="socio-profile">
                                    <img src="https://i.pravatar.cc/150?img=8" alt="Avatar" class="socio-avatar">
                                    <div class="socio-info">
                                        <h3>Luis Fernando Rojas</h3>
                                        <span class="badge-status active">Activo</span>
                                    </div>
                                </div>
                                <i class="ph ph-dots-three-vertical socio-options"></i>
                            </div>
                            
                            <div class="socio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Innovación Digital MX</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-envelope-simple"></i>
                                    <span>luis.rojas@innovacion.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-phone"></i>
                                    <span>(55) 8888-9999</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Ciudad de México, CDMX</span>
                                </div>
                            </div>
                            
                            <div class="socio-meta">
                                <div class="meta-group">
                                    <span class="meta-label">Tipo de membresía</span>
                                    <span class="meta-value">Corporativa</span>
                                </div>
                                <div class="meta-group right">
                                    <span class="meta-label">Vence</span>
                                    <span class="meta-value">31 jul 2024</span>
                                </div>
                            </div>
                            
                            <div class="socio-actions">
                                <button class="btn-card edit">
                                    <i class="ph ph-pencil-simple"></i> Editar
                                </button>
                                <button class="btn-card delete">
                                    <i class="ph ph-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>

                        <!-- Card 5 -->
                        <div class="socio-card">
                            <div class="socio-header">
                                <div class="socio-profile">
                                    <img src="https://i.pravatar.cc/150?img=41" alt="Avatar" class="socio-avatar">
                                    <div class="socio-info">
                                        <h3>Carmen Sánchez Ruiz</h3>
                                        <span class="badge-status active">Activo</span>
                                    </div>
                                </div>
                                <i class="ph ph-dots-three-vertical socio-options"></i>
                            </div>
                            
                            <div class="socio-details">
                                <div class="detail-item">
                                    <i class="ph ph-buildings"></i>
                                    <span>Grupo Financiero del Norte</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-envelope-simple"></i>
                                    <span>carmen.sanchez@financiera.mx</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-phone"></i>
                                    <span>(81) 2222-3333</span>
                                </div>
                                <div class="detail-item">
                                    <i class="ph ph-map-pin"></i>
                                    <span>Monterrey, Nuevo León</span>
                                </div>
                            </div>
                            
                            <div class="socio-meta">
                                <div class="meta-group">
                                    <span class="meta-label">Tipo de membresía</span>
                                    <span class="meta-value">Premium</span>
                                </div>
                                <div class="meta-group right">
                                    <span class="meta-label">Vence</span>
                                    <span class="meta-value">13 feb 2024</span>
                                </div>
                            </div>
                            
                            <div class="socio-actions">
                                <button class="btn-card edit">
                                    <i class="ph ph-pencil-simple"></i> Editar
                                </button>
                                <button class="btn-card delete">
                                    <i class="ph ph-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>

                    </div> <!-- Fin socios-grid -->
                    
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
