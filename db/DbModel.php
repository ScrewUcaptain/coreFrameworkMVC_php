<?php

namespace suc\phpmvc\db;

use suc\phpmvc\Model;
use suc\phpmvc\Application;

abstract class DbModel extends Model
{
    abstract public static function tableName() : string;

    abstract public function attribute(): array;
    
    abstract public  static function primaryKey(): string;

    

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attribute();

        $params = array_map( fn($at) => ":$at", $attributes);

        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ")
                        VALUES(". implode(',', $params) .");");

        foreach ($attributes as $attribute)
        {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }

        $statement->execute();
        return true;
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }

    public static function findOne($where) //where [email => azeaze@azezea.com, firstname => bob]
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        // SELECT * FROM $tableName WHERE email = :email AND firstname = :firstname
        $sql = implode('AND' ,array_map(fn($attr) => "$attr = :$attr", $attributes));
        // SELECT * FROM $tableName WHERE $sql
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach($where as $key => $val)
        {
            $statement->bindValue(":$key", $val);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
    }
}