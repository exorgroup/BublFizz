<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * BublFiz Social
 * Description: Database configuration and connection management
 * Location: /backend/config/database.php
 */

/**
 * Database Configuration Class
 * Manages database connections and configuration settings
 */
class DatabaseConfig
{
    /**
     * Database configuration settings
     * @var array
     */
    private static $config = [
        'host' => 'localhost',
        'dbname' => 'bublFizz',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ];

    /**
     * PDO connection instance (singleton)
     * @var PDO|null
     */
    private static $connection = null;

    /**
     * Get database configuration from environment or defaults
     * 
     * @return array Database configuration array
     */
    public static function getConfig()
    {
        return [
            'host' => $_ENV['DB_HOST'] ?? self::$config['host'],
            'dbname' => $_ENV['DB_NAME'] ?? self::$config['dbname'],
            'username' => $_ENV['DB_USER'] ?? self::$config['username'],
            'password' => $_ENV['DB_PASS'] ?? self::$config['password'],
            'charset' => $_ENV['DB_CHARSET'] ?? self::$config['charset'],
            'options' => self::$config['options']
        ];
    }

    /**
     * Create and return PDO database connection
     * Uses singleton pattern to ensure single connection
     * 
     * @return PDO Database connection instance
     * @throws PDOException If connection fails
     */
    public static function getConnection()
    {
        if (self::$connection === null) {
            $config = self::getConfig();

            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

            try {
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );

                // Set timezone to UTC for consistency
                self::$connection->exec("SET time_zone = '+00:00'");
            } catch (PDOException $e) {
                throw new PDOException("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    /**
     * Test database connection
     * 
     * @return bool True if connection successful, false otherwise
     */
    public static function testConnection()
    {
        try {
            $pdo = self::getConnection();
            $pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Close database connection
     * 
     * @return void
     */
    public static function closeConnection()
    {
        self::$connection = null;
    }

    /**
     * Get database connection for migrations and CLI scripts
     * 
     * @return PDO Database connection instance
     */
    public static function getMigrationConnection()
    {
        return self::getConnection();
    }
}

// Global PDO instance for backward compatibility
try {
    $pdo = DatabaseConfig::getConnection();
} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "Database connection error: " . $e->getMessage() . "\n";
        exit(1);
    } else {
        // Log error and show generic message for web requests
        error_log("Database connection error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
}
