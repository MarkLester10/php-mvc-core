<?php
//Singleton Pattern applied here

namespace marklester\phpmvc\db;

use marklester\phpmvc\Application;

class Database
{
    public \PDO $pdo;

    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $newMigrations = [];
        $this->createMigrationsTable();

        $appliedMigrations = $this->getAppliedMigrations();

        //gives the list of the migration files inside the directory migrations
        $files = scandir(Application::$ROOT_DIR . '/migrations');

        //From the files this will remove the already applied migration files and return an array of the new migration files that was not applied
        $toApplyMigrations = array_diff($files, $appliedMigrations);

        foreach ($toApplyMigrations as $migration) {
            if ($migration === '.' || $migration === "..") {
                continue;
            }

            require_once Application::$ROOT_DIR . "/migrations/$migration";

            //get the name of migration file without the extension
            $className = pathinfo($migration, PATHINFO_FILENAME);
            //create instance of the not applied migrations
            $instance = new $className();
            $this->log("Applying Migration: $migration");
            $instance->up();
            $this->log("Migrated Successfully: $migration ");
            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("Keep Hacking! All Migrations Applied");
        }
    }

    public function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR (255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB");
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        //fetch single migration column value as a single dimentional array
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $migrationsArr = array_map(fn ($m) => "('$m')", $migrations);
        $migrations = implode(",", $migrationsArr);
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $migrations");
        $statement->execute();
    }

    public function prepare($SQLStatement)
    {
        return $this->pdo->prepare($SQLStatement);
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
