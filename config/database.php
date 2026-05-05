<?php

class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    private $socket;
    private $autoCreateDatabase;
    public $conn;

    public function __construct()
    {
        $databaseUrl = $this->env('DATABASE_URL', $this->env('MYSQL_URL', ''));
        $parsedUrl = $databaseUrl !== '' ? $this->parseDatabaseUrl($databaseUrl) : [];

        $this->host = $parsedUrl['host'] ?? $this->env('DB_HOST', $this->env('MYSQL_HOST', '127.0.0.1'));
        $this->port = $parsedUrl['port'] ?? $this->env('DB_PORT', $this->env('MYSQL_PORT', '3306'));
        $this->db_name = $parsedUrl['db_name'] ?? $this->env('DB_NAME', $this->env('MYSQL_DATABASE', 'asociapro'));
        $this->username = $parsedUrl['username'] ?? $this->env('DB_USER', $this->env('MYSQL_USER', 'root'));
        $this->password = $parsedUrl['password'] ?? $this->env('DB_PASS', $this->env('MYSQL_PASSWORD', ''));
        $this->socket = $this->env('DB_SOCKET', $this->env('MYSQL_UNIX_PORT', ''));
        $this->autoCreateDatabase = $this->shouldAutoCreateDatabase($databaseUrl);
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            if ($this->autoCreateDatabase) {
                $serverConnection = new PDO(
                    $this->buildServerDsn(),
                    $this->username,
                    $this->password,
                    $options
                );
                $serverConnection->exec('SET NAMES utf8mb4');
                $serverConnection->exec(
                    'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '', $this->db_name) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
                );
            }

            $this->conn = new PDO(
                $this->buildDatabaseDsn(),
                $this->username,
                $this->password,
                $options
            );
            $this->conn->exec('SET NAMES utf8mb4');
        } catch (PDOException $exception) {
            throw new RuntimeException(
                'Error de conexion a la base de datos: ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        return $this->conn;
    }

    private function buildServerDsn(): string
    {
        if ($this->socket !== '') {
            return 'mysql:unix_socket=' . $this->socket . ';charset=utf8mb4';
        }

        return 'mysql:host=' . $this->host . ';port=' . $this->port . ';charset=utf8mb4';
    }

    private function buildDatabaseDsn(): string
    {
        if ($this->socket !== '') {
            return 'mysql:unix_socket=' . $this->socket . ';dbname=' . $this->db_name . ';charset=utf8mb4';
        }

        return 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db_name . ';charset=utf8mb4';
    }

    private function env(string $key, string $default = ''): string
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        $trimmed = trim((string) $value);
        return $trimmed !== '' ? $trimmed : $default;
    }

    private function parseDatabaseUrl(string $databaseUrl): array
    {
        $parts = parse_url($databaseUrl);
        if ($parts === false) {
            return [];
        }

        return [
            'host' => (string) ($parts['host'] ?? ''),
            'port' => isset($parts['port']) ? (string) $parts['port'] : '',
            'db_name' => isset($parts['path']) ? ltrim((string) $parts['path'], '/') : '',
            'username' => isset($parts['user']) ? urldecode((string) $parts['user']) : '',
            'password' => isset($parts['pass']) ? urldecode((string) $parts['pass']) : '',
        ];
    }

    private function shouldAutoCreateDatabase(string $databaseUrl): bool
    {
        $override = strtolower($this->env('DB_AUTO_CREATE', ''));
        if ($override !== '') {
            return in_array($override, ['1', 'true', 'yes', 'on'], true);
        }

        if ($databaseUrl !== '' || $this->env('VERCEL', '') === '1') {
            return false;
        }

        return true;
    }
}
?>
