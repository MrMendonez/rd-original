<?php
    if (version_compare(PHP_VERSION, '5.2.0', '<')) {
        die('Perch requires PHP 5.2. This server is running version ' . PHP_VERSION);
    }

    function perch_autoload($class_name) {
        if (strpos($class_name, 'PerchAPI')!==false) {
            $file = PERCH_CORE . '/lib/api/' . $class_name . '.class.php';
        }else{
            $file = PERCH_CORE . '/lib/' . $class_name . '.class.php';
        }
        
        
        if (is_readable($file)) {
            include $file;
            return true;
        }
        return false;
    }
    
    spl_autoload_register('perch_autoload');
        
    if (get_magic_quotes_runtime()) set_magic_quotes_runtime(false);
    
    date_default_timezone_set('UTC');

    // Essentials used on every request - no point autoloading
    include(PERCH_CORE.'/lib/PerchDB.class.php');
    include(PERCH_CORE.'/lib/PerchDB_MySQL.class.php');
    include(PERCH_CORE.'/lib/PerchUtil.class.php');
    include(PERCH_CORE.'/lib/Perch.class.php');

?>