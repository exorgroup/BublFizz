<?php
/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * BublFiz Social
 * Description: Database migration runner for managing schema changes
 * Location: /database/migrate.php
 */

/**
 * Database Migration Runner
 * Handles execution and tracking of database schema migrations
 */
class Migrator
{
    /**
     * PDO database connection instance
     * @var PDO
     */
    private $pdo;

    /**
     * Migration tracking table name
     * @var string
     */
    private $migrationTable = 'migrations';

    /**
     * Initialize migrator with database connection
     * 
     * @param PDO $pdo Database connection instance
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->createMigrationTable();
    }

    /**
     * Execute all pending migrations in sequential order
     * 
     * @return void
     * @throws Exception If migration file is invalid or execution fails
     */
    public function run()
    {
        $files = glob(__DIR__ . '/migrations/*.php');
        sort($files); // Ensure sequential execution
        
        foreach ($files as $file) {
            $migration = basename($file, '.php');
            
            if (!$this->hasRun($migration)) {
                $this->executeMigration($file, $migration);
                echo "Migrated: $migration\n";
            }
        }
        
        echo "All migrations completed.\n";
    }

    /**
     * Execute a single migration file
     * 
     * @param string $file Full path to migration file
     * @param string $migration Migration identifier
     * @return void
     * @throws Exception If migration execution fails
     */
    private function executeMigration($file, $migration)
    {
        try {
            $this->pdo->beginTransaction();
            
            // Execute migration file
            include $file;
            
            // Mark as completed
            $this->markAsRun($migration);
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Migration failed: $migration - " . $e->getMessage());
        }
    }

    /**
     * Check if a migration has already been executed
     * 
     * @param string $migration Migration identifier
     * @return bool True if migration has been run, false otherwise
     */
    private function hasRun($migration)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->migrationTable} WHERE migration = ?");
        $stmt->execute([$migration]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Mark a migration as completed in tracking table
     * 
     * @param string $migration Migration identifier
     * @return void
     */
    private function markAsRun($migration)
    {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->migrationTable} (migration, executed_at) VALUES (?, NOW())");
        $stmt->execute([$migration]);
    }

    /**
     * Create migration tracking table if it doesn't exist
     * 
     * @return void
     */
    private function createMigrationTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->pdo->exec($sql);
    }

    /**
     * Rollback the last executed migration
     * 
     * @return void
     * @throws Exception If no migrations to rollback or rollback fails
     */
    public function rollback()
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM {$this->migrationTable} ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $lastMigration = $stmt->fetchColumn();
        
        if (!$lastMigration) {
            throw new Exception("No migrations to rollback");
        }
        
        // Look for rollback file
        $rollbackFile = __DIR__ . "/rollbacks/{$lastMigration}.php";
        
        if (file_exists($rollbackFile)) {
            try {
                $this->pdo->beginTransaction();
                include $rollbackFile;
                
                // Remove from tracking table
                $stmt = $this->pdo->prepare("DELETE FROM {$this->migrationTable} WHERE migration = ?");
                $stmt->execute([$lastMigration]);
                
                $this->pdo->commit();
                echo "Rolled back: $lastMigration\n";
            } catch (Exception $e) {
                $this->pdo->rollBack();
                throw new Exception("Rollback failed: " . $e->getMessage());
            }
        } else {
            throw new Exception("No rollback file found for: $lastMigration");
        }
    }

    /**
     * Get list of executed migrations
     * 
     * @return array Array of migration names with execution timestamps
     */
    public function getExecutedMigrations()
    {
        $stmt = $this->pdo->query("SELECT migration, executed_at FROM {$this->migrationTable} ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    require_once __DIR__ . '/../backend/config/database.php';
    
    try {
        $migrator = new Migrator($pdo);
        
        $command = $argv[1] ?? 'run';
        
        switch ($command) {
            case 'run':
                $migrator->run();
                break;
            case 'rollback':
                $migrator->rollback();
                break;
            case 'status':
                $migrations = $migrator->getExecutedMigrations();
                foreach ($migrations as $migration) {
                    echo "{$migration['migration']} - {$migration['executed_at']}\n";
                }
                break;
            default:
                echo "Usage: php migrate.php [run|rollback|status]\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>