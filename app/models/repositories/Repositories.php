<?php
namespace Models\Repositories;



abstract class Repositories
{
    public static function getRepository($name)
    {
        $className = "\\Models\\Repositories\\Repository\\{$name}";

        if (! class_exists($className)) {
            throw new Exceptions("Repository {$className} doesn't exists.");
        }
        
        return new $className();
    }
}
