<?php

require_once __DIR__ . '/database.php';

date_default_timezone_set('America/Mexico_City');

function roles_catalog(): array
{
    return [
        'Superadmin' => [
            'description' => 'Acceso total al sistema y todas las asociaciones',
            'permissions' => 'Todos los modulos',
            'icon' => 'ph-shield-checkered',
            'tone' => 'purple',
        ],
        'Administrador' => [
            'description' => 'Gestion completa de la asociacion',
            'permissions' => 'Todos excepto configuracion global',
            'icon' => 'ph-shield-check',
            'tone' => 'blue',
        ],
        'Mesa Directiva' => [
            'description' => 'Acceso a reportes y decisiones importantes',
            'permissions' => 'Reportes, finanzas, votaciones',
            'icon' => 'ph-shield',
            'tone' => 'green',
        ],
        'Capturista' => [
            'description' => 'Captura de informacion y gestion operativa',
            'permissions' => 'Socios, pagos, eventos',
            'icon' => 'ph-user',
            'tone' => 'yellow',
        ],
        'Socio' => [
            'description' => 'Acceso limitado al portal del socio',
            'permissions' => 'Solo portal personal',
            'icon' => 'ph-user-circle',
            'tone' => 'gray',
        ],
    ];
}

function app_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $database = new Database();
    $pdo = $database->getConnection();

    if (!$pdo instanceof PDO) {
        throw new RuntimeException('No fue posible establecer la conexion con la base de datos.');
    }

    ensure_app_schema($pdo);
    seed_demo_data($pdo);

    return $pdo;
}

function ensure_app_schema(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS socios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre_completo VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            telefono VARCHAR(30) NOT NULL,
            empresa VARCHAR(150) NOT NULL,
            puesto VARCHAR(150) DEFAULT '',
            industria VARCHAR(100) DEFAULT '',
            ciudad VARCHAR(100) NOT NULL,
            estado VARCHAR(100) NOT NULL,
            tipo_membresia ENUM('Basica', 'Premium', 'Corporativa') NOT NULL,
            cuota_anual DECIMAL(10,2) NOT NULL DEFAULT 0,
            fecha_vencimiento DATE NOT NULL,
            avatar_url VARCHAR(255) DEFAULT NULL,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_socios_nombre (nombre_completo),
            INDEX idx_socios_email (email),
            INDEX idx_socios_empresa (empresa),
            INDEX idx_socios_vencimiento (fecha_vencimiento)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS dashboard_actividades (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo VARCHAR(50) NOT NULL,
            descripcion VARCHAR(255) NOT NULL,
            referencia_tipo VARCHAR(50) DEFAULT NULL,
            referencia_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_dashboard_actividades_fecha (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS dashboard_eventos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(180) NOT NULL,
            descripcion TEXT DEFAULT NULL,
            ubicacion VARCHAR(180) NOT NULL,
            tipo VARCHAR(80) DEFAULT 'Evento',
            modalidad VARCHAR(60) NOT NULL DEFAULT 'Presencial',
            fecha_inicio DATE NOT NULL,
            hora_inicio TIME DEFAULT '09:00:00',
            capacidad INT NOT NULL DEFAULT 0,
            registrados INT NOT NULL DEFAULT 0,
            precio DECIMAL(10,2) NOT NULL DEFAULT 0,
            imagen_url VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_dashboard_eventos_fecha (fecha_inicio)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS solicitudes_membresia (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre_completo VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL,
            telefono VARCHAR(30) NOT NULL,
            empresa VARCHAR(150) NOT NULL,
            puesto VARCHAR(150) DEFAULT '',
            industria VARCHAR(100) DEFAULT '',
            ciudad VARCHAR(100) NOT NULL,
            estado VARCHAR(100) NOT NULL,
            tipo_membresia ENUM('Basica', 'Premium', 'Corporativa') NOT NULL,
            documentos_json LONGTEXT NOT NULL,
            avatar_url VARCHAR(255) DEFAULT NULL,
            notas TEXT DEFAULT NULL,
            status ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL DEFAULT 'Pendiente',
            submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            reviewed_at DATETIME DEFAULT NULL,
            INDEX idx_solicitudes_status (status),
            INDEX idx_solicitudes_fecha (submitted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(80) NOT NULL UNIQUE,
            descripcion VARCHAR(255) DEFAULT '',
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(120) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            rol_id INT NOT NULL,
            avatar_url VARCHAR(255) DEFAULT NULL,
            activo TINYINT(1) NOT NULL DEFAULT 1,
            ultimo_acceso DATETIME DEFAULT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS pagos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            socio_id INT NOT NULL,
            concepto VARCHAR(180) NOT NULL,
            metodo_pago ENUM('Transferencia', 'Tarjeta', 'Efectivo', 'Cheque') NOT NULL,
            monto DECIMAL(10,2) NOT NULL DEFAULT 0,
            status ENUM('Pagado', 'Pendiente', 'Vencido') NOT NULL DEFAULT 'Pagado',
            numero_factura VARCHAR(60) DEFAULT NULL,
            fecha_pago DATE NOT NULL,
            notas TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_pagos_fecha (fecha_pago),
            INDEX idx_pagos_status (status),
            FOREIGN KEY (socio_id) REFERENCES socios(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS comunicados (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(180) NOT NULL,
            asunto VARCHAR(180) NOT NULL,
            canal ENUM('Email', 'WhatsApp', 'Mixto') NOT NULL DEFAULT 'Email',
            audiencia VARCHAR(60) NOT NULL,
            cuerpo LONGTEXT NOT NULL,
            status ENUM('Enviado', 'Borrador') NOT NULL DEFAULT 'Enviado',
            destinatarios_count INT NOT NULL DEFAULT 0,
            abiertos_count INT NOT NULL DEFAULT 0,
            clicks_count INT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            sent_at DATETIME DEFAULT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_comunicados_fecha (created_at),
            INDEX idx_comunicados_status (status),
            INDEX idx_comunicados_canal (canal)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS documentos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(180) NOT NULL,
            categoria VARCHAR(80) NOT NULL,
            nivel_acceso VARCHAR(80) NOT NULL,
            original_filename VARCHAR(255) NOT NULL,
            file_ext VARCHAR(10) NOT NULL,
            mime_type VARCHAR(120) NOT NULL,
            file_size_bytes INT NOT NULL DEFAULT 0,
            storage_mode ENUM('generated', 'uploaded') NOT NULL DEFAULT 'generated',
            file_path VARCHAR(255) DEFAULT NULL,
            generated_content LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_documentos_categoria (categoria),
            INDEX idx_documentos_fecha (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS noticias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(200) NOT NULL,
            categoria VARCHAR(80) NOT NULL,
            resumen TEXT NOT NULL,
            contenido LONGTEXT NOT NULL,
            autor VARCHAR(120) NOT NULL,
            status ENUM('Publicado', 'Borrador') NOT NULL DEFAULT 'Publicado',
            imagen_url VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_noticias_categoria (categoria),
            INDEX idx_noticias_status (status),
            INDEX idx_noticias_fecha (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS votaciones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titulo VARCHAR(200) NOT NULL,
            descripcion TEXT NOT NULL,
            fecha_inicio DATE NOT NULL,
            fecha_cierre DATE NOT NULL,
            total_elegibles INT NOT NULL DEFAULT 0,
            votos_favor INT NOT NULL DEFAULT 0,
            votos_contra INT NOT NULL DEFAULT 0,
            abstenciones INT NOT NULL DEFAULT 0,
            acta_contenido LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_votaciones_fecha_inicio (fecha_inicio),
            INDEX idx_votaciones_fecha_cierre (fecha_cierre)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    ensure_table_column($pdo, 'usuarios', 'avatar_url', "ALTER TABLE usuarios ADD COLUMN avatar_url VARCHAR(255) DEFAULT NULL");
    ensure_table_column($pdo, 'usuarios', 'ultimo_acceso', "ALTER TABLE usuarios ADD COLUMN ultimo_acceso DATETIME DEFAULT NULL");
    ensure_table_column($pdo, 'usuarios', 'activo', "ALTER TABLE usuarios ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1");
    ensure_table_column($pdo, 'dashboard_eventos', 'descripcion', "ALTER TABLE dashboard_eventos ADD COLUMN descripcion TEXT DEFAULT NULL");
    ensure_table_column($pdo, 'dashboard_eventos', 'tipo', "ALTER TABLE dashboard_eventos ADD COLUMN tipo VARCHAR(80) DEFAULT 'Evento'");
    ensure_table_column($pdo, 'dashboard_eventos', 'hora_inicio', "ALTER TABLE dashboard_eventos ADD COLUMN hora_inicio TIME DEFAULT '09:00:00'");
    ensure_table_column($pdo, 'dashboard_eventos', 'precio', "ALTER TABLE dashboard_eventos ADD COLUMN precio DECIMAL(10,2) NOT NULL DEFAULT 0");
    ensure_table_column($pdo, 'dashboard_eventos', 'imagen_url', "ALTER TABLE dashboard_eventos ADD COLUMN imagen_url VARCHAR(255) DEFAULT NULL");
}

function seed_demo_data(PDO $pdo): void
{
    $totalSocios = (int) $pdo->query("SELECT COUNT(*) FROM socios")->fetchColumn();
    if ($totalSocios === 0) {
        $seedSocios = [
            [
                'nombre' => 'Maria Gonzalez Lopez',
                'email' => 'maria.gonzalez@empresa.com',
                'telefono' => '(55) 1234-5678',
                'empresa' => 'Universidad Virtual del Norte',
                'puesto' => 'Directora General',
                'industria' => 'Educacion',
                'ciudad' => 'Monterrey',
                'estado' => 'Nuevo Leon',
                'tipo' => 'Premium',
                'vencimiento' => date('Y-m-d', strtotime('+8 months')),
                'avatar' => 'https://i.pravatar.cc/150?img=32',
                'created_at' => date('Y-m-d 10:30:00', strtotime('first day of -5 months')),
            ],
            [
                'nombre' => 'Roberto Hernandez Cruz',
                'email' => 'roberto.hernandez@tech.com',
                'telefono' => '(33) 9876-5432',
                'empresa' => 'Tech Solutions SA',
                'puesto' => 'Director Comercial',
                'industria' => 'Tecnologia',
                'ciudad' => 'Guadalajara',
                'estado' => 'Jalisco',
                'tipo' => 'Basica',
                'vencimiento' => date('Y-m-d', strtotime('+6 months')),
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'created_at' => date('Y-m-d 09:15:00', strtotime('first day of -4 months')),
            ],
            [
                'nombre' => 'Ana Patricia Martinez',
                'email' => 'ana.martinez@consulting.mx',
                'telefono' => '(81) 5555-1234',
                'empresa' => 'Consultoria Estrategica',
                'puesto' => 'Consultora Senior',
                'industria' => 'Consultoria',
                'ciudad' => 'San Pedro',
                'estado' => 'Nuevo Leon',
                'tipo' => 'Premium',
                'vencimiento' => date('Y-m-d', strtotime('-90 days')),
                'avatar' => 'https://i.pravatar.cc/150?img=5',
                'created_at' => date('Y-m-d 16:45:00', strtotime('first day of -3 months')),
            ],
            [
                'nombre' => 'Luis Fernando Rojas',
                'email' => 'luis.rojas@innovacion.com',
                'telefono' => '(55) 8888-9999',
                'empresa' => 'Innovacion Digital MX',
                'puesto' => 'Director de Innovacion',
                'industria' => 'Tecnologia',
                'ciudad' => 'Ciudad de Mexico',
                'estado' => 'CDMX',
                'tipo' => 'Corporativa',
                'vencimiento' => date('Y-m-d', strtotime('+10 months')),
                'avatar' => 'https://i.pravatar.cc/150?img=8',
                'created_at' => date('Y-m-d 14:20:00', strtotime('first day of -2 months')),
            ],
            [
                'nombre' => 'Carmen Sanchez Ruiz',
                'email' => 'carmen.sanchez@financiera.mx',
                'telefono' => '(81) 2222-3333',
                'empresa' => 'Grupo Financiero del Norte',
                'puesto' => 'Gerente de Alianzas',
                'industria' => 'Finanzas',
                'ciudad' => 'Monterrey',
                'estado' => 'Nuevo Leon',
                'tipo' => 'Premium',
                'vencimiento' => date('Y-m-d', strtotime('+11 months')),
                'avatar' => 'https://i.pravatar.cc/150?img=41',
                'created_at' => date('Y-m-d 11:00:00', strtotime('first day of this month')),
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO socios (
                nombre_completo, email, telefono, empresa, puesto, industria, ciudad, estado,
                tipo_membresia, cuota_anual, fecha_vencimiento, avatar_url, activo, created_at, updated_at
            ) VALUES (
                :nombre, :email, :telefono, :empresa, :puesto, :industria, :ciudad, :estado,
                :tipo, :cuota, :vencimiento, :avatar, 1, :created_at, :created_at
            )"
        );

        foreach ($seedSocios as $socio) {
            $stmt->execute([
                ':nombre' => $socio['nombre'],
                ':email' => $socio['email'],
                ':telefono' => $socio['telefono'],
                ':empresa' => $socio['empresa'],
                ':puesto' => $socio['puesto'],
                ':industria' => $socio['industria'],
                ':ciudad' => $socio['ciudad'],
                ':estado' => $socio['estado'],
                ':tipo' => $socio['tipo'],
                ':cuota' => membership_fee($socio['tipo']),
                ':vencimiento' => $socio['vencimiento'],
                ':avatar' => $socio['avatar'],
                ':created_at' => $socio['created_at'],
            ]);
        }
    }

    $totalActividades = (int) $pdo->query("SELECT COUNT(*) FROM dashboard_actividades")->fetchColumn();
    if ($totalActividades === 0) {
        $seedActividades = [
            [
                'tipo' => 'alta_socio',
                'descripcion' => 'Luis Fernando Rojas se unio como miembro Corporativa',
                'created_at' => date('Y-m-d 10:30:00', strtotime('-2 days')),
            ],
            [
                'tipo' => 'pago',
                'descripcion' => 'Pago recibido de Maria Gonzalez Lopez - $5,000',
                'created_at' => date('Y-m-d 09:15:00', strtotime('-2 days')),
            ],
            [
                'tipo' => 'evento',
                'descripcion' => '15 nuevos registros para el Congreso Anual 2026',
                'created_at' => date('Y-m-d 16:45:00', strtotime('-3 days')),
            ],
            [
                'tipo' => 'solicitud',
                'descripcion' => 'Nueva solicitud de membresia de Sofia Mendoza',
                'created_at' => date('Y-m-d 14:20:00', strtotime('-4 days')),
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO dashboard_actividades (tipo, descripcion, created_at)
             VALUES (:tipo, :descripcion, :created_at)"
        );

        foreach ($seedActividades as $actividad) {
            $stmt->execute($actividad);
        }
    }

    $totalEventos = (int) $pdo->query("SELECT COUNT(*) FROM dashboard_eventos")->fetchColumn();
    if ($totalEventos === 0) {
        $seedEventos = [
            [
                'titulo' => 'Congreso Anual de Educacion Virtual 2024',
                'descripcion' => 'El evento mas importante del ano para profesionales de la educacion virtual.',
                'ubicacion' => 'Centro de Convenciones Monterrey',
                'tipo' => 'Congreso',
                'modalidad' => 'Presencial',
                'fecha_inicio' => date('Y-m-d', strtotime('+10 days')),
                'hora_inicio' => '09:00:00',
                'capacidad' => 500,
                'registrados' => 342,
                'precio' => 2500,
                'imagen_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80',
            ],
            [
                'titulo' => 'Workshop: Innovacion en Metodologias Digitales',
                'descripcion' => 'Aprende las ultimas tendencias en metodologias de ensenanza digital.',
                'ubicacion' => 'Virtual - Zoom',
                'tipo' => 'Workshop',
                'modalidad' => 'Virtual',
                'fecha_inicio' => date('Y-m-d', strtotime('+18 days')),
                'hora_inicio' => '16:00:00',
                'capacidad' => 100,
                'registrados' => 87,
                'precio' => 500,
                'imagen_url' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
            ],
            [
                'titulo' => 'Networking: Encuentro de Directivos',
                'descripcion' => 'Exclusivo para miembros premium. Cena de networking con lideres del sector.',
                'ubicacion' => 'Hotel Quinta Real, Monterrey',
                'tipo' => 'Networking',
                'modalidad' => 'Presencial',
                'fecha_inicio' => date('Y-m-d', strtotime('+27 days')),
                'hora_inicio' => '19:00:00',
                'capacidad' => 50,
                'registrados' => 48,
                'precio' => 0,
                'imagen_url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200&q=80',
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO dashboard_eventos (
                titulo, descripcion, ubicacion, tipo, modalidad, fecha_inicio, hora_inicio, capacidad, registrados, precio, imagen_url
            ) VALUES (
                :titulo, :descripcion, :ubicacion, :tipo, :modalidad, :fecha_inicio, :hora_inicio, :capacidad, :registrados, :precio, :imagen_url
            )"
        );

        foreach ($seedEventos as $evento) {
            $stmt->execute($evento);
        }
    } else {
        $eventBackfills = [
            [
                'match' => 'Congreso Anual de Educacion Virtual%',
                'titulo' => 'Congreso Anual de Educacion Virtual 2024',
                'descripcion' => 'El evento mas importante del ano para profesionales de la educacion virtual.',
                'tipo' => 'Congreso',
                'hora_inicio' => '09:00:00',
                'precio' => 2500,
                'imagen_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80',
            ],
            [
                'match' => 'Workshop: Innovacion en Metodologias Digitales%',
                'titulo' => 'Workshop: Innovacion en Metodologias Digitales',
                'descripcion' => 'Aprende las ultimas tendencias en metodologias de ensenanza digital.',
                'tipo' => 'Workshop',
                'hora_inicio' => '16:00:00',
                'precio' => 500,
                'imagen_url' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
            ],
            [
                'match' => 'Networking: Encuentro de Directivos%',
                'titulo' => 'Networking: Encuentro de Directivos',
                'descripcion' => 'Exclusivo para miembros premium. Cena de networking con lideres del sector.',
                'tipo' => 'Networking',
                'hora_inicio' => '19:00:00',
                'precio' => 0,
                'imagen_url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200&q=80',
            ],
        ];

        $stmt = $pdo->prepare(
            "UPDATE dashboard_eventos
             SET titulo = :titulo,
                 descripcion = COALESCE(NULLIF(descripcion, ''), :descripcion),
                 tipo = COALESCE(NULLIF(tipo, ''), :tipo),
                 hora_inicio = COALESCE(hora_inicio, :hora_inicio),
                 precio = CASE WHEN precio = 0 THEN :precio ELSE precio END,
                 imagen_url = COALESCE(NULLIF(imagen_url, ''), :imagen_url)
             WHERE titulo LIKE :match"
        );

        foreach ($eventBackfills as $backfill) {
            $stmt->execute([
                ':titulo' => $backfill['titulo'],
                ':descripcion' => $backfill['descripcion'],
                ':tipo' => $backfill['tipo'],
                ':hora_inicio' => $backfill['hora_inicio'],
                ':precio' => $backfill['precio'],
                ':imagen_url' => $backfill['imagen_url'],
                ':match' => $backfill['match'],
            ]);
        }
    }

    $totalSolicitudes = (int) $pdo->query("SELECT COUNT(*) FROM solicitudes_membresia")->fetchColumn();
    if ($totalSolicitudes === 0) {
        $seedSolicitudes = [
            [
                'nombre' => 'Pedro Ramirez Torres',
                'email' => 'pedro.ramirez@startup.mx',
                'telefono' => '(33) 1111-2222',
                'empresa' => 'StartUp Innovadora',
                'puesto' => 'Fundador',
                'industria' => 'Tecnologia',
                'ciudad' => 'Guadalajara',
                'estado' => 'Jalisco',
                'tipo' => 'Basica',
                'avatar' => '',
                'notas' => 'Busca integrarse a la red de socios para acceder a eventos y alianzas estrategicas.',
                'documentos' => [
                    ['nombre' => 'INE', 'archivo' => 'ine-pedro-ramirez.pdf'],
                    ['nombre' => 'Comprobante de domicilio', 'archivo' => 'comprobante-domicilio-pedro.pdf'],
                    ['nombre' => 'RFC', 'archivo' => 'rfc-pedro-ramirez.pdf'],
                ],
                'submitted_at' => '2024-04-19 10:30:00',
            ],
            [
                'nombre' => 'Sofia Mendoza Alvarez',
                'email' => 'sofia.mendoza@corporativo.com',
                'telefono' => '(55) 4444-5555',
                'empresa' => 'Corporativo Internacional',
                'puesto' => 'Directora de Operaciones',
                'industria' => 'Servicios',
                'ciudad' => 'Ciudad de Mexico',
                'estado' => 'CDMX',
                'tipo' => 'Corporativa',
                'avatar' => '',
                'notas' => 'Solicita membresia corporativa para incluir a su equipo de direccion en la asociacion.',
                'documentos' => [
                    ['nombre' => 'INE', 'archivo' => 'ine-sofia-mendoza.pdf'],
                    ['nombre' => 'Acta constitutiva', 'archivo' => 'acta-constitutiva-corporativo-internacional.pdf'],
                ],
                'submitted_at' => '2024-04-20 11:20:00',
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO solicitudes_membresia (
                nombre_completo, email, telefono, empresa, puesto, industria, ciudad, estado,
                tipo_membresia, documentos_json, avatar_url, notas, status, submitted_at
            ) VALUES (
                :nombre, :email, :telefono, :empresa, :puesto, :industria, :ciudad, :estado,
                :tipo, :documentos, :avatar, :notas, 'Pendiente', :submitted_at
            )"
        );

        foreach ($seedSolicitudes as $solicitud) {
            $stmt->execute([
                ':nombre' => $solicitud['nombre'],
                ':email' => $solicitud['email'],
                ':telefono' => $solicitud['telefono'],
                ':empresa' => $solicitud['empresa'],
                ':puesto' => $solicitud['puesto'],
                ':industria' => $solicitud['industria'],
                ':ciudad' => $solicitud['ciudad'],
                ':estado' => $solicitud['estado'],
                ':tipo' => $solicitud['tipo'],
                ':documentos' => json_encode($solicitud['documentos'], JSON_UNESCAPED_UNICODE),
                ':avatar' => $solicitud['avatar'],
                ':notas' => $solicitud['notas'],
                ':submitted_at' => $solicitud['submitted_at'],
            ]);
        }
    }

    $rolesCatalog = roles_catalog();
    $roleStmt = $pdo->prepare(
        "INSERT INTO roles (nombre, descripcion)
         VALUES (:nombre, :descripcion)
         ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion)"
    );
    foreach ($rolesCatalog as $roleName => $roleMeta) {
        $roleStmt->execute([
            ':nombre' => $roleName,
            ':descripcion' => $roleMeta['description'],
        ]);
    }

    merge_legacy_role($pdo, 'Admin', 'Administrador');
    merge_legacy_role($pdo, 'Usuario', 'Socio');

    $rolesByName = fetch_roles_indexed_by_name($pdo);
    $seedUsers = [
        [
            'nombre' => 'Carlos Mendoza',
            'email' => 'carlos@amuvie.org',
            'rol' => 'Administrador',
            'activo' => 1,
            'ultimo_acceso' => '2024-04-24 09:30:00',
            'avatar_url' => 'https://i.pravatar.cc/150?img=11',
        ],
        [
            'nombre' => 'Laura Martinez',
            'email' => 'laura@amuvie.org',
            'rol' => 'Mesa Directiva',
            'activo' => 1,
            'ultimo_acceso' => '2024-04-23 14:20:00',
            'avatar_url' => 'https://i.pravatar.cc/150?img=47',
        ],
        [
            'nombre' => 'Roberto Silva',
            'email' => 'roberto@amuvie.org',
            'rol' => 'Capturista',
            'activo' => 1,
            'ultimo_acceso' => '2024-04-24 08:15:00',
            'avatar_url' => 'https://i.pravatar.cc/150?img=12',
        ],
    ];

    $userExistsStmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $userStmt = $pdo->prepare(
        "INSERT INTO usuarios (nombre, email, password_hash, rol_id, avatar_url, activo, ultimo_acceso)
         VALUES (:nombre, :email, :password_hash, :rol_id, :avatar_url, :activo, :ultimo_acceso)"
    );

    foreach ($seedUsers as $user) {
        if (!isset($rolesByName[$user['rol']])) {
            continue;
        }

        $userExistsStmt->execute([':email' => $user['email']]);
        if ($userExistsStmt->fetch()) {
            continue;
        }

        $userStmt->execute([
            ':nombre' => $user['nombre'],
            ':email' => $user['email'],
            ':password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            ':rol_id' => $rolesByName[$user['rol']]['id'],
            ':avatar_url' => $user['avatar_url'],
            ':activo' => $user['activo'],
            ':ultimo_acceso' => $user['ultimo_acceso'],
        ]);
    }

    $totalPayments = (int) $pdo->query("SELECT COUNT(*) FROM pagos")->fetchColumn();
    if ($totalPayments === 0) {
        $memberIndex = fetch_members_indexed_by_email($pdo);
        $seedPayments = [
            [
                'email' => 'maria.gonzalez@empresa.com',
                'concepto' => 'Cuota anual 2024',
                'metodo' => 'Transferencia',
                'monto' => 5000,
                'status' => 'Pagado',
                'factura' => 'FAC-2024-001',
                'fecha_pago' => '2024-01-14',
                'notas' => 'Pago confirmado por transferencia bancaria.',
            ],
            [
                'email' => 'roberto.hernandez@tech.com',
                'concepto' => 'Cuota anual 2024',
                'metodo' => 'Tarjeta',
                'monto' => 3000,
                'status' => 'Pagado',
                'factura' => 'FAC-2024-045',
                'fecha_pago' => '2024-03-19',
                'notas' => 'Pago realizado con tarjeta corporativa.',
            ],
            [
                'email' => 'ana.martinez@consulting.mx',
                'concepto' => 'Cuota anual 2024',
                'metodo' => 'Transferencia',
                'monto' => 5000,
                'status' => 'Vencido',
                'factura' => null,
                'fecha_pago' => '2023-12-09',
                'notas' => 'Cuota vencida pendiente de regularizacion.',
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO pagos (
                socio_id, concepto, metodo_pago, monto, status, numero_factura, fecha_pago, notas
            ) VALUES (
                :socio_id, :concepto, :metodo_pago, :monto, :status, :numero_factura, :fecha_pago, :notas
            )"
        );

        foreach ($seedPayments as $payment) {
            if (!isset($memberIndex[$payment['email']])) {
                continue;
            }

            $stmt->execute([
                ':socio_id' => $memberIndex[$payment['email']]['id'],
                ':concepto' => $payment['concepto'],
                ':metodo_pago' => $payment['metodo'],
                ':monto' => $payment['monto'],
                ':status' => $payment['status'],
                ':numero_factura' => $payment['factura'],
                ':fecha_pago' => $payment['fecha_pago'],
                ':notas' => $payment['notas'],
            ]);
        }
    }

    $totalCommunications = (int) $pdo->query("SELECT COUNT(*) FROM comunicados")->fetchColumn();
    if ($totalCommunications === 0) {
        $seedCommunications = [
            [
                'titulo' => 'Invitacion Congreso Anual 2024',
                'asunto' => 'Acompananos en el Congreso Anual 2024',
                'canal' => 'Email',
                'audiencia' => 'Miembros Activos',
                'cuerpo' => "Te invitamos a participar en nuestro Congreso Anual 2024.\n\nConsulta el programa, confirma tu asistencia y comparte esta invitacion con tu equipo.",
                'status' => 'Enviado',
                'created_at' => '2024-04-14 09:15:00',
                'sent_at' => '2024-04-14 09:15:00',
            ],
            [
                'titulo' => 'Recordatorio de pago - Cuotas vencidas',
                'asunto' => 'Regulariza tu cuota anual',
                'canal' => 'Email',
                'audiencia' => 'Pagos Vencidos',
                'cuerpo' => "Detectamos una cuota anual vencida en tu membresia.\n\nTe pedimos revisar tu estado de cuenta y ponerte en contacto con el equipo de finanzas para regularizarla.",
                'status' => 'Enviado',
                'created_at' => '2024-04-19 11:40:00',
                'sent_at' => '2024-04-19 11:40:00',
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO comunicados (
                titulo, asunto, canal, audiencia, cuerpo, status,
                destinatarios_count, abiertos_count, clicks_count, created_at, sent_at
            ) VALUES (
                :titulo, :asunto, :canal, :audiencia, :cuerpo, :status,
                :destinatarios_count, :abiertos_count, :clicks_count, :created_at, :sent_at
            )"
        );

        foreach ($seedCommunications as $communication) {
            $recipientCount = count(communication_recipient_members($pdo, $communication['audiencia']));
            $metrics = communication_estimated_metrics($recipientCount, $communication['canal']);

            $stmt->execute([
                ':titulo' => $communication['titulo'],
                ':asunto' => $communication['asunto'],
                ':canal' => $communication['canal'],
                ':audiencia' => $communication['audiencia'],
                ':cuerpo' => $communication['cuerpo'],
                ':status' => $communication['status'],
                ':destinatarios_count' => $recipientCount,
                ':abiertos_count' => $metrics['abiertos_count'],
                ':clicks_count' => $metrics['clicks_count'],
                ':created_at' => $communication['created_at'],
                ':sent_at' => $communication['sent_at'],
            ]);
        }
    }

    $totalDocuments = (int) $pdo->query("SELECT COUNT(*) FROM documentos")->fetchColumn();
    if ($totalDocuments === 0) {
        $seedDocuments = [
            [
                'titulo' => 'Estatutos AMUVIE 2024',
                'categoria' => 'Estatutos',
                'nivel_acceso' => 'Todos los socios',
                'original_filename' => 'estatutos-amuvie-2024.pdf',
                'generated_content' => "Estatutos AMUVIE 2024\n\nDocumento institucional con lineamientos generales, objetivos y estructura de la asociacion.",
                'created_at' => date('Y-01-09 10:00:00'),
            ],
            [
                'titulo' => 'Acta Asamblea General Marzo 2024',
                'categoria' => 'Actas',
                'nivel_acceso' => 'Mesa directiva',
                'original_filename' => 'acta-asamblea-general-marzo-2024.pdf',
                'generated_content' => "Acta Asamblea General Marzo 2024\n\nResumen de acuerdos, votaciones y compromisos derivados de la asamblea general.",
                'created_at' => date('Y-03-24 12:00:00'),
            ],
            [
                'titulo' => 'Reglamento Interno',
                'categoria' => 'Reglamentos',
                'nivel_acceso' => 'Todos los socios',
                'original_filename' => 'reglamento-interno.pdf',
                'generated_content' => "Reglamento Interno\n\nReglas de operacion, conducta y convivencia para los miembros de AMUVIE.",
                'created_at' => date('Y-01-31 16:30:00'),
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO documentos (
                titulo, categoria, nivel_acceso, original_filename, file_ext, mime_type,
                file_size_bytes, storage_mode, file_path, generated_content, created_at
            ) VALUES (
                :titulo, :categoria, :nivel_acceso, :original_filename, 'pdf', 'application/pdf',
                :file_size_bytes, 'generated', NULL, :generated_content, :created_at
            )"
        );

        foreach ($seedDocuments as $document) {
            $pdfContent = generated_document_pdf_bytes($document['titulo'], $document['generated_content']);
            $stmt->execute([
                ':titulo' => $document['titulo'],
                ':categoria' => $document['categoria'],
                ':nivel_acceso' => $document['nivel_acceso'],
                ':original_filename' => $document['original_filename'],
                ':file_size_bytes' => strlen($pdfContent),
                ':generated_content' => $document['generated_content'],
                ':created_at' => $document['created_at'],
            ]);
        }
    }

    $totalNews = (int) $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
    if ($totalNews === 0) {
        $seedNews = [
            [
                'titulo' => 'AMUVIE firma convenio con Universidad Internacional',
                'categoria' => 'Convenios',
                'resumen' => 'Se amplian los beneficios para miembros con acceso a programas de educacion continua.',
                'contenido' => "AMUVIE ha firmado un nuevo convenio con Universidad Internacional para ampliar la oferta academica disponible para sus miembros.\n\nEl acuerdo incluye descuentos preferenciales, acceso a diplomados y colaboraciones para eventos de formacion ejecutiva.",
                'autor' => 'Redaccion AMUVIE',
                'status' => 'Publicado',
                'imagen_url' => news_image_for_category('Convenios'),
                'created_at' => '2024-04-19 09:00:00',
            ],
            [
                'titulo' => 'Exitoso Workshop de Innovacion Digital',
                'categoria' => 'Eventos',
                'resumen' => 'Mas de 80 participantes en el taller sobre metodologias agiles y transformacion digital.',
                'contenido' => "El workshop de Innovacion Digital reunio a profesionales del sector para revisar tendencias, herramientas y casos de uso aplicables a proyectos educativos y empresariales.\n\nLa sesion incluyo networking, preguntas abiertas y una guia practica de implementacion.",
                'autor' => 'Carlos Mendoza',
                'status' => 'Publicado',
                'imagen_url' => news_image_for_category('Eventos'),
                'created_at' => '2024-04-17 14:00:00',
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO noticias (
                titulo, categoria, resumen, contenido, autor, status, imagen_url, created_at
            ) VALUES (
                :titulo, :categoria, :resumen, :contenido, :autor, :status, :imagen_url, :created_at
            )"
        );

        foreach ($seedNews as $newsItem) {
            $stmt->execute($newsItem);
        }
    }

    $totalVotes = (int) $pdo->query("SELECT COUNT(*) FROM votaciones")->fetchColumn();
    if ($totalVotes === 0) {
        $eligibleMembers = (int) $pdo->query("SELECT COUNT(*) FROM socios WHERE activo = 1")->fetchColumn();
        $seedVotes = [
            [
                'titulo' => 'Aprobacion de nuevos estatutos 2024',
                'descripcion' => 'Votacion para aprobar las modificaciones propuestas a los estatutos',
                'fecha_inicio' => '2026-04-24',
                'fecha_cierre' => '2026-05-04',
                'total_elegibles' => max($eligibleMembers, 221),
                'votos_favor' => 128,
                'votos_contra' => 12,
                'abstenciones' => 2,
            ],
            [
                'titulo' => 'Eleccion de Mesa Directiva 2024-2026',
                'descripcion' => 'Eleccion de presidente, secretario y tesorero',
                'fecha_inicio' => '2026-05-09',
                'fecha_cierre' => '2026-05-19',
                'total_elegibles' => max($eligibleMembers, 221),
                'votos_favor' => 0,
                'votos_contra' => 0,
                'abstenciones' => 0,
            ],
        ];

        $stmt = $pdo->prepare(
            "INSERT INTO votaciones (
                titulo, descripcion, fecha_inicio, fecha_cierre, total_elegibles,
                votos_favor, votos_contra, abstenciones, acta_contenido
            ) VALUES (
                :titulo, :descripcion, :fecha_inicio, :fecha_cierre, :total_elegibles,
                :votos_favor, :votos_contra, :abstenciones, :acta_contenido
            )"
        );

        foreach ($seedVotes as $vote) {
            $stmt->execute([
                ':titulo' => $vote['titulo'],
                ':descripcion' => $vote['descripcion'],
                ':fecha_inicio' => $vote['fecha_inicio'],
                ':fecha_cierre' => $vote['fecha_cierre'],
                ':total_elegibles' => $vote['total_elegibles'],
                ':votos_favor' => $vote['votos_favor'],
                ':votos_contra' => $vote['votos_contra'],
                ':abstenciones' => $vote['abstenciones'],
                ':acta_contenido' => generate_voting_minutes_body($vote),
            ]);
        }
    }
}

function membership_options(): array
{
    return [
        'Basica' => 'Basica ($3,000/anio)',
        'Premium' => 'Premium ($5,000/anio)',
        'Corporativa' => 'Corporativa ($12,000/anio)',
    ];
}

function industry_options(): array
{
    return [
        'Educacion',
        'Tecnologia',
        'Consultoria',
        'Finanzas',
        'Salud',
        'Manufactura',
        'Servicios',
        'Gobierno',
    ];
}

function membership_fee(string $membershipType): float
{
    $fees = [
        'Basica' => 3000,
        'Premium' => 5000,
        'Corporativa' => 12000,
    ];

    return (float) ($fees[$membershipType] ?? 3000);
}

function format_currency(float $amount): string
{
    return '$' . number_format($amount, 0, '.', ',');
}

function format_compact_currency(float $amount): string
{
    if ($amount >= 1000) {
        return '$' . number_format($amount / 1000, 1) . 'K';
    }

    return format_currency($amount);
}

function month_name_short(int $monthNumber): string
{
    $months = [
        1 => 'Ene',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dic',
    ];

    return $months[$monthNumber] ?? '';
}

function month_name_lower(int $monthNumber): string
{
    return strtolower(month_name_short($monthNumber));
}

function format_short_date(string $date): string
{
    $dateObject = new DateTimeImmutable($date);
    $day = $dateObject->format('j');
    $month = month_name_lower((int) $dateObject->format('n'));
    $year = $dateObject->format('Y');

    return $day . ' ' . $month . ' ' . $year;
}

function format_datetime_display(string $dateTime): string
{
    $dateObject = new DateTimeImmutable($dateTime);

    return $dateObject->format('Y-m-d H:i');
}

function format_datetime_label(string $dateTime): string
{
    $dateObject = new DateTimeImmutable($dateTime);

    return format_short_date($dateObject->format('Y-m-d'));
}

function current_user_name(): string
{
    return $_SESSION['user_name'] ?? 'Carlos Mendoza';
}

function current_user_email(): string
{
    return $_SESSION['user_email'] ?? 'carlos@amuvie.org';
}

function current_user_role_label(): string
{
    $role = $_SESSION['user_role'] ?? 1;

    return $role === 1 ? 'admin' : 'usuario';
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sanitize_phone_number(string $phone): string
{
    return preg_replace('/\D+/', '', $phone) ?? '';
}

function whatsapp_phone_number(string $phone): string
{
    $digits = sanitize_phone_number($phone);

    if (strlen($digits) === 10) {
        return '52' . $digits;
    }

    return $digits;
}

function member_whatsapp_message(array $member): string
{
    $recipient = trim((string) ($member['nombre_completo'] ?? ''));
    $sender = current_user_name();

    return 'Hola ' . $recipient . ', te contacto desde el directorio de socios de AMUVIE. Soy ' . $sender . '.';
}

function member_whatsapp_url(array $member): string
{
    $phone = whatsapp_phone_number((string) ($member['telefono'] ?? ''));
    $message = rawurlencode(member_whatsapp_message($member));

    return 'https://wa.me/' . $phone . '?text=' . $message;
}

function ensure_table_column(PDO $pdo, string $tableName, string $columnName, string $alterSql): void
{
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND COLUMN_NAME = :column_name"
    );
    $stmt->execute([
        ':table_name' => $tableName,
        ':column_name' => $columnName,
    ]);

    if ((int) $stmt->fetchColumn() === 0) {
        $pdo->exec($alterSql);
    }
}

function flash_message(string $type, string $message): void
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function pull_flash_message(): ?array
{
    if (!isset($_SESSION['flash_message'])) {
        return null;
    }

    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

    return $message;
}

function store_form_state(array $data, array $errors = []): void
{
    $_SESSION['form_state'] = [
        'data' => $data,
        'errors' => $errors,
    ];
}

function pull_form_state(): array
{
    $formState = $_SESSION['form_state'] ?? [
        'data' => [],
        'errors' => [],
    ];

    unset($_SESSION['form_state']);

    return $formState;
}

function member_form_defaults(): array
{
    return [
        'id' => '',
        'nombre_completo' => '',
        'email' => '',
        'telefono' => '',
        'empresa' => '',
        'puesto' => '',
        'industria' => '',
        'ciudad' => '',
        'estado' => '',
        'tipo_membresia' => 'Basica',
        'avatar_url' => '',
    ];
}

function normalize_member_form(array $input): array
{
    $data = member_form_defaults();

    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function validate_member_form(array $data, PDO $pdo, ?array $avatarUpload = null): array
{
    $errors = [];
    $requiredFields = [
        'nombre_completo' => 'El nombre completo es obligatorio.',
        'email' => 'El correo electronico es obligatorio.',
        'telefono' => 'El telefono es obligatorio.',
        'empresa' => 'La empresa es obligatoria.',
        'ciudad' => 'La ciudad es obligatoria.',
        'estado' => 'El estado es obligatorio.',
        'tipo_membresia' => 'Selecciona el tipo de membresia.',
    ];

    foreach ($requiredFields as $field => $message) {
        if ($data[$field] === '') {
            $errors[$field] = $message;
        }
    }

    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Ingresa un correo electronico valido.';
    }

    if ($data['industria'] === '') {
        $errors['industria'] = 'Selecciona una industria.';
    }

    if (!array_key_exists($data['tipo_membresia'], membership_options())) {
        $errors['tipo_membresia'] = 'Selecciona un tipo de membresia valido.';
    }

    $query = "SELECT id FROM socios WHERE email = :email";
    $params = [':email' => $data['email']];

    if ($data['id'] !== '') {
        $query .= " AND id <> :id";
        $params[':id'] = (int) $data['id'];
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    if ($stmt->fetch()) {
        $errors['email'] = 'Ya existe un socio registrado con ese correo.';
    }

    $avatarError = validate_avatar_upload($avatarUpload);
    if ($avatarError !== null) {
        $errors['avatar'] = $avatarError;
    }

    return $errors;
}

function member_avatar_for_email(string $email): string
{
    $imageNumber = (crc32(strtolower($email)) % 70) + 1;

    return 'https://i.pravatar.cc/150?img=' . $imageNumber;
}

function save_member(PDO $pdo, array $data, ?array $avatarUpload = null): int
{
    $fee = membership_fee($data['tipo_membresia']);

    if ($data['id'] !== '') {
        $existing = find_member_by_id($pdo, (int) $data['id']);
        if (!$existing) {
            throw new RuntimeException('El socio que intentas editar no existe.');
        }

        $avatarUrl = $existing['avatar_url'];
        if (has_uploaded_avatar($avatarUpload)) {
            $avatarUrl = store_avatar_upload($avatarUpload);
            delete_local_avatar_file($existing['avatar_url']);
        }

        $stmt = $pdo->prepare(
            "UPDATE socios
             SET nombre_completo = :nombre,
                 email = :email,
                 telefono = :telefono,
                 empresa = :empresa,
                 puesto = :puesto,
                 industria = :industria,
                 ciudad = :ciudad,
                 estado = :estado,
                 tipo_membresia = :tipo,
                 cuota_anual = :cuota,
                 avatar_url = :avatar
             WHERE id = :id"
        );

        $stmt->execute([
            ':nombre' => $data['nombre_completo'],
            ':email' => $data['email'],
            ':telefono' => $data['telefono'],
            ':empresa' => $data['empresa'],
            ':puesto' => $data['puesto'],
            ':industria' => $data['industria'],
            ':ciudad' => $data['ciudad'],
            ':estado' => $data['estado'],
            ':tipo' => $data['tipo_membresia'],
            ':cuota' => $fee,
            ':avatar' => $avatarUrl,
            ':id' => (int) $data['id'],
        ]);

        log_activity($pdo, 'actualizacion_socio', 'Se actualizo la ficha de ' . $data['nombre_completo'], 'socio', (int) $data['id']);

        return (int) $data['id'];
    }

    $createdAt = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d', strtotime('+1 year'));
    $avatarUrl = has_uploaded_avatar($avatarUpload)
        ? store_avatar_upload($avatarUpload)
        : (($data['avatar_url'] ?? '') !== '' ? $data['avatar_url'] : member_avatar_for_email($data['email']));

    $stmt = $pdo->prepare(
        "INSERT INTO socios (
            nombre_completo, email, telefono, empresa, puesto, industria, ciudad, estado,
            tipo_membresia, cuota_anual, fecha_vencimiento, avatar_url, activo, created_at, updated_at
        ) VALUES (
            :nombre, :email, :telefono, :empresa, :puesto, :industria, :ciudad, :estado,
            :tipo, :cuota, :vencimiento, :avatar, 1, :created_at, :created_at
        )"
    );

    $stmt->execute([
        ':nombre' => $data['nombre_completo'],
        ':email' => $data['email'],
        ':telefono' => $data['telefono'],
        ':empresa' => $data['empresa'],
        ':puesto' => $data['puesto'],
        ':industria' => $data['industria'],
        ':ciudad' => $data['ciudad'],
        ':estado' => $data['estado'],
        ':tipo' => $data['tipo_membresia'],
        ':cuota' => $fee,
        ':vencimiento' => $expiresAt,
        ':avatar' => $avatarUrl,
        ':created_at' => $createdAt,
    ]);

    $memberId = (int) $pdo->lastInsertId();
    log_activity($pdo, 'alta_socio', $data['nombre_completo'] . ' se unio como socio ' . $data['tipo_membresia'], 'socio', $memberId);

    return $memberId;
}

function delete_member(PDO $pdo, int $memberId): void
{
    $member = find_member_by_id($pdo, $memberId);
    if (!$member) {
        throw new RuntimeException('El socio que intentas eliminar no existe.');
    }

    $stmt = $pdo->prepare("DELETE FROM socios WHERE id = :id");
    $stmt->execute([':id' => $memberId]);
    delete_local_avatar_file($member['avatar_url']);

    log_activity($pdo, 'eliminacion_socio', 'Se elimino a ' . $member['nombre_completo'] . ' del padron de socios', 'socio', $memberId);
}

function log_activity(PDO $pdo, string $type, string $description, ?string $referenceType = null, ?int $referenceId = null): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO dashboard_actividades (tipo, descripcion, referencia_tipo, referencia_id)
         VALUES (:tipo, :descripcion, :referencia_tipo, :referencia_id)"
    );

    $stmt->execute([
        ':tipo' => $type,
        ':descripcion' => $description,
        ':referencia_tipo' => $referenceType,
        ':referencia_id' => $referenceId,
    ]);
}

function find_member_by_id(PDO $pdo, int $memberId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT *,
                CASE WHEN activo = 1 AND fecha_vencimiento >= CURDATE() THEN 'Activo' ELSE 'Vencido' END AS estatus
         FROM socios
         WHERE id = :id
         LIMIT 1"
    );
    $stmt->execute([':id' => $memberId]);

    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    return $member ?: null;
}

function member_filters_from_request(array $query): array
{
    return [
        'search' => trim((string) ($query['search'] ?? '')),
        'status' => trim((string) ($query['status'] ?? '')),
        'sort' => trim((string) ($query['sort'] ?? 'recent')),
        'tipo' => trim((string) ($query['tipo'] ?? '')),
        'estado' => trim((string) ($query['estado'] ?? '')),
        'industria' => trim((string) ($query['industria'] ?? '')),
    ];
}

function fetch_members(PDO $pdo, array $filters = []): array
{
    $sql = "SELECT *,
                   CASE WHEN activo = 1 AND fecha_vencimiento >= CURDATE() THEN 'Activo' ELSE 'Vencido' END AS estatus
            FROM socios
            WHERE 1 = 1";
    $params = [];

    if (($filters['search'] ?? '') !== '') {
        $sql .= " AND (nombre_completo LIKE :search OR email LIKE :search OR empresa LIKE :search OR ciudad LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (($filters['status'] ?? '') === 'Activo') {
        $sql .= " AND activo = 1 AND fecha_vencimiento >= CURDATE()";
    } elseif (($filters['status'] ?? '') === 'Vencido') {
        $sql .= " AND (activo = 0 OR fecha_vencimiento < CURDATE())";
    }

    if (($filters['tipo'] ?? '') !== '') {
        $sql .= " AND tipo_membresia = :tipo";
        $params[':tipo'] = $filters['tipo'];
    }

    if (($filters['estado'] ?? '') !== '') {
        $sql .= " AND estado = :estado";
        $params[':estado'] = $filters['estado'];
    }

    if (($filters['industria'] ?? '') !== '') {
        $sql .= " AND industria = :industria";
        $params[':industria'] = $filters['industria'];
    }

    $sort = $filters['sort'] ?? 'recent';
    if ($sort === 'alpha') {
        $sql .= " ORDER BY nombre_completo ASC, id DESC";
    } else {
        $sql .= " ORDER BY created_at DESC, id DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_members_indexed_by_email(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM socios");
    $members = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $member) {
        $members[$member['email']] = $member;
    }

    return $members;
}

function fetch_member_states(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT DISTINCT estado FROM socios WHERE estado <> '' ORDER BY estado ASC");

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function communication_channel_options(): array
{
    return ['Email', 'WhatsApp', 'Mixto'];
}

function communication_audience_options(): array
{
    return [
        'Miembros Activos',
        'Pagos Vencidos',
        'Membresia Premium',
        'Membresia Corporativa',
        'Todos los Socios',
    ];
}

function communication_audience_label(string $audience): string
{
    return match ($audience) {
        'Miembros Activos' => 'Todos los miembros activos',
        'Pagos Vencidos' => 'Miembros con pagos vencidos',
        'Membresia Premium' => 'Socios con membresia premium',
        'Membresia Corporativa' => 'Socios con membresia corporativa',
        'Todos los Socios' => 'Todos los socios registrados',
        default => $audience,
    };
}

function communication_form_defaults(): array
{
    return [
        'id' => '',
        'titulo' => '',
        'asunto' => '',
        'canal' => 'Email',
        'audiencia' => 'Miembros Activos',
        'status' => 'Enviado',
        'cuerpo' => '',
    ];
}

function normalize_communication_form(array $input): array
{
    $data = communication_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function communication_form_data_from_record(array $communication): array
{
    return [
        'id' => (string) ($communication['id'] ?? ''),
        'titulo' => (string) ($communication['titulo'] ?? ''),
        'asunto' => (string) ($communication['asunto'] ?? ''),
        'canal' => (string) ($communication['canal'] ?? 'Email'),
        'audiencia' => (string) ($communication['audiencia'] ?? 'Miembros Activos'),
        'status' => (string) ($communication['status'] ?? 'Enviado'),
        'cuerpo' => (string) ($communication['cuerpo'] ?? ''),
    ];
}

function communication_filters_from_request(array $query): array
{
    return [
        'search' => trim((string) ($query['search'] ?? '')),
        'channel' => trim((string) ($query['channel'] ?? '')),
    ];
}

function communication_recipient_members(PDO $pdo, string $audience): array
{
    return match ($audience) {
        'Pagos Vencidos' => fetch_members($pdo, [
            'search' => '',
            'status' => 'Vencido',
            'sort' => 'alpha',
            'tipo' => '',
            'estado' => '',
            'industria' => '',
        ]),
        'Membresia Premium' => fetch_members($pdo, [
            'search' => '',
            'status' => '',
            'sort' => 'alpha',
            'tipo' => 'Premium',
            'estado' => '',
            'industria' => '',
        ]),
        'Membresia Corporativa' => fetch_members($pdo, [
            'search' => '',
            'status' => '',
            'sort' => 'alpha',
            'tipo' => 'Corporativa',
            'estado' => '',
            'industria' => '',
        ]),
        'Todos los Socios' => fetch_members($pdo, [
            'search' => '',
            'status' => '',
            'sort' => 'alpha',
            'tipo' => '',
            'estado' => '',
            'industria' => '',
        ]),
        default => fetch_members($pdo, [
            'search' => '',
            'status' => 'Activo',
            'sort' => 'alpha',
            'tipo' => '',
            'estado' => '',
            'industria' => '',
        ]),
    };
}

function communication_estimated_metrics(int $recipientCount, string $channel): array
{
    if ($recipientCount <= 0) {
        return ['abiertos_count' => 0, 'clicks_count' => 0];
    }

    $openRate = match ($channel) {
        'WhatsApp' => 0.93,
        'Mixto' => 0.89,
        default => 0.85,
    };
    $clickRate = match ($channel) {
        'WhatsApp' => 0.62,
        'Mixto' => 0.55,
        default => 0.44,
    };

    return [
        'abiertos_count' => min($recipientCount, (int) round($recipientCount * $openRate)),
        'clicks_count' => min($recipientCount, (int) round($recipientCount * $clickRate)),
    ];
}

function communication_open_rate(array $communication): int
{
    $recipients = (int) ($communication['destinatarios_count'] ?? 0);
    if ($recipients <= 0) {
        return 0;
    }

    return (int) round(((int) $communication['abiertos_count'] / $recipients) * 100);
}

function communication_click_rate(array $communication): int
{
    $recipients = (int) ($communication['destinatarios_count'] ?? 0);
    if ($recipients <= 0) {
        return 0;
    }

    return (int) round(((int) $communication['clicks_count'] / $recipients) * 100);
}

function validate_communication_form(array $data, PDO $pdo): array
{
    $errors = [];
    $communicationId = (int) ($data['id'] ?? 0);

    if ($communicationId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM comunicados WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $communicationId]);
        if (!$stmt->fetch()) {
            $errors['general'] = 'El comunicado que intentas actualizar ya no existe.';
        }
    }

    if ($data['titulo'] === '') {
        $errors['titulo'] = 'El titulo es obligatorio.';
    }

    if ($data['asunto'] === '') {
        $errors['asunto'] = 'El asunto es obligatorio.';
    }

    if (!in_array($data['canal'], communication_channel_options(), true)) {
        $errors['canal'] = 'Selecciona un canal valido.';
    }

    if (!in_array($data['audiencia'], communication_audience_options(), true)) {
        $errors['audiencia'] = 'Selecciona una audiencia valida.';
    }

    if ($data['cuerpo'] === '') {
        $errors['cuerpo'] = 'El mensaje es obligatorio.';
    }

    if (!in_array($data['status'], ['Enviado', 'Borrador'], true)) {
        $errors['status'] = 'Selecciona un estado valido.';
    }

    if ($data['status'] === 'Enviado' && count(communication_recipient_members($pdo, $data['audiencia'])) === 0) {
        $errors['audiencia'] = 'No hay destinatarios disponibles para esa audiencia.';
    }

    return $errors;
}

function save_communication(PDO $pdo, array $data): int
{
    $communicationId = (int) ($data['id'] ?? 0);
    $recipientCount = count(communication_recipient_members($pdo, $data['audiencia']));
    $metrics = $data['status'] === 'Enviado'
        ? communication_estimated_metrics($recipientCount, $data['canal'])
        : ['abiertos_count' => 0, 'clicks_count' => 0];
    $sentAt = $data['status'] === 'Enviado' ? date('Y-m-d H:i:s') : null;

    $params = [
        ':titulo' => $data['titulo'],
        ':asunto' => $data['asunto'],
        ':canal' => $data['canal'],
        ':audiencia' => $data['audiencia'],
        ':cuerpo' => $data['cuerpo'],
        ':status' => $data['status'],
        ':destinatarios_count' => $recipientCount,
        ':abiertos_count' => $metrics['abiertos_count'],
        ':clicks_count' => $metrics['clicks_count'],
        ':sent_at' => $sentAt,
    ];

    if ($communicationId > 0) {
        $stmt = $pdo->prepare(
            "UPDATE comunicados
             SET titulo = :titulo,
                 asunto = :asunto,
                 canal = :canal,
                 audiencia = :audiencia,
                 cuerpo = :cuerpo,
                 status = :status,
                 destinatarios_count = :destinatarios_count,
                 abiertos_count = :abiertos_count,
                 clicks_count = :clicks_count,
                 sent_at = :sent_at
             WHERE id = :id"
        );
        $params[':id'] = $communicationId;
        $stmt->execute($params);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO comunicados (
                titulo, asunto, canal, audiencia, cuerpo, status,
                destinatarios_count, abiertos_count, clicks_count, sent_at
            ) VALUES (
                :titulo, :asunto, :canal, :audiencia, :cuerpo, :status,
                :destinatarios_count, :abiertos_count, :clicks_count, :sent_at
            )"
        );
        $stmt->execute($params);
        $communicationId = (int) $pdo->lastInsertId();
    }

    log_activity(
        $pdo,
        $data['id'] !== '' ? 'comunicado_actualizado' : 'comunicado_creado',
        ($data['id'] !== '' ? 'Se actualizo el comunicado ' : 'Se preparo el comunicado ') . $data['titulo'],
        'comunicado',
        $communicationId
    );

    return $communicationId;
}

function fetch_communications(PDO $pdo, array $filters = []): array
{
    $sql = "SELECT * FROM comunicados WHERE 1 = 1";
    $params = [];

    if (($filters['search'] ?? '') !== '') {
        $sql .= " AND (titulo LIKE :search OR asunto LIKE :search OR audiencia LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (($filters['channel'] ?? '') !== '' && in_array($filters['channel'], communication_channel_options(), true)) {
        $sql .= " AND canal = :channel";
        $params[':channel'] = $filters['channel'];
    }

    $sql .= " ORDER BY COALESCE(sent_at, created_at) DESC, id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function find_communication_by_id(PDO $pdo, int $communicationId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM comunicados WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $communicationId]);
    $communication = $stmt->fetch(PDO::FETCH_ASSOC);

    return $communication ?: null;
}

function communication_recipients_preview(PDO $pdo, string $audience, int $limit = 5): array
{
    return array_slice(communication_recipient_members($pdo, $audience), 0, $limit);
}

function fetch_recent_activity(PDO $pdo, int $limit = 4): array
{
    $stmt = $pdo->prepare("SELECT * FROM dashboard_actividades ORDER BY created_at DESC, id DESC LIMIT :limite");
    $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_upcoming_events(PDO $pdo, int $limit = 3): array
{
    $stmt = $pdo->prepare(
        "SELECT * FROM dashboard_eventos
         WHERE fecha_inicio >= CURDATE()
         ORDER BY fecha_inicio ASC, hora_inicio ASC, id ASC
         LIMIT :limite"
    );
    $stmt->bindValue(':limite', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function event_type_options(): array
{
    return ['Congreso', 'Workshop', 'Networking', 'Seminario', 'Capacitacion'];
}

function event_form_defaults(): array
{
    return [
        'id' => '',
        'titulo' => '',
        'fecha_inicio' => date('Y-m-d'),
        'hora_inicio' => '09:00',
        'ubicacion' => '',
        'tipo' => 'Workshop',
        'capacidad' => '50',
        'precio' => '0',
        'descripcion' => '',
        'modalidad' => 'Presencial',
    ];
}

function normalize_event_form(array $input): array
{
    $data = event_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function event_filters_from_request(array $query): array
{
    return [
        'search' => trim((string) ($query['search'] ?? '')),
        'type' => trim((string) ($query['type'] ?? '')),
    ];
}

function fetch_events(PDO $pdo, array $filters = []): array
{
    $sql = "SELECT *,
                   CASE WHEN capacidad > 0 THEN ROUND((registrados / capacidad) * 100) ELSE 0 END AS ocupacion
            FROM dashboard_eventos
            WHERE 1 = 1";
    $params = [];

    if (($filters['search'] ?? '') !== '') {
        $sql .= " AND (titulo LIKE :search OR descripcion LIKE :search OR ubicacion LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (($filters['type'] ?? '') !== '' && in_array($filters['type'], event_type_options(), true)) {
        $sql .= " AND tipo = :type";
        $params[':type'] = $filters['type'];
    }

    $sql .= " ORDER BY fecha_inicio ASC, hora_inicio ASC, id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function validate_event_form(array $data): array
{
    $errors = [];

    if ($data['titulo'] === '') {
        $errors['titulo'] = 'El nombre del evento es obligatorio.';
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', $data['fecha_inicio']);
    if (!$date || $date->format('Y-m-d') !== $data['fecha_inicio']) {
        $errors['fecha_inicio'] = 'Selecciona una fecha valida.';
    }

    if (!preg_match('/^\d{2}:\d{2}$/', $data['hora_inicio'])) {
        $errors['hora_inicio'] = 'Selecciona una hora valida.';
    }

    if ($data['ubicacion'] === '') {
        $errors['ubicacion'] = 'La ubicacion es obligatoria.';
    }

    if (!in_array($data['tipo'], event_type_options(), true)) {
        $errors['tipo'] = 'Selecciona un tipo valido.';
    }

    if ((int) $data['capacidad'] <= 0) {
        $errors['capacidad'] = 'La capacidad debe ser mayor a 0.';
    }

    if (normalize_money($data['precio']) < 0) {
        $errors['precio'] = 'El precio no puede ser negativo.';
    }

    if ($data['descripcion'] === '') {
        $errors['descripcion'] = 'La descripcion es obligatoria.';
    }

    return $errors;
}

function event_image_for_type(string $type): string
{
    return match ($type) {
        'Congreso' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80',
        'Workshop' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
        'Networking' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1200&q=80',
        'Seminario' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=1200&q=80',
        default => 'https://images.unsplash.com/photo-1528605248644-14dd04022da1?w=1200&q=80',
    };
}

function save_event(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare(
        "INSERT INTO dashboard_eventos (
            titulo, descripcion, ubicacion, tipo, modalidad, fecha_inicio, hora_inicio, capacidad, registrados, precio, imagen_url
        ) VALUES (
            :titulo, :descripcion, :ubicacion, :tipo, :modalidad, :fecha_inicio, :hora_inicio, :capacidad, 0, :precio, :imagen_url
        )"
    );
    $stmt->execute([
        ':titulo' => $data['titulo'],
        ':descripcion' => $data['descripcion'],
        ':ubicacion' => $data['ubicacion'],
        ':tipo' => $data['tipo'],
        ':modalidad' => $data['modalidad'],
        ':fecha_inicio' => $data['fecha_inicio'],
        ':hora_inicio' => $data['hora_inicio'] . ':00',
        ':capacidad' => (int) $data['capacidad'],
        ':precio' => normalize_money($data['precio']),
        ':imagen_url' => event_image_for_type($data['tipo']),
    ]);

    $eventId = (int) $pdo->lastInsertId();
    log_activity($pdo, 'evento_creado', 'Se creo el evento ' . $data['titulo'], 'evento', $eventId);

    return $eventId;
}

function format_event_datetime(string $date, string $time): string
{
    $dateLabel = format_short_date($date);
    $timeLabel = substr($time, 0, 5);

    return $dateLabel . ' - ' . $timeLabel;
}

function document_category_options(): array
{
    return ['Estatutos', 'Actas', 'Reglamentos', 'Politicas', 'Formatos'];
}

function document_access_options(): array
{
    return ['Todos los socios', 'Mesa directiva', 'Administracion'];
}

function document_form_defaults(): array
{
    return [
        'titulo' => '',
        'categoria' => 'Estatutos',
        'nivel_acceso' => 'Todos los socios',
    ];
}

function normalize_document_form(array $input): array
{
    $data = document_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function document_filters_from_request(array $query): array
{
    return [
        'search' => trim((string) ($query['search'] ?? '')),
        'category' => trim((string) ($query['category'] ?? '')),
    ];
}

function fetch_documents(PDO $pdo, array $filters = []): array
{
    $sql = "SELECT * FROM documentos WHERE 1 = 1";
    $params = [];

    if (($filters['search'] ?? '') !== '') {
        $sql .= " AND (titulo LIKE :search OR categoria LIKE :search OR nivel_acceso LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (($filters['category'] ?? '') !== '' && in_array($filters['category'], document_category_options(), true)) {
        $sql .= " AND categoria = :category";
        $params[':category'] = $filters['category'];
    }

    $sql .= " ORDER BY created_at DESC, id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function find_document_by_id(PDO $pdo, int $documentId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM documentos WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $documentId]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    return $document ?: null;
}

function validate_document_form(array $data, ?array $documentUpload = null): array
{
    $errors = [];

    if ($data['titulo'] === '') {
        $errors['titulo'] = 'El titulo del documento es obligatorio.';
    }

    if (!in_array($data['categoria'], document_category_options(), true)) {
        $errors['categoria'] = 'Selecciona una categoria valida.';
    }

    if (!in_array($data['nivel_acceso'], document_access_options(), true)) {
        $errors['nivel_acceso'] = 'Selecciona un nivel de acceso valido.';
    }

    $uploadError = validate_document_upload($documentUpload);
    if ($uploadError !== null) {
        $errors['archivo'] = $uploadError;
    }

    return $errors;
}

function validate_document_upload(?array $documentUpload): ?string
{
    if (!is_array($documentUpload) || ($documentUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return 'Selecciona un archivo para subir.';
    }

    if (($documentUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return 'No fue posible cargar el archivo.';
    }

    if (($documentUpload['size'] ?? 0) > 10 * 1024 * 1024) {
        return 'El archivo no debe exceder 10 MB.';
    }

    $extension = strtolower(pathinfo((string) ($documentUpload['name'] ?? ''), PATHINFO_EXTENSION));
    $allowedExtensions = ['pdf', 'doc', 'docx'];
    if (!in_array($extension, $allowedExtensions, true)) {
        return 'El archivo debe ser PDF, DOC o DOCX.';
    }

    return null;
}

function document_upload_directory_path(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'documents';
}

function document_upload_directory_url(): string
{
    return 'assets/uploads/documents';
}

function store_document_upload(array $documentUpload): array
{
    $extension = strtolower(pathinfo((string) ($documentUpload['name'] ?? ''), PATHINFO_EXTENSION));
    $mimeTypeMap = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    $mimeType = $mimeTypeMap[$extension] ?? 'application/octet-stream';
    $directoryPath = document_upload_directory_path();
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0775, true);
    }

    $fileName = 'documento-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
    $destination = $directoryPath . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($documentUpload['tmp_name'], $destination)) {
        throw new RuntimeException('No fue posible guardar el documento.');
    }

    return [
        'file_path' => document_upload_directory_url() . '/' . $fileName,
        'file_ext' => $extension,
        'mime_type' => $mimeType,
        'file_size_bytes' => (int) filesize($destination),
        'original_filename' => (string) ($documentUpload['name'] ?? $fileName),
    ];
}

function save_document(PDO $pdo, array $data, array $documentUpload): int
{
    $storedFile = store_document_upload($documentUpload);
    $stmt = $pdo->prepare(
        "INSERT INTO documentos (
            titulo, categoria, nivel_acceso, original_filename, file_ext, mime_type,
            file_size_bytes, storage_mode, file_path, generated_content
        ) VALUES (
            :titulo, :categoria, :nivel_acceso, :original_filename, :file_ext, :mime_type,
            :file_size_bytes, 'uploaded', :file_path, NULL
        )"
    );
    $stmt->execute([
        ':titulo' => $data['titulo'],
        ':categoria' => $data['categoria'],
        ':nivel_acceso' => $data['nivel_acceso'],
        ':original_filename' => $storedFile['original_filename'],
        ':file_ext' => $storedFile['file_ext'],
        ':mime_type' => $storedFile['mime_type'],
        ':file_size_bytes' => $storedFile['file_size_bytes'],
        ':file_path' => $storedFile['file_path'],
    ]);

    $documentId = (int) $pdo->lastInsertId();
    log_activity($pdo, 'documento_subido', 'Se subio el documento ' . $data['titulo'], 'documento', $documentId);

    return $documentId;
}

function format_file_size(int $bytes): string
{
    if ($bytes >= 1024 * 1024) {
        return number_format($bytes / (1024 * 1024), 1) . ' MB';
    }

    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }

    return $bytes . ' B';
}

function generated_document_pdf_bytes(string $title, string $body): string
{
    $text = $title . "\n\n" . $body;
    $escaped = str_replace(["\\", "(", ")", "\r"], ["\\\\", "\\(", "\\)", ''], $text);
    $lines = explode("\n", $escaped);
    $commands = [];
    $y = 760;
    foreach ($lines as $line) {
        $commands[] = 'BT /F1 14 Tf 50 ' . $y . ' Td (' . trim($line) . ') Tj ET';
        $y -= 24;
    }
    $stream = implode("\n", $commands) . "\n";
    $length = strlen($stream);

    $pdf = "%PDF-1.4\n";
    $offsets = [];
    $objects = [
        "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
        "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
        "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
        "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
        "5 0 obj\n<< /Length {$length} >>\nstream\n{$stream}endstream\nendobj\n",
    ];

    foreach ($objects as $object) {
        $offsets[] = strlen($pdf);
        $pdf .= $object;
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 6\n0000000000 65535 f \n";
    foreach ($offsets as $offset) {
        $pdf .= sprintf("%010d 00000 n \n", $offset);
    }
    $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";

    return $pdf;
}

function output_document_response(array $document, bool $download): void
{
    $fileName = trim((string) ($document['original_filename'] ?? 'documento.pdf')) !== ''
        ? (string) $document['original_filename']
        : ((string) ($document['titulo'] ?? 'documento') . '.' . ($document['file_ext'] ?? 'pdf'));
    $disposition = $download ? 'attachment' : 'inline';

    if (($document['storage_mode'] ?? 'generated') === 'uploaded' && !empty($document['file_path'])) {
        $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $document['file_path']);
        if (!is_file($absolutePath)) {
            throw new RuntimeException('El archivo solicitado no existe en el servidor.');
        }

        header('Content-Type: ' . ($document['mime_type'] ?: 'application/octet-stream'));
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . filesize($absolutePath));
        readfile($absolutePath);
        exit;
    }

    $pdf = generated_document_pdf_bytes((string) $document['titulo'], (string) ($document['generated_content'] ?? 'Documento generado por AsociaPro.'));
    header('Content-Type: application/pdf');
    header('Content-Disposition: ' . $disposition . '; filename="' . basename($fileName) . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}

function news_category_options(): array
{
    return ['Noticias', 'Eventos', 'Convenios', 'Comunicados'];
}

function news_image_for_category(string $category): string
{
    return match ($category) {
        'Eventos' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
        'Convenios' => 'https://images.unsplash.com/photo-1556740749-887f6717d7e4?w=1200&q=80',
        'Comunicados' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1200&q=80',
        default => 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=1200&q=80',
    };
}

function news_form_defaults(): array
{
    return [
        'id' => '',
        'titulo' => '',
        'categoria' => 'Noticias',
        'resumen' => '',
        'contenido' => '',
    ];
}

function normalize_news_form(array $input): array
{
    $data = news_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function fetch_news(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM noticias ORDER BY created_at DESC, id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function find_news_by_id(PDO $pdo, int $newsId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $newsId]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    return $news ?: null;
}

function validate_news_form(array $data): array
{
    $errors = [];

    if ($data['titulo'] === '') {
        $errors['titulo'] = 'El titulo es obligatorio.';
    }

    if (!in_array($data['categoria'], news_category_options(), true)) {
        $errors['categoria'] = 'Selecciona una categoria valida.';
    }

    if ($data['resumen'] === '') {
        $errors['resumen'] = 'El resumen es obligatorio.';
    }

    if ($data['contenido'] === '') {
        $errors['contenido'] = 'El contenido es obligatorio.';
    }

    return $errors;
}

function save_news(PDO $pdo, array $data): int
{
    $newsId = (int) ($data['id'] ?? 0);

    if ($newsId > 0) {
        $existing = find_news_by_id($pdo, $newsId);
        if (!$existing) {
            throw new RuntimeException('La noticia seleccionada no existe.');
        }

        $stmt = $pdo->prepare(
            "UPDATE noticias
             SET titulo = :titulo,
                 categoria = :categoria,
                 resumen = :resumen,
                 contenido = :contenido,
                 imagen_url = COALESCE(NULLIF(imagen_url, ''), :imagen_url)
             WHERE id = :id"
        );
        $stmt->execute([
            ':titulo' => $data['titulo'],
            ':categoria' => $data['categoria'],
            ':resumen' => $data['resumen'],
            ':contenido' => $data['contenido'],
            ':imagen_url' => news_image_for_category($data['categoria']),
            ':id' => $newsId,
        ]);

        log_activity($pdo, 'noticia_actualizada', 'Se actualizo la noticia ' . $data['titulo'], 'noticia', $newsId);
        return $newsId;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO noticias (
            titulo, categoria, resumen, contenido, autor, status, imagen_url
        ) VALUES (
            :titulo, :categoria, :resumen, :contenido, :autor, 'Publicado', :imagen_url
        )"
    );
    $stmt->execute([
        ':titulo' => $data['titulo'],
        ':categoria' => $data['categoria'],
        ':resumen' => $data['resumen'],
        ':contenido' => $data['contenido'],
        ':autor' => current_user_name(),
        ':imagen_url' => news_image_for_category($data['categoria']),
    ]);

    $createdId = (int) $pdo->lastInsertId();
    log_activity($pdo, 'noticia_creada', 'Se publico la noticia ' . $data['titulo'], 'noticia', $createdId);

    return $createdId;
}

function delete_news(PDO $pdo, int $newsId): void
{
    $news = find_news_by_id($pdo, $newsId);
    if (!$news) {
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = :id");
    $stmt->execute([':id' => $newsId]);

    log_activity($pdo, 'noticia_eliminada', 'Se elimino la noticia ' . $news['titulo'], 'noticia', $newsId);
}

function format_news_publish_date(string $dateTime): string
{
    $months = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre',
    ];

    $timestamp = strtotime($dateTime);
    if ($timestamp === false) {
        return $dateTime;
    }

    $monthIndex = (int) date('n', $timestamp);
    return date('j', $timestamp) . ' de ' . ($months[$monthIndex] ?? date('M', $timestamp)) . ' de ' . date('Y', $timestamp);
}

function voting_form_defaults(): array
{
    return [
        'titulo' => '',
        'descripcion' => '',
        'fecha_inicio' => '',
        'fecha_cierre' => '',
    ];
}

function normalize_voting_form(array $input): array
{
    $data = voting_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function validate_voting_form(array $data): array
{
    $errors = [];

    if ($data['titulo'] === '') {
        $errors['titulo'] = 'El titulo de la votacion es obligatorio.';
    }

    if ($data['descripcion'] === '') {
        $errors['descripcion'] = 'La descripcion es obligatoria.';
    }

    if ($data['fecha_inicio'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha_inicio'])) {
        $errors['fecha_inicio'] = 'Selecciona una fecha de inicio valida.';
    }

    if ($data['fecha_cierre'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['fecha_cierre'])) {
        $errors['fecha_cierre'] = 'Selecciona una fecha de cierre valida.';
    }

    if (!isset($errors['fecha_inicio'], $errors['fecha_cierre']) && strtotime($data['fecha_cierre']) < strtotime($data['fecha_inicio'])) {
        $errors['fecha_cierre'] = 'La fecha de cierre no puede ser anterior al inicio.';
    }

    return $errors;
}

function fetch_votes(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM votaciones ORDER BY fecha_inicio ASC, id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map('decorate_vote_record', $rows);
}

function find_vote_by_id(PDO $pdo, int $voteId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM votaciones WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $voteId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? decorate_vote_record($row) : null;
}

function decorate_vote_record(array $vote): array
{
    $favor = (int) ($vote['votos_favor'] ?? 0);
    $contra = (int) ($vote['votos_contra'] ?? 0);
    $abstenciones = (int) ($vote['abstenciones'] ?? 0);
    $participation = $favor + $contra + $abstenciones;
    $eligible = max(1, (int) ($vote['total_elegibles'] ?? 0));

    $vote['participacion_total'] = $participation;
    $vote['participacion_porcentaje'] = (int) round(($participation / $eligible) * 100);
    $vote['favor_porcentaje'] = $participation > 0 ? (int) round(($favor / $participation) * 100) : 0;
    $vote['contra_porcentaje'] = $participation > 0 ? (int) round(($contra / $participation) * 100) : 0;
    $vote['abstencion_porcentaje'] = $participation > 0 ? (int) round(($abstenciones / $participation) * 100) : 0;
    $vote['status_label'] = voting_status_label((string) ($vote['fecha_inicio'] ?? ''), (string) ($vote['fecha_cierre'] ?? ''));
    $vote['status_badge_class'] = voting_status_badge_class($vote['status_label']);

    return $vote;
}

function voting_status_label(string $startDate, string $endDate): string
{
    $today = date('Y-m-d');
    if ($today < $startDate) {
        return 'Proxima';
    }
    if ($today > $endDate) {
        return 'Finalizada';
    }

    return 'En curso';
}

function voting_status_badge_class(string $label): string
{
    return match ($label) {
        'En curso' => 'active',
        'Proxima' => 'info',
        default => 'inactive',
    };
}

function save_vote(PDO $pdo, array $data): int
{
    $eligibleMembers = (int) $pdo->query("SELECT COUNT(*) FROM socios WHERE activo = 1")->fetchColumn();
    $record = [
        'titulo' => $data['titulo'],
        'descripcion' => $data['descripcion'],
        'fecha_inicio' => $data['fecha_inicio'],
        'fecha_cierre' => $data['fecha_cierre'],
        'total_elegibles' => max(1, $eligibleMembers),
        'votos_favor' => 0,
        'votos_contra' => 0,
        'abstenciones' => 0,
    ];

    $stmt = $pdo->prepare(
        "INSERT INTO votaciones (
            titulo, descripcion, fecha_inicio, fecha_cierre, total_elegibles,
            votos_favor, votos_contra, abstenciones, acta_contenido
        ) VALUES (
            :titulo, :descripcion, :fecha_inicio, :fecha_cierre, :total_elegibles,
            0, 0, 0, :acta_contenido
        )"
    );
    $stmt->execute([
        ':titulo' => $record['titulo'],
        ':descripcion' => $record['descripcion'],
        ':fecha_inicio' => $record['fecha_inicio'],
        ':fecha_cierre' => $record['fecha_cierre'],
        ':total_elegibles' => $record['total_elegibles'],
        ':acta_contenido' => generate_voting_minutes_body($record),
    ]);

    $voteId = (int) $pdo->lastInsertId();
    log_activity($pdo, 'votacion_creada', 'Se creo la votacion ' . $record['titulo'], 'votacion', $voteId);

    return $voteId;
}

function generate_voting_minutes_body(array $vote): string
{
    return implode("\n", [
        $vote['titulo'],
        '',
        $vote['descripcion'],
        '',
        'Periodo de votacion: ' . format_short_date((string) $vote['fecha_inicio']) . ' al ' . format_short_date((string) $vote['fecha_cierre']),
        'Participantes elegibles: ' . (int) ($vote['total_elegibles'] ?? 0),
        'A favor: ' . (int) ($vote['votos_favor'] ?? 0),
        'En contra: ' . (int) ($vote['votos_contra'] ?? 0),
        'Abstenciones: ' . (int) ($vote['abstenciones'] ?? 0),
    ]);
}

function output_vote_minutes_response(array $vote): void
{
    $fileName = 'acta-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower((string) $vote['titulo'])) . '.pdf';
    $content = (string) ($vote['acta_contenido'] ?? generate_voting_minutes_body($vote));
    $pdf = generated_document_pdf_bytes((string) $vote['titulo'], $content);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}

function dashboard_metrics(PDO $pdo): array
{
    $total = (int) $pdo->query("SELECT COUNT(*) FROM socios")->fetchColumn();
    $active = (int) $pdo->query("SELECT COUNT(*) FROM socios WHERE activo = 1 AND fecha_vencimiento >= CURDATE()")->fetchColumn();
    $expired = (int) $pdo->query("SELECT COUNT(*) FROM socios WHERE activo = 0 OR fecha_vencimiento < CURDATE()")->fetchColumn();

    $revenueStmt = $pdo->prepare(
        "SELECT COALESCE(SUM(cuota_anual), 0)
         FROM socios
         WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())"
    );
    $revenueStmt->execute();
    $monthlyRevenue = (float) $revenueStmt->fetchColumn();

    $currentMonthStart = new DateTimeImmutable('first day of this month 00:00:00');
    $previousMonthStart = $currentMonthStart->modify('-1 month');
    $nextMonthStart = $currentMonthStart->modify('+1 month');

    $currentNewMembers = count_members_between($pdo, $currentMonthStart, $nextMonthStart);
    $previousNewMembers = count_members_between($pdo, $previousMonthStart, $currentMonthStart);

    $currentExpired = count_expired_between($pdo, $currentMonthStart, $nextMonthStart);
    $previousExpired = count_expired_between($pdo, $previousMonthStart, $currentMonthStart);

    $currentRevenue = revenue_between($pdo, $currentMonthStart, $nextMonthStart);
    $previousRevenue = revenue_between($pdo, $previousMonthStart, $currentMonthStart);

    return [
        'total' => $total,
        'active' => $active,
        'expired' => $expired,
        'monthly_revenue' => $monthlyRevenue,
        'trend_total' => percentage_change($currentNewMembers, $previousNewMembers),
        'trend_active' => percentage_change($active, max($active - $currentNewMembers + $previousNewMembers, 0)),
        'trend_expired' => percentage_change($currentExpired, $previousExpired),
        'trend_revenue' => percentage_change($currentRevenue, $previousRevenue),
    ];
}

function count_members_between(PDO $pdo, DateTimeImmutable $start, DateTimeImmutable $end): int
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM socios WHERE created_at >= :inicio AND created_at < :fin");
    $stmt->execute([
        ':inicio' => $start->format('Y-m-d H:i:s'),
        ':fin' => $end->format('Y-m-d H:i:s'),
    ]);

    return (int) $stmt->fetchColumn();
}

function count_expired_between(PDO $pdo, DateTimeImmutable $start, DateTimeImmutable $end): int
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM socios WHERE fecha_vencimiento >= :inicio AND fecha_vencimiento < :fin");
    $stmt->execute([
        ':inicio' => $start->format('Y-m-d'),
        ':fin' => $end->format('Y-m-d'),
    ]);

    return (int) $stmt->fetchColumn();
}

function revenue_between(PDO $pdo, DateTimeImmutable $start, DateTimeImmutable $end): float
{
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(cuota_anual), 0)
         FROM socios
         WHERE created_at >= :inicio AND created_at < :fin"
    );
    $stmt->execute([
        ':inicio' => $start->format('Y-m-d H:i:s'),
        ':fin' => $end->format('Y-m-d H:i:s'),
    ]);

    return (float) $stmt->fetchColumn();
}

function percentage_change(float $current, float $previous): float
{
    if ($previous == 0.0) {
        return $current > 0 ? 100.0 : 0.0;
    }

    return (($current - $previous) / $previous) * 100;
}

function dashboard_series(PDO $pdo): array
{
    $labels = [];
    $membershipCounts = [];
    $revenueTotals = [];
    $cumulativeTotal = 0;

    $monthlyRevenueMap = [];
    $monthlyRevenueStmt = $pdo->query(
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COALESCE(SUM(cuota_anual), 0) AS total
         FROM socios
         GROUP BY ym"
    );

    foreach ($monthlyRevenueStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $monthlyRevenueMap[$row['ym']] = (float) $row['total'];
    }

    $joinedMembersStmt = $pdo->query("SELECT created_at FROM socios ORDER BY created_at ASC");
    $joinedMembers = $joinedMembersStmt->fetchAll(PDO::FETCH_COLUMN);

    $joinedByMonth = [];
    foreach ($joinedMembers as $createdAt) {
        $key = date('Y-m', strtotime($createdAt));
        if (!isset($joinedByMonth[$key])) {
            $joinedByMonth[$key] = 0;
        }
        $joinedByMonth[$key]++;
    }

    for ($offset = 5; $offset >= 0; $offset--) {
        $month = new DateTimeImmutable('first day of -' . $offset . ' months');
        $key = $month->format('Y-m');
        $labels[] = month_name_short((int) $month->format('n'));
        $cumulativeTotal += $joinedByMonth[$key] ?? 0;
        $membershipCounts[] = $cumulativeTotal;
        $revenueTotals[] = $monthlyRevenueMap[$key] ?? 0.0;
    }

    return [
        'labels' => $labels,
        'membership_counts' => $membershipCounts,
        'revenue_totals' => $revenueTotals,
    ];
}

function export_members_csv(array $members): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="socios.csv"');

    $output = fopen('php://output', 'wb');
    fwrite($output, chr(239) . chr(187) . chr(191));

    fputcsv($output, ['Nombre', 'Email', 'Telefono', 'Empresa', 'Puesto', 'Industria', 'Ciudad', 'Estado', 'Membresia', 'Estatus', 'Vencimiento']);

    foreach ($members as $member) {
        fputcsv($output, [
            $member['nombre_completo'],
            $member['email'],
            $member['telefono'],
            $member['empresa'],
            $member['puesto'],
            $member['industria'],
            $member['ciudad'],
            $member['estado'],
            $member['tipo_membresia'],
            $member['estatus'],
            $member['fecha_vencimiento'],
        ]);
    }

    fclose($output);
}

function build_query_string(array $params): string
{
    $filtered = [];

    foreach ($params as $key => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $filtered[$key] = $value;
    }

    return http_build_query($filtered);
}

function count_pending_requests(PDO $pdo): int
{
    return (int) $pdo->query("SELECT COUNT(*) FROM solicitudes_membresia WHERE status = 'Pendiente'")->fetchColumn();
}

function request_badge_label(int $count): string
{
    return $count > 99 ? '99+' : (string) $count;
}

function fetch_pending_requests(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT * FROM solicitudes_membresia
         WHERE status = 'Pendiente'
         ORDER BY submitted_at ASC, id ASC"
    );
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map('hydrate_request_documents', $requests);
}

function find_request_by_id(PDO $pdo, int $requestId): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM solicitudes_membresia WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    return $request ? hydrate_request_documents($request) : null;
}

function hydrate_request_documents(array $request): array
{
    $request['documentos'] = decode_request_documents($request['documentos_json'] ?? '[]');

    return $request;
}

function decode_request_documents(string $documentsJson): array
{
    $documents = json_decode($documentsJson, true);

    return is_array($documents) ? $documents : [];
}

function approve_request(PDO $pdo, int $requestId): array
{
    $request = find_request_by_id($pdo, $requestId);
    if (!$request || $request['status'] !== 'Pendiente') {
        throw new RuntimeException('La solicitud ya no esta disponible para revision.');
    }

    $memberData = [
        'id' => '',
        'nombre_completo' => $request['nombre_completo'],
        'email' => $request['email'],
        'telefono' => $request['telefono'],
        'empresa' => $request['empresa'],
        'puesto' => $request['puesto'],
        'industria' => $request['industria'],
        'ciudad' => $request['ciudad'],
        'estado' => $request['estado'],
        'tipo_membresia' => $request['tipo_membresia'],
        'avatar_url' => $request['avatar_url'] ?: member_avatar_for_email($request['email']),
    ];

    $validationErrors = validate_member_form($memberData, $pdo);
    if ($validationErrors !== []) {
        throw new RuntimeException(reset($validationErrors));
    }

    $memberId = save_member($pdo, $memberData, null);

    $stmt = $pdo->prepare(
        "UPDATE solicitudes_membresia
         SET status = 'Aprobada', reviewed_at = NOW()
         WHERE id = :id"
    );
    $stmt->execute([':id' => $requestId]);

    log_activity($pdo, 'solicitud_aprobada', 'Solicitud aprobada para ' . $request['nombre_completo'], 'solicitud', $requestId);

    return [
        'request' => $request,
        'member_id' => $memberId,
    ];
}

function reject_request(PDO $pdo, int $requestId): array
{
    $request = find_request_by_id($pdo, $requestId);
    if (!$request || $request['status'] !== 'Pendiente') {
        throw new RuntimeException('La solicitud ya no esta disponible para revision.');
    }

    $stmt = $pdo->prepare(
        "UPDATE solicitudes_membresia
         SET status = 'Rechazada', reviewed_at = NOW()
         WHERE id = :id"
    );
    $stmt->execute([':id' => $requestId]);

    log_activity($pdo, 'solicitud_rechazada', 'Solicitud rechazada para ' . $request['nombre_completo'], 'solicitud', $requestId);

    return $request;
}

function format_request_submission_date(string $dateTime): string
{
    $dateObject = new DateTimeImmutable($dateTime);
    $day = $dateObject->format('j');
    $month = month_name_long((int) $dateObject->format('n'));
    $year = $dateObject->format('Y');

    return $day . ' de ' . $month . ' de ' . $year;
}

function month_name_long(int $monthNumber): string
{
    $months = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre',
    ];

    return $months[$monthNumber] ?? '';
}

function request_initial(string $name): string
{
    return strtoupper(substr(trim($name), 0, 1));
}

function download_request_documents(array $request): void
{
    $safeBaseName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($request['nombre_completo']));
    $safeBaseName = trim((string) $safeBaseName, '-');
    $fileBaseName = ($safeBaseName !== '' ? $safeBaseName : 'solicitud') . '-documentos';
    $documents = $request['documentos'] ?? [];

    if (class_exists('ZipArchive')) {
        $tempFile = tempnam(sys_get_temp_dir(), 'reqdocs_');
        $zip = new ZipArchive();

        if ($zip->open($tempFile, ZipArchive::OVERWRITE) === true) {
            foreach ($documents as $index => $document) {
                $documentName = $document['nombre'] ?? ('Documento ' . ($index + 1));
                $fileName = ($document['archivo'] ?? $documentName) . '.txt';
                $content = "Documento: " . $documentName . PHP_EOL
                    . "Solicitante: " . $request['nombre_completo'] . PHP_EOL
                    . "Empresa: " . $request['empresa'] . PHP_EOL
                    . "Referencia: " . ($document['archivo'] ?? 'archivo-referencial') . PHP_EOL;
                $zip->addFromString($fileName, $content);
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $fileBaseName . '.zip"');
            header('Content-Length: ' . filesize($tempFile));
            readfile($tempFile);
            unlink($tempFile);
            exit;
        }
    }

    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $fileBaseName . '.txt"');

    echo "Expediente de solicitud\n";
    echo "Solicitante: " . $request['nombre_completo'] . "\n";
    echo "Empresa: " . $request['empresa'] . "\n\n";
    echo "Documentos adjuntos:\n";
    foreach ($documents as $document) {
        echo "- " . ($document['nombre'] ?? 'Documento') . " (" . ($document['archivo'] ?? 'referencia') . ")\n";
    }
    exit;
}

function fetch_roles_indexed_by_name(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT * FROM roles ORDER BY nombre ASC");
    $roles = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $role) {
        $roles[$role['nombre']] = $role;
    }

    return $roles;
}

function fetch_roles(PDO $pdo): array
{
    return array_values(fetch_roles_indexed_by_name($pdo));
}

function merge_legacy_role(PDO $pdo, string $legacyName, string $targetName): void
{
    $roles = fetch_roles_indexed_by_name($pdo);
    if (!isset($roles[$legacyName]) || !isset($roles[$targetName])) {
        return;
    }

    $legacyId = (int) $roles[$legacyName]['id'];
    $targetId = (int) $roles[$targetName]['id'];

    if ($legacyId === $targetId) {
        return;
    }

    $stmt = $pdo->prepare("UPDATE usuarios SET rol_id = :target_id WHERE rol_id = :legacy_id");
    $stmt->execute([
        ':target_id' => $targetId,
        ':legacy_id' => $legacyId,
    ]);

    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = :legacy_id");
    $stmt->execute([':legacy_id' => $legacyId]);
}

function users_tab_from_request(array $query): string
{
    return (($query['tab'] ?? 'users') === 'roles') ? 'roles' : 'users';
}

function user_form_defaults(): array
{
    return [
        'nombre' => '',
        'email' => '',
        'rol_id' => '',
        'activo' => '1',
        'password' => '',
        'password_confirm' => '',
        'avatar_url' => '',
    ];
}

function normalize_user_form(array $input): array
{
    $data = user_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function validate_user_form(array $data, PDO $pdo, ?array $avatarUpload = null): array
{
    $errors = [];

    if ($data['nombre'] === '') {
        $errors['nombre'] = 'El nombre completo es obligatorio.';
    }

    if ($data['email'] === '') {
        $errors['email'] = 'El correo electronico es obligatorio.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Ingresa un correo electronico valido.';
    }

    if ($data['rol_id'] === '') {
        $errors['rol_id'] = 'Selecciona un rol.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $data['rol_id']]);
        if (!$stmt->fetch()) {
            $errors['rol_id'] = 'Selecciona un rol valido.';
        }
    }

    if ($data['password'] === '') {
        $errors['password'] = 'La contrasena temporal es obligatoria.';
    } elseif (strlen($data['password']) < 8) {
        $errors['password'] = 'La contrasena debe tener al menos 8 caracteres.';
    }

    if ($data['password_confirm'] === '') {
        $errors['password_confirm'] = 'Confirma la contrasena.';
    } elseif ($data['password_confirm'] !== $data['password']) {
        $errors['password_confirm'] = 'Las contrasenas no coinciden.';
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $data['email']]);
    if ($stmt->fetch()) {
        $errors['email'] = 'Ya existe un usuario registrado con ese correo.';
    }

    $avatarError = validate_avatar_upload($avatarUpload);
    if ($avatarError !== null) {
        $errors['avatar'] = $avatarError;
    }

    return $errors;
}

function save_user(PDO $pdo, array $data, ?array $avatarUpload = null): int
{
    $avatarUrl = has_uploaded_avatar($avatarUpload)
        ? store_avatar_upload($avatarUpload)
        : member_avatar_for_email($data['email']);

    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (nombre, email, password_hash, rol_id, avatar_url, activo, ultimo_acceso)
         VALUES (:nombre, :email, :password_hash, :rol_id, :avatar_url, :activo, NULL)"
    );
    $stmt->execute([
        ':nombre' => $data['nombre'],
        ':email' => $data['email'],
        ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ':rol_id' => (int) $data['rol_id'],
        ':avatar_url' => $avatarUrl,
        ':activo' => $data['activo'] === '0' ? 0 : 1,
    ]);

    $userId = (int) $pdo->lastInsertId();
    log_activity($pdo, 'alta_usuario', 'Se creo el usuario ' . $data['nombre'], 'usuario', $userId);

    return $userId;
}

function fetch_users(PDO $pdo, string $search = ''): array
{
    $sql = "SELECT u.*, r.nombre AS rol_nombre, r.descripcion AS rol_descripcion
            FROM usuarios u
            INNER JOIN roles r ON r.id = u.rol_id
            WHERE 1 = 1";
    $params = [];

    if ($search !== '') {
        $sql .= " AND (u.nombre LIKE :search OR u.email LIKE :search OR r.nombre LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $sql .= " ORDER BY u.nombre ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function format_last_access(?string $dateTime): string
{
    if (!$dateTime) {
        return 'Sin acceso';
    }

    return (new DateTimeImmutable($dateTime))->format('Y-m-d H:i');
}

function role_meta(string $roleName): array
{
    $catalog = roles_catalog();

    return $catalog[$roleName] ?? [
        'description' => '',
        'permissions' => '',
        'icon' => 'ph-shield',
        'tone' => 'blue',
    ];
}

function payment_methods(): array
{
    return ['Transferencia', 'Tarjeta', 'Efectivo', 'Cheque'];
}

function payment_statuses(): array
{
    return ['Pagado', 'Pendiente', 'Vencido'];
}

function payment_form_defaults(): array
{
    return [
        'id' => '',
        'socio_id' => '',
        'concepto' => 'Cuota anual ' . date('Y'),
        'monto' => '',
        'metodo_pago' => 'Transferencia',
        'fecha_pago' => date('Y-m-d'),
        'numero_factura' => next_invoice_number(),
        'status' => 'Pagado',
        'notas' => '',
    ];
}

function next_invoice_number(): string
{
    return 'FAC-' . date('Y') . '-' . random_int(100, 99999);
}

function normalize_payment_form(array $input): array
{
    $data = payment_form_defaults();
    foreach ($data as $key => $defaultValue) {
        if (array_key_exists($key, $input)) {
            $data[$key] = trim((string) $input[$key]);
        }
    }

    return $data;
}

function payment_form_data_from_payment(array $payment): array
{
    return [
        'id' => (string) ($payment['id'] ?? ''),
        'socio_id' => (string) ($payment['socio_id'] ?? ''),
        'concepto' => (string) ($payment['concepto'] ?? ''),
        'monto' => isset($payment['monto']) ? (string) normalize_money($payment['monto']) : '',
        'metodo_pago' => (string) ($payment['metodo_pago'] ?? 'Transferencia'),
        'fecha_pago' => (string) ($payment['fecha_pago'] ?? date('Y-m-d')),
        'numero_factura' => (string) ($payment['numero_factura'] ?? ''),
        'status' => (string) ($payment['status'] ?? 'Pagado'),
        'notas' => (string) ($payment['notas'] ?? ''),
    ];
}

function payment_filters_from_request(array $query): array
{
    return [
        'search' => trim((string) ($query['search'] ?? '')),
        'status' => trim((string) ($query['status'] ?? '')),
    ];
}

function fetch_payments(PDO $pdo, array $filters = []): array
{
    $sql = "SELECT p.*, s.nombre_completo AS socio_nombre, s.empresa AS socio_empresa, s.tipo_membresia
            FROM pagos p
            INNER JOIN socios s ON s.id = p.socio_id
            WHERE 1 = 1";
    $params = [];

    if (($filters['search'] ?? '') !== '') {
        $sql .= " AND (
            s.nombre_completo LIKE :search
            OR p.concepto LIKE :search
            OR COALESCE(p.numero_factura, '') LIKE :search
        )";
        $params[':search'] = '%' . $filters['search'] . '%';
    }

    if (($filters['status'] ?? '') !== '' && in_array($filters['status'], payment_statuses(), true)) {
        $sql .= " AND p.status = :status";
        $params[':status'] = $filters['status'];
    }

    $sql .= " ORDER BY p.fecha_pago DESC, p.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function find_payment_by_id(PDO $pdo, int $paymentId): ?array
{
    $stmt = $pdo->prepare(
        "SELECT p.*, s.nombre_completo AS socio_nombre, s.empresa AS socio_empresa, s.tipo_membresia, s.email AS socio_email
         FROM pagos p
         INNER JOIN socios s ON s.id = p.socio_id
         WHERE p.id = :id
         LIMIT 1"
    );
    $stmt->execute([':id' => $paymentId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    return $payment ?: null;
}

function payment_metrics(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT
            COALESCE(SUM(monto), 0) AS total_ingresos,
            COALESCE(SUM(CASE WHEN status = 'Pagado' THEN monto ELSE 0 END), 0) AS total_pagado,
            COALESCE(SUM(CASE WHEN status = 'Pendiente' THEN monto ELSE 0 END), 0) AS total_pendiente,
            COALESCE(SUM(CASE WHEN status = 'Vencido' THEN monto ELSE 0 END), 0) AS total_vencido
         FROM pagos"
    );
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    return [
        'total_ingresos' => (float) ($row['total_ingresos'] ?? 0),
        'total_pagado' => (float) ($row['total_pagado'] ?? 0),
        'total_pendiente' => (float) ($row['total_pendiente'] ?? 0),
        'total_vencido' => (float) ($row['total_vencido'] ?? 0),
    ];
}

function validate_payment_form(array $data, PDO $pdo): array
{
    $errors = [];
    $paymentId = (int) ($data['id'] ?? 0);

    if ($paymentId > 0) {
        $stmt = $pdo->prepare("SELECT id FROM pagos WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $paymentId]);
        if (!$stmt->fetch()) {
            $errors['general'] = 'El pago que intentas actualizar ya no existe.';
        }
    }

    if ($data['socio_id'] === '') {
        $errors['socio_id'] = 'Selecciona un socio.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM socios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => (int) $data['socio_id']]);
        if (!$stmt->fetch()) {
            $errors['socio_id'] = 'Selecciona un socio valido.';
        }
    }

    if ($data['concepto'] === '') {
        $errors['concepto'] = 'El concepto es obligatorio.';
    }

    $amount = normalize_money($data['monto']);
    if ($amount <= 0) {
        $errors['monto'] = 'El monto debe ser mayor a 0.';
    }

    if (!in_array($data['metodo_pago'], payment_methods(), true)) {
        $errors['metodo_pago'] = 'Selecciona un metodo de pago valido.';
    }

    if ($data['fecha_pago'] === '') {
        $errors['fecha_pago'] = 'La fecha de pago es obligatoria.';
    } else {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $data['fecha_pago']);
        if (!$date || $date->format('Y-m-d') !== $data['fecha_pago']) {
            $errors['fecha_pago'] = 'Selecciona una fecha valida.';
        }
    }

    if ($data['numero_factura'] !== '') {
        $stmt = $pdo->prepare(
            "SELECT id
             FROM pagos
             WHERE numero_factura = :factura
               AND id <> :id
             LIMIT 1"
        );
        $stmt->execute([
            ':factura' => $data['numero_factura'],
            ':id' => $paymentId,
        ]);
        if ($stmt->fetch()) {
            $errors['numero_factura'] = 'Ese numero de factura ya existe.';
        }
    }

    if (($data['status'] ?? 'Pagado') !== '' && !in_array($data['status'], payment_statuses(), true)) {
        $errors['status'] = 'Selecciona un estado valido.';
    }

    return $errors;
}

function normalize_money($amount): float
{
    if (is_string($amount)) {
        $amount = str_replace([',', '$', ' '], '', $amount);
    }

    return round((float) $amount, 2);
}

function save_payment(PDO $pdo, array $data): int
{
    $paymentId = (int) ($data['id'] ?? 0);
    $params = [
        ':socio_id' => (int) $data['socio_id'],
        ':concepto' => $data['concepto'],
        ':metodo_pago' => $data['metodo_pago'],
        ':monto' => normalize_money($data['monto']),
        ':status' => $data['status'] ?: 'Pagado',
        ':numero_factura' => $data['numero_factura'] !== '' ? $data['numero_factura'] : null,
        ':fecha_pago' => $data['fecha_pago'],
        ':notas' => $data['notas'],
    ];

    if ($paymentId > 0) {
        $stmt = $pdo->prepare(
            "UPDATE pagos
             SET socio_id = :socio_id,
                 concepto = :concepto,
                 metodo_pago = :metodo_pago,
                 monto = :monto,
                 status = :status,
                 numero_factura = :numero_factura,
                 fecha_pago = :fecha_pago,
                 notas = :notas
             WHERE id = :id"
        );
        $params[':id'] = $paymentId;
        $stmt->execute($params);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO pagos (
                socio_id, concepto, metodo_pago, monto, status, numero_factura, fecha_pago, notas
            ) VALUES (
                :socio_id, :concepto, :metodo_pago, :monto, :status, :numero_factura, :fecha_pago, :notas
            )"
        );
        $stmt->execute($params);
        $paymentId = (int) $pdo->lastInsertId();
    }

    $payment = find_payment_by_id($pdo, $paymentId);
    if ($payment) {
        log_activity(
            $pdo,
            $data['id'] !== '' ? 'pago_actualizado' : 'pago_registrado',
            ($data['id'] !== '' ? 'Pago actualizado para ' : 'Pago registrado para ')
                . $payment['socio_nombre']
                . ' por '
                . format_currency((float) $payment['monto']),
            'pago',
            $paymentId
        );
    }

    return $paymentId;
}

function delete_payment(PDO $pdo, int $paymentId): void
{
    $payment = find_payment_by_id($pdo, $paymentId);
    if (!$payment) {
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM pagos WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $paymentId]);

    log_activity(
        $pdo,
        'pago_eliminado',
        'Pago eliminado de ' . $payment['socio_nombre'] . ' por ' . format_currency((float) $payment['monto']),
        'pago',
        $paymentId
    );
}

function payment_receipt_filename(array $payment): string
{
    $base = trim((string) ($payment['numero_factura'] ?? '')) !== ''
        ? (string) $payment['numero_factura']
        : 'pago-' . (int) ($payment['id'] ?? 0);

    $base = preg_replace('/[^A-Za-z0-9\-_.]+/', '-', $base) ?: 'recibo';

    return 'recibo-' . $base . '.html';
}

function download_payment_receipt(array $payment): void
{
    $statusLabel = (string) ($payment['status'] ?? 'Pagado');
    $dateLabel = (new DateTimeImmutable((string) $payment['fecha_pago']))->format('d/m/Y');
    $invoiceLabel = trim((string) ($payment['numero_factura'] ?? '')) !== ''
        ? (string) $payment['numero_factura']
        : 'Sin factura';

    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . payment_receipt_filename($payment) . '"');

    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Recibo de pago</title>';
    echo '<style>
        body{font-family:Arial,sans-serif;background:#f8fafc;color:#0f172a;margin:0;padding:32px;}
        .sheet{max-width:820px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:40px;}
        .top{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;margin-bottom:32px;}
        .brand h1{margin:0;font-size:30px;}
        .brand p{margin:8px 0 0;color:#64748b;}
        .badge{display:inline-block;padding:8px 14px;border-radius:999px;background:#dcfce7;color:#15803d;font-weight:700;}
        .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-bottom:28px;}
        .item{padding:16px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc;}
        .item span{display:block;color:#64748b;font-size:13px;margin-bottom:8px;}
        .item strong{font-size:18px;}
        .notes{padding:18px;border-radius:14px;background:#eff6ff;border:1px solid #bfdbfe;margin-top:12px;}
        .footer{margin-top:28px;color:#64748b;font-size:14px;}
    </style></head><body><div class="sheet">';
    echo '<div class="top"><div class="brand"><h1>AsociaPro</h1><p>Comprobante de pago AMUVIE</p></div>';
    echo '<div><div class="badge">' . h($statusLabel) . '</div></div></div>';
    echo '<div class="grid">';
    echo '<div class="item"><span>Socio</span><strong>' . h((string) $payment['socio_nombre']) . '</strong></div>';
    echo '<div class="item"><span>Empresa</span><strong>' . h((string) $payment['socio_empresa']) . '</strong></div>';
    echo '<div class="item"><span>Concepto</span><strong>' . h((string) $payment['concepto']) . '</strong></div>';
    echo '<div class="item"><span>Monto</span><strong>' . h(format_currency((float) $payment['monto'])) . ' MXN</strong></div>';
    echo '<div class="item"><span>Metodo de pago</span><strong>' . h((string) $payment['metodo_pago']) . '</strong></div>';
    echo '<div class="item"><span>Fecha de pago</span><strong>' . h($dateLabel) . '</strong></div>';
    echo '<div class="item"><span>Factura</span><strong>' . h($invoiceLabel) . '</strong></div>';
    echo '<div class="item"><span>Correo del socio</span><strong>' . h((string) ($payment['socio_email'] ?? '')) . '</strong></div>';
    echo '</div>';

    if (trim((string) ($payment['notas'] ?? '')) !== '') {
        echo '<div class="notes"><strong>Notas</strong><p>' . nl2br(h((string) $payment['notas'])) . '</p></div>';
    }

    echo '<div class="footer">Generado el ' . h((new DateTimeImmutable())->format('d/m/Y H:i')) . '.</div>';
    echo '</div></body></html>';
    exit;
}

function export_payments_csv(array $payments): void
{
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="pagos.csv"');

    $output = fopen('php://output', 'wb');
    fwrite($output, chr(239) . chr(187) . chr(191));
    fputcsv($output, ['Fecha', 'Socio', 'Concepto', 'Metodo', 'Monto', 'Estado', 'Factura']);

    foreach ($payments as $payment) {
        fputcsv($output, [
            $payment['fecha_pago'],
            $payment['socio_nombre'],
            $payment['concepto'],
            $payment['metodo_pago'],
            normalize_money($payment['monto']),
            $payment['status'],
            $payment['numero_factura'] ?? '',
        ]);
    }

    fclose($output);
}

function has_uploaded_avatar(?array $avatarUpload): bool
{
    return is_array($avatarUpload)
        && ($avatarUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK
        && !empty($avatarUpload['tmp_name']);
}

function validate_avatar_upload(?array $avatarUpload): ?string
{
    if (!is_array($avatarUpload) || ($avatarUpload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($avatarUpload['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return 'No fue posible cargar la foto de perfil.';
    }

    if (($avatarUpload['size'] ?? 0) > 2 * 1024 * 1024) {
        return 'La foto de perfil no debe exceder 2 MB.';
    }

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $mimeType = mime_content_type($avatarUpload['tmp_name']);
    if (!isset($allowedMimeTypes[$mimeType])) {
        return 'La foto debe ser JPG, PNG, WEBP o GIF.';
    }

    return null;
}

function store_avatar_upload(array $avatarUpload): string
{
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $mimeType = mime_content_type($avatarUpload['tmp_name']);
    $extension = $allowedMimeTypes[$mimeType] ?? 'jpg';
    $directoryPath = avatar_upload_directory_path();
    if (!is_dir($directoryPath)) {
        mkdir($directoryPath, 0775, true);
    }

    $fileName = 'avatar-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
    $destination = $directoryPath . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($avatarUpload['tmp_name'], $destination)) {
        throw new RuntimeException('No fue posible guardar la foto de perfil.');
    }

    return avatar_upload_directory_url() . '/' . $fileName;
}

function avatar_upload_directory_path(): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
}

function avatar_upload_directory_url(): string
{
    return 'assets/uploads/avatars';
}

function delete_local_avatar_file(?string $avatarUrl): void
{
    if (!$avatarUrl || strncmp($avatarUrl, avatar_upload_directory_url() . '/', strlen(avatar_upload_directory_url()) + 1) !== 0) {
        return;
    }

    $filePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $avatarUrl);
    if (is_file($filePath)) {
        unlink($filePath);
    }
}
?>
