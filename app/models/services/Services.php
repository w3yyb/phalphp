<?php
namespace Models\Services;


abstract class Services
{
    public static function getService($name)
    {
        $className = "\\Models\\Services\\Service\\{$name}";

        if (! class_exists($className)) {
            throw new Exceptions("Class {$className} doesn't exists.");
        }
        
        return new $className();
    }
}
