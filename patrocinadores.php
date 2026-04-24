<?php
// patrocinadores.php
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
    <title>Patrocinadores - AsociaPro</title>
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
                <a href="patrocinadores.php" class="nav-item active">
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
                            <h1>Patrocinadores</h1>
                            <p>Gestión de patrocinios y beneficios</p>
                        </div>
                        <button class="btn btn-primary">
                            <i class="ph ph-plus"></i> Nuevo Patrocinador
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="charts-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-medal" style="color: #3b82f6;"></i> Patrocinadores Activos
                            </div>
                            <div class="crm-stat-value">2</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-currency-dollar" style="color: #10b981;"></i> Ingresos Anuales
                            </div>
                            <div class="crm-stat-value">$80,000</div>
                        </div>
                        <div class="crm-stat-card">
                            <div class="crm-stat-label">
                                <i class="ph ph-calendar-blank" style="color: #9333ea;"></i> Próximo a Renovar
                            </div>
                            <div class="crm-stat-value">2</div>
                        </div>
                    </div>

                    <!-- Paquetes de Patrocinio -->
                    <h2 class="crm-section-title">Paquetes de Patrocinio</h2>
                    <div class="paquetes-grid">
                        <div class="paquete-card">
                            <span class="paquete-badge platino">Platino</span>
                            <div class="paquete-price">$50K<span>/año</span></div>
                            <ul class="paquete-benefits">
                                <li><i class="ph ph-check-circle"></i> Logo destacado en todos los eventos</li>
                                <li><i class="ph ph-check-circle"></i> Stand premium en congreso anual</li>
                                <li><i class="ph ph-check-circle"></i> Menciones en redes sociales (mensual)</li>
                                <li><i class="ph ph-check-circle"></i> Espacio publicitario en newsletter</li>
                                <li><i class="ph ph-check-circle"></i> Participación en mesa directiva invitada</li>
                            </ul>
                        </div>
                        <div class="paquete-card">
                            <span class="paquete-badge oro">Oro</span>
                            <div class="paquete-price">$30K<span>/año</span></div>
                            <ul class="paquete-benefits">
                                <li><i class="ph ph-check-circle"></i> Logo en eventos principales</li>
                                <li><i class="ph ph-check-circle"></i> Stand en congreso anual</li>
                                <li><i class="ph ph-check-circle"></i> Menciones en redes sociales (trimestral)</li>
                                <li><i class="ph ph-check-circle"></i> Espacio en newsletter</li>
                            </ul>
                        </div>
                        <div class="paquete-card">
                            <span class="paquete-badge plata">Plata</span>
                            <div class="paquete-price">$15K<span>/año</span></div>
                            <ul class="paquete-benefits">
                                <li><i class="ph ph-check-circle"></i> Logo en eventos</li>
                                <li><i class="ph ph-check-circle"></i> Mención en redes sociales (semestral)</li>
                                <li><i class="ph ph-check-circle"></i> Espacio en newsletter</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Patrocinadores Activos -->
                    <h2 class="crm-section-title">Patrocinadores Activos</h2>
                    
                    <div class="patrocinador-card">
                        <div class="patrocinador-header">
                            <div class="patrocinador-info">
                                <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Grupo Financiero del Norte" class="patrocinador-logo">
                                <div class="patrocinador-name">
                                    <h3>Grupo Financiero del Norte</h3>
                                    <div class="patrocinador-badges">
                                        <span class="badge-audit actualizacion" style="background: #f3e8ff; color: #9333ea;">Platino</span>
                                        <span class="badge-audit creacion">Activo</span>
                                    </div>
                                </div>
                            </div>
                            <div class="patrocinador-cost">
                                <h4>$50,000</h4>
                                <span>por año</span>
                            </div>
                        </div>
                        <div class="patrocinador-body">
                            <div class="patrocinador-detail-group">
                                <h5>Vigencia</h5>
                                <p>31 dic 2023 - 30 dic 2024</p>
                            </div>
                            <div class="patrocinador-detail-group">
                                <h5>Beneficios</h5>
                                <div class="beneficios-tags">
                                    <span class="beneficio-tag">Logo en eventos</span>
                                    <span class="beneficio-tag">Stand en congreso</span>
                                    <span class="beneficio-tag">Menciones en redes</span>
                                </div>
                            </div>
                        </div>
                        <div class="patrocinador-actions">
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">Ver detalles</button>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">Editar</button>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">Generar reporte</button>
                        </div>
                    </div>

                    <div class="patrocinador-card">
                        <div class="patrocinador-header">
                            <div class="patrocinador-info">
                                <img src="https://images.unsplash.com/photo-1622547748225-3fc4abd2cca0?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Tech Solutions Internacional" class="patrocinador-logo">
                                <div class="patrocinador-name">
                                    <h3>Tech Solutions Internacional</h3>
                                    <div class="patrocinador-badges">
                                        <span class="badge-audit" style="background: #fef9c3; color: #ca8a04;">Oro</span>
                                        <span class="badge-audit creacion">Activo</span>
                                    </div>
                                </div>
                            </div>
                            <div class="patrocinador-cost">
                                <h4>$30,000</h4>
                                <span>por año</span>
                            </div>
                        </div>
                        <div class="patrocinador-body">
                            <div class="patrocinador-detail-group">
                                <h5>Vigencia</h5>
                                <p>31 dic 2023 - 30 dic 2024</p>
                            </div>
                            <div class="patrocinador-detail-group">
                                <h5>Beneficios</h5>
                                <div class="beneficios-tags">
                                    <span class="beneficio-tag">Logo en eventos</span>
                                    <span class="beneficio-tag">Menciones en redes</span>
                                </div>
                            </div>
                        </div>
                        <div class="patrocinador-actions">
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">Ver detalles</button>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">Editar</button>
                            <button class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">Generar reporte</button>
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
