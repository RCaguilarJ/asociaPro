<?php
// dashboard.php
session_start();

// Descomenta esto para requerir login real
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
    <title>Dashboard - AsociaPro</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Chart.js para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="dashboard.php" class="nav-item active">
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
                <a href="auditoria.php" class="nav-item">
                    <i class="ph ph-shield-check nav-icon"></i>
                    Auditoría
                </a>
            </nav>
        </aside>

        <!-- Contenido Principal (Header + Dashboard) -->
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
                            <!-- Usamos una imagen de placeholder para el avatar -->
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

            <!-- Área desplazable del Dashboard -->
            <div class="dashboard-scrollable">
                <div class="dashboard-wrapper">
                    
                    <div class="dashboard-header">
                        <div>
                            <h1>Dashboard</h1>
                            <p>Bienvenido al panel de control de AMUVIE</p>
                        </div>
                    </div>

                    <!-- Grilla de Estadísticas -->
                    <div class="stats-grid">
                        <div class="card stat-card">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon blue"><i class="ph ph-users"></i></div>
                                <div class="stat-trend up"><i class="ph ph-trend-up"></i> 8.3%</div>
                            </div>
                            <div class="stat-label">Total Socios</div>
                            <div class="stat-value">245</div>
                        </div>
                        
                        <div class="card stat-card">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon green"><i class="ph ph-user-check"></i></div>
                                <div class="stat-trend up"><i class="ph ph-trend-up"></i> 5.2%</div>
                            </div>
                            <div class="stat-label">Socios Activos</div>
                            <div class="stat-value">221</div>
                        </div>

                        <div class="card stat-card">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon red"><i class="ph ph-user-minus"></i></div>
                                <div class="stat-trend down"><i class="ph ph-trend-down"></i> 2.1%</div>
                            </div>
                            <div class="stat-label">Socios Vencidos</div>
                            <div class="stat-value">24</div>
                        </div>

                        <div class="card stat-card">
                            <div class="stat-icon-wrapper">
                                <div class="stat-icon purple"><i class="ph ph-currency-dollar"></i></div>
                                <div class="stat-trend up"><i class="ph ph-trend-up"></i> 12.5%</div>
                            </div>
                            <div class="stat-label">Ingresos del Mes</div>
                            <div class="stat-value">$125K</div>
                        </div>
                    </div>

                    <!-- Grilla de Gráficos -->
                    <div class="charts-grid">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Crecimiento de Socios</h3>
                            </div>
                            <div style="height: 300px; width: 100%;">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Ingresos Mensuales</h3>
                            </div>
                            <div style="height: 300px; width: 100%;">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Grilla de Listas -->
                    <div class="lists-grid">
                        <!-- Actividad Reciente -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actividad Reciente</h3>
                                <a href="#" class="card-link">Ver todo</a>
                            </div>
                            <ul class="activity-list">
                                <li class="activity-item">
                                    <div class="activity-dot"></div>
                                    <div class="activity-content">
                                        <p>Luis Fernando Rojas se unió como miembro Corporativo</p>
                                        <span class="activity-time">2024-04-23 10:30</span>
                                    </div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-dot"></div>
                                    <div class="activity-content">
                                        <p>Pago recibido de María González López - $5,000</p>
                                        <span class="activity-time">2024-04-23 09:15</span>
                                    </div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-dot"></div>
                                    <div class="activity-content">
                                        <p>15 nuevos registros para el Congreso Anual 2024</p>
                                        <span class="activity-time">2024-04-22 16:45</span>
                                    </div>
                                </li>
                                <li class="activity-item">
                                    <div class="activity-dot"></div>
                                    <div class="activity-content">
                                        <p>Nueva solicitud de membresía de Sofía Mendoza</p>
                                        <span class="activity-time">2024-04-21 14:20</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Próximos Eventos -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Próximos Eventos</h3>
                                <a href="#" class="card-link">Ver todos &rarr;</a>
                            </div>
                            <div class="events-list">
                                <div class="event-item">
                                    <div class="event-date">
                                        <span>MAY</span>
                                        <span>15</span>
                                    </div>
                                    <div class="event-content">
                                        <h4>Congreso Anual de Educación Virtual 2024</h4>
                                        <p>Centro de Convenciones Monterrey</p>
                                        <p>342/500 registrados</p>
                                    </div>
                                </div>
                                
                                <div class="event-item" style="background-color: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin: 0.5rem 0;">
                                    <div class="event-date">
                                        <span>MAY</span>
                                        <span>15</span>
                                    </div>
                                    <div class="event-content">
                                        <h4>Workshop: Innovación en Metodologías Digitales</h4>
                                        <p>Virtual - Zoom</p>
                                        <p>87/100 registrados</p>
                                    </div>
                                </div>

                                <div class="event-item">
                                    <div class="event-date">
                                        <span>MAY</span>
                                        <span>15</span>
                                    </div>
                                    <div class="event-content">
                                        <h4>Networking: Encuentro de Directivos</h4>
                                        <p>Hotel Quinta Real, Monterrey</p>
                                        <p>48/50 registrados</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <h3 class="card-title" style="margin-bottom: 1rem; margin-top: 1rem;">Acciones Rápidas</h3>
                    <div class="actions-grid">
                        <div class="card action-card">
                            <i class="ph ph-users action-icon"></i>
                            <span class="action-label">Agregar Socio</span>
                        </div>
                        
                        <div class="card action-card">
                            <i class="ph ph-calendar-plus action-icon"></i>
                            <span class="action-label">Crear Evento</span>
                        </div>
                        
                        <div class="card action-card">
                            <i class="ph ph-file-text action-icon"></i>
                            <span class="action-label">Enviar Comunicado</span>
                        </div>
                        
                        <div class="card action-card">
                            <i class="ph ph-trend-up action-icon"></i>
                            <span class="action-label">Ver Reportes</span>
                        </div>
                    </div>
                    
                </div> <!-- End dashboard-wrapper -->
            </div> <!-- End dashboard-scrollable -->
        </div> <!-- End main-content -->
    </div> <!-- End app-container -->

    <script>
        // Configuración Gráfico de Líneas (Crecimiento de Socios)
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar'],
                datasets: [{
                    label: 'Socios',
                    data: [210, 220, 225, 235, 240, 245],
                    borderColor: '#3b82f6', 
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4, 
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#111827',
                        bodyColor: '#111827',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 260,
                        ticks: { stepSize: 65, color: '#6b7280' },
                        grid: { borderDash: [4, 4], color: '#e5e7eb' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' },
                        border: { display: false }
                    }
                }
            }
        });

        // Configuración Gráfico de Barras (Ingresos Mensuales)
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Oct', 'Nov', 'Dic', 'Ene', 'Feb', 'Mar'],
                datasets: [{
                    label: 'Ingresos',
                    data: [98000, 105000, 115000, 118000, 122000, 125000],
                    backgroundColor: '#8b5cf6', 
                    borderRadius: 4,
                    barPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#111827',
                        bodyColor: '#8b5cf6',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'revenue : ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 140000,
                        ticks: { stepSize: 35000, color: '#6b7280' },
                        grid: { borderDash: [4, 4], color: '#e5e7eb' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' },
                        border: { display: false }
                    }
                }
            }
        });

        // Lógica para mostrar/ocultar el menú del perfil
        const userProfileBtn = document.getElementById('userProfileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        userProfileBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Evita que el clic se propague y cierre el menú de inmediato
            profileDropdown.classList.toggle('show');
        });

        // Cerrar el menú si se hace clic fuera de él
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && e.target !== userProfileBtn && !userProfileBtn.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    </script>
</body>
</html>
