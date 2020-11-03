<?php

namespace marklester\phpmvc\db;

use marklester\phpmvc\Model;
use marklester\phpmvc\Application;

// abstract methods attributes, tablename, primaryKey

abstract class DBModel extends Model
{
    abstract public function tableName(): string;

    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn ($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ") VALUES(" . implode(",", $params) . ")");

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    static public function findOne($where) //['email'=>hello@email.com,'firstname'=>Hello]
    {
        //static tells the actual class on which finOne will be called
        $tableName = static::tableName();
        $attributes = array_keys($where);

        //this will result to this SQL statement 
        //"SELECT * FROM $tableName WHERE email = :email AND firstname = :firstname"
        $sql = implode('AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));

        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");

        foreach ($where as $key => $item) {
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();

        //will return an instance of a class where the findOne is called - User
        return $statement->fetchObject(static::class);
    }

    public static function prepare($SQLStatement)
    {
        return Application::$app->db->pdo->prepare($SQLStatement);
    }
}
