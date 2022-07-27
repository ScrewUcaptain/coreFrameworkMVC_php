<?php

namespace suc\phpmvc\db;

use PDO;
use suc\phpmvc\Application;

class Database
{
    public PDO $pdo;

    public function __construct(array $config) // Enter the Database infos in your .env file
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // to see errors connection 
    }

    public function applyMigrations()
    {
        $this->createMigrationTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigration = [];

        $files = scandir(Application::$ROOT_DIR.'/migrations'); 
        $toAppliedMigrations = array_diff($files, $appliedMigrations);

        foreach($toAppliedMigrations as $migration)
        {
            if($migration === '.' || $migration === '..')
            {
                continue;
            }
            require_once Application::$ROOT_DIR.'/migrations/'.$migration;
            $className = pathinfo($migration,PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying migration $migration".PHP_EOL);
            $instance->up();
            $this->log("Applyied migration $migration".PHP_EOL);
            $newMigration[] = $migration;
        }

        if(!empty($newMigration))
        {
            $this->saveMigrations($newMigration);
        } else {
            $this->log("All migrations are applied");
        }

    }

    public function createMigrationTable() : void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )  ENGINE=INNODB;");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(',', array_map( fn($m) => "('$m')", $migrations)); // translate all migration file into ('nameMigration') for the sql statement

        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUE $str ");
        $statement->execute();
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    protected function log($message) // to echo a particular message like a log
    {
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }
}