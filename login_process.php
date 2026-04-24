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
// $database = new Database();
// $db = $database->getConnection();

// --- MODO DEMO FRONTEND PARA VERCEL ---
// Simular un inicio de sesión exitoso sin importar las credenciales
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = "Carlos Mendoza";
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = 1;

// Redirigir al dashboard
header("Location: dashboard.php");
exit;
// ---------------------------------------
?>
