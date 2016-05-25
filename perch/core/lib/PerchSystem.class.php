<?php

class PerchSystem
{
    private static $search_handlers = array();
    private static $template_vars   = array();
    
    public static function set_page($page)
    {
        $Perch = Perch::fetch();
        $Perch->set_page($page);
        $Content = PerchContent::fetch();
        $Content->clear_cache();
    }
    
    public static function register_search_handler($className)
    {
        self::$search_handlers[] = $className;
        return true;
    }
    
    public static function get_registered_search_handlers()
    {
        return self::$search_handlers;
    }
    
    public static function set_var($var, $value=false)
    {
        self::$template_vars[$var] = $value;
    }
    
    public static function unset_var($var)
    {
        if (isset(self::$template_vars[$var])) unset(self::$template_vars[$var]);
    }
    
    public static function set_vars($vars)
    {
        if (PerchUtil::count($vars)) {
            self::$template_vars = array_merge(self::$template_vars, $vars);
        }
    }
    
    public static function get_var($var)
    {
        if (isset(self::$template_vars[$var])) {
            return self::$template_vars[$var];
        }
        
        return false;
    }
    
    public static function get_vars()
    {
        return self::$template_vars;
    }
}


?>