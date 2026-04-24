<?php
// login_process.php
session_start();
require_once 'config/database.php';

// Redirigir si alguien intenta acceder directamente a este archivo sin enviar el formulario POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Obtener y limpiar datos del formulario
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$password = trim($_POST['password']);

if (empty($email) || empty($password)) {
    header("Location: index.php?error=" . urlencode("Por favor completa todos los campos."));
    exit;
}

// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // Buscar al usuario por su email
        $query = "SELECT id, nombre, email, password_hash, rol_id, activo FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si el usuario está activo
            if ($row['activo'] == 0) {
                header("Location: index.php?error=" . urlencode("Esta cuenta ha sido desactivada."));
                exit;
            }

            // Verificar la contraseña proporcionada contra el hash en la base de datos
            if (password_verify($password, $row['password_hash'])) {
                // Contraseña correcta: Iniciar sesión
                
                // Prevenir ataques de Session Fixation
                session_regenerate_id(true);

                // Guardar datos en la sesión
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['nombre'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_role'] = $row['rol_id'];

                // Redirigir al dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Contraseña incorrecta
                header("Location: index.php?error=" . urlencode("Correo o contraseña incorrectos."));
                exit;
            }
        } else {
            // Usuario no encontrado
            header("Location: index.php?error=" . urlencode("Correo o contraseña incorrectos."));
            exit;
        }
    } catch(PDOException $e) {
        // Error en la consulta
        header("Location: index.php?error=" . urlencode("Error en el sistema. Intenta más tarde."));
        exit;
    }
} else {
    header("Location: index.php?error=" . urlencode("No se pudo conectar a la base de datos."));
    exit;
}
?>
