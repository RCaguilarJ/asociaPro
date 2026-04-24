<?php
// setup.php
// Script para inicializar la base de datos, tablas y el primer usuario administrador.

try {
    // Conectamos a MySQL sin seleccionar base de datos primero
    $conn = new PDO("mysql:host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("set names utf8mb4");

    echo "<h2>Instalador de AsociaPro</h2>";

    // 1. Crear base de datos
    $conn->exec("CREATE DATABASE IF NOT EXISTS asociapro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de datos 'asociapro' creada (o ya existía).<br>";

    // Seleccionar la base de datos
    $conn->exec("USE asociapro");

    // 2. Crear tabla roles
    $sqlRoles = "CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(50) NOT NULL UNIQUE,
        descripcion VARCHAR(255)
    )";
    $conn->exec($sqlRoles);
    echo "✅ Tabla 'roles' creada.<br>";

    // Insertar roles (ignorando si ya existen)
    $conn->exec("INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES (1, 'Admin', 'Administrador principal del sistema')");
    $conn->exec("INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES (2, 'Usuario', 'Usuario regular / Miembro')");

    // 3. Crear tabla usuarios
    $sqlUsuarios = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        rol_id INT NOT NULL,
        activo BOOLEAN DEFAULT TRUE,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT
    )";
    $conn->exec($sqlUsuarios);
    echo "✅ Tabla 'usuarios' creada.<br>";

    // 4. Insertar usuario administrador por defecto
    $emailAdmin = 'admin@asociapro.com';
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$emailAdmin]);
    
    if ($stmt->rowCount() == 0) {
        $password = 'admin123';
        // Hashear la contraseña usando bcrypt
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $insertAdmin = $conn->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol_id) VALUES (?, ?, ?, ?)");
        $insertAdmin->execute(['Administrador', $emailAdmin, $hash, 1]);
        
        echo "<div style='background-color: #d1fae5; padding: 15px; border-radius: 8px; margin-top: 20px;'>";
        echo "✅ Usuario administrador creado exitosamente:<br><br>";
        echo "<strong>Email:</strong> $emailAdmin <br>";
        echo "<strong>Contraseña:</strong> $password <br>";
        echo "</div>";
    } else {
        echo "ℹ️ El usuario administrador ya existía en la base de datos.<br>";
    }

    echo "<h3 style='color: #2563eb;'>¡Instalación completada!</h3>";
    echo "<a href='index.php' style='display: inline-block; background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 10px;'>Ir a Iniciar Sesión</a>";
    
} catch(PDOException $e) {
    echo "<div style='background-color: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-top: 20px;'>";
    echo "<strong>Error durante la instalación:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
