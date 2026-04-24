<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AsociaPro - Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons (Phosphor Icons) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
    <div class="login-container">
        <!-- Left Side: Branding & Info -->
        <div class="login-branding">
            <div class="brand-logo">
                <i class="ph ph-buildings"></i>
                <div class="brand-text">
                    <h1>AsociaPro</h1>
                    <p>Gestión de Asociaciones Civiles</p>
                </div>
            </div>
            
            <div class="brand-message">
                <h2>Administra tu asociación de forma profesional</h2>
                <p>Plataforma integral para gestión de socios, eventos, pagos y comunicación. Todo en un solo lugar.</p>
            </div>
            
            <div class="brand-stats">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Asociaciones</p>
                </div>
                <div class="stat-item">
                    <h3>25K+</h3>
                    <p>Socios Activos</p>
                </div>
                <div class="stat-item">
                    <h3>99.9%</h3>
                    <p>Uptime</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="login-form-section">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Bienvenido</h2>
                    <p>Ingresa a tu cuenta para continuar</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error">
                        <i class="ph ph-warning-circle"></i>
                        <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                    </div>
                <?php endif; ?>

                <form action="login_process.php" method="POST" class="form-content">
                    <div class="input-group">
                        <label for="email">Correo electrónico</label>
                        <div class="input-wrapper">
                            <i class="ph ph-envelope"></i>
                            <!-- TODO: Quitar en producción -->
                            <input type="email" id="email" name="email" placeholder="tu@email.com" value="admin@asociapro.com" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <i class="ph ph-lock-key"></i>
                            <!-- TODO: Quitar en producción -->
                            <input type="password" id="password" name="password" placeholder="••••••••" value="admin123" required>
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <i class="ph ph-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Recordarme</span>
                        </label>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-primary">Iniciar sesión</button>
                </form>

                <div class="form-footer">
                    <p>¿No tienes una cuenta? <a href="#">Solicitar acceso</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('ph-eye');
                icon.classList.add('ph-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('ph-eye-slash');
                icon.classList.add('ph-eye');
            }
        });
    </script>
</body>
</html>
