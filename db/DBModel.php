<?php

namespace marklester\phpmvc\db;

use marklester\phpmvc\Model;
use marklester\phpmvc\Application;

// abstract methods attributes, tablename, primaryKey

abstract class DBModel extends Model
{
    public int $total_records;

    abstract public function tableName(): string;

    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    public static function prepare($SQLStatement)
    {
        return Application::$app->db->pdo->prepare($SQLStatement);
    }

    //INSERT to Database
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

     //Update 
     public function update($id)
     {
         $tableName = static::tableName();
         $attributes = $this->attributes();
         $params = implode(',', array_map(fn ($attr) => "$attr = :$attr", $attributes));
         $statement = self::prepare("UPDATE $tableName SET $params WHERE id=:id");
 
         foreach ($attributes as $attribute) {
             $statement->bindValue(":$attribute", $this->{$attribute});
         }
         $statement->bindValue(":id", $id);
 
 
         $statement->execute();
         return true;
     }
 
     //Select One from Database
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
 
 
     //Find all in database
     static public function findAll($where = [])
     {
         $tableName = static::tableName();
         if ($where) {
             $attributes = array_keys($where);
             $sql = implode('AND ', array_map(fn ($attr) => "$attr = :$attr", $attributes));
             $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
             foreach ($where as $key => $item) {
                 $statement->bindValue(":$key", $item);
             }
         }
         $statement = self::prepare("SELECT * FROM $tableName");
         $statement->execute();
         return $statement->fetchAll(\PDO::FETCH_OBJ);
     }
 
     public function deleted($id)
     {
         $tableName = static::tableName();
         $statement = self::prepare("DELETE FROM $tableName  WHERE id=:id");
         $statement->bindValue(":id", $id);
         $statement->execute();
         return true;
     }
}