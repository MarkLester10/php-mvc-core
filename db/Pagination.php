<?php

namespace marklester\phpmvc\db;

use marklester\phpmvc\Application;

class Pagination
{
    private $tableName;
    private $limit;
    private $total_records;
    private $where;

    public function __construct($tableName, $limit, $where = [])
    {
        $this->tableName = $tableName;
        $this->limit = $limit;
        $this->where = $where;
        $this->set_total_records();
    }

    public function findAndPaginate()
    {
        $start = 0;
        if ($this->current_page() > 1) {
            $start = ($this->current_page() * $this->limit) - $this->limit;
        }
        $stmt = self::prepare("SELECT * FROM $this->tableName LIMIT $start, $this->limit");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function current_page()
    {
        return isset($_GET['page']) ? (int)$_GET['page'] : 1;
    }

    public function set_total_records()
    {
        if ($this->where) {
            $attributes = array_keys($this->where);

            $sql = implode('AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));
            $statement = self::prepare("SELECT * FROM $this->tableName WHERE $sql");
            foreach ($this->where as $key => $item) {
                $statement->bindValue(":$key", $item);
            }
        }
        $statement = self::prepare("SELECT * FROM $this->tableName");
        $statement->execute();
        $this->total_records = $statement->rowCount();
    }

    public function get_pagination_number()
    {
        return ceil($this->total_records / $this->limit);
    }

    public static function prepare($SQLStatement)
    {
        return Application::$app->db->pdo->prepare($SQLStatement);
    }
}