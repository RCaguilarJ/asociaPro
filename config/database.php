<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'asociapro';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $serverConnection = new PDO(
                'mysql:host=' . $this->host,
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $serverConnection->exec('SET NAMES utf8mb4');
            $serverConnection->exec(
                'CREATE DATABASE IF NOT EXISTS ' . $this->db_name . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
            );

            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4',
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $exception) {
            throw new RuntimeException('Error de conexion a la base de datos: ' . $exception->getMessage(), 0, $exception);
        }

        return $this->conn;
    }
}
?>
