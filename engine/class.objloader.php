<?php

class ObjLoader {

    // A collection of already loaded objects.
    private static $collection = array();

    /* Returns the instance of a class registered on collection.
     * If the class isn't registered yet, create a new instance of that, register it on collection, then returns it.
     */

    public static function loadObject($path, $classname, $args = array()) {
        if (isset(self::$collection[$path])) {
            return self::$collection[$path];
        }

        try {
            require_once $path;
            $r = new ReflectionClass(ucfirst($classname));
            self::$collection[$path] = $r->newInstanceArgs($args);
            return self::$collection[$path];
        } catch (Exception $ex) {
            System::log("sys_error","Error Message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }
    }

}
