<?php
// usuarios.php
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
    <title>Usuarios y Roles - AsociaPro</title>
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
                <a href="usuarios.php" class="nav-item active">
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
                            <h1>Usuarios y Roles</h1>
                            <p>Gestión de accesos y permisos</p>
                        </div>
                        <button class="btn btn-primary">
                            <i class="ph ph-plus"></i> Nuevo Usuario
                        </button>
                    </div>

                    <!-- Tabs -->
                    <div class="tabs-container">
                        <a href="#" class="tab-item active">Usuarios</a>
                        <a href="#" class="tab-item">Roles y Permisos</a>
                    </div>

                    <!-- Filter / Search -->
                    <div class="search-input-wrapper search-full-width">
                        <i class="ph ph-magnifying-glass"></i>
                        <input type="text" placeholder="Buscar usuarios...">
                    </div>

                    <!-- Table -->
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Último acceso</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <img src="https://i.pravatar.cc/150?img=11" alt="Carlos Mendoza">
                                            <div class="user-cell-info">
                                                <span class="user-cell-name">Carlos Mendoza</span>
                                                <span class="user-cell-email">carlos@amuvie.org</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-role"><i class="ph ph-shield-check"></i> Administrador</span>
                                    </td>
                                    <td>2024-04-24 09:30</td>
                                    <td><span class="badge-status active">Activo</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-icon-btn"><i class="ph ph-envelope-simple"></i></button>
                                            <button class="action-icon-btn"><i class="ph ph-pencil-simple"></i></button>
                                            <button class="action-icon-btn danger"><i class="ph ph-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <img src="https://i.pravatar.cc/150?img=47" alt="Laura Martinez">
                                            <div class="user-cell-info">
                                                <span class="user-cell-name">Laura Martinez</span>
                                                <span class="user-cell-email">laura@amuvie.org</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-role"><i class="ph ph-shield"></i> Mesa Directiva</span>
                                    </td>
                                    <td>2024-04-23 14:20</td>
                                    <td><span class="badge-status active">Activo</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-icon-btn"><i class="ph ph-envelope-simple"></i></button>
                                            <button class="action-icon-btn"><i class="ph ph-pencil-simple"></i></button>
                                            <button class="action-icon-btn danger"><i class="ph ph-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="user-cell">
                                            <img src="https://i.pravatar.cc/150?img=12" alt="Roberto Silva">
                                            <div class="user-cell-info">
                                                <span class="user-cell-name">Roberto Silva</span>
                                                <span class="user-cell-email">roberto@amuvie.org</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-role"><i class="ph ph-user"></i> Capturista</span>
                                    </td>
                                    <td>2024-04-24 08:15</td>
                                    <td><span class="badge-status active">Activo</span></td>
                                    <td>
                                        <div class="table-actions">
                                            <button class="action-icon-btn"><i class="ph ph-envelope-simple"></i></button>
                                            <button class="action-icon-btn"><i class="ph ph-pencil-simple"></i></button>
                                            <button class="action-icon-btn danger"><i class="ph ph-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
