<?php
class ObjLoader{
    // A collection of already loaded objects.
    private static $collection = array();
    
    /* Return the instance of a class registered on collection.
     * If the class isn't registered yet, create a new instance of that, register it on collection, then return it.
     */
    protected static function load($path, $classname, $args){
        if(isset(self::$collection[$path])){
            return self::$collection[$path];
        }
        
        if(filesize($path) === 0 || !file_exists($path)){
            System::debug(array("Class file does not exists or is empty."));
            return null;
        }
        
        require_once $path;
        
        $r = new ReflectionClass(ucfirst($classname));
        
        self::$collection[$path] = $r->newInstanceArgs($args);;
        
        return self::$collection[$path];
    }
        
}