<?php

class PerchDB {
	
	static private $instance;
	
	public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = 'PerchDB_MySQL';
            self::$instance = new $c;
        }

        return self::$instance;
	}
	
}
?>