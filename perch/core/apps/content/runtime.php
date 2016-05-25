<?php
    include(PERCH_CORE.'/apps/content/PerchContent_Regions.class.php');
    include(PERCH_CORE.'/apps/content/PerchContent_Region.class.php');
    include(PERCH_CORE.'/apps/content/PerchContent_Items.class.php');
    include(PERCH_CORE.'/apps/content/PerchContent_Item.class.php');
    include(PERCH_CORE.'/apps/content/PerchContent_Pages.class.php');
    include(PERCH_CORE.'/apps/content/PerchContent_Page.class.php');
    include(PERCH_CORE.'/apps/content/PerchContent.class.php');

    perch_content_check_preview();

    function perch_content($key=false, $return=false)
    {
        if ($key === false) {
            echo 'You must pass in a <em>name</em> for the content. e.g. <code style="color: navy;background: white;">&lt;' . '?php perch_content(\'Phone number\'); ?' . '&gt;</code>'; 
        }
        
        $Content = PerchContent::fetch();
        $out = $Content->get($key);
        
        // Post processing - if there are still <perch:x /> tags
        if (strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
		        
        if ($return) return $out;
        echo $out;

    }
    
    
    function perch_content_custom($key=false, $opts=false, $return=false)
    {
        if ($key === false) return ' ';

        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            $return  = true; 
            $postpro = false;
        }else{
            $postpro = true;
        }

        $Content = PerchContent::fetch();

        $out = $Content->get_custom($key, $opts);

        // Post processing - if there are still <perch:x /> tags
        if ($postpro && !is_array($out) && strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }

        if ($return) return $out;
        echo $out;
    }
    
    
    function perch_content_check_preview()
    {
        if (!defined('PERCH_PREVIEW_ARG')) define('PERCH_PREVIEW_ARG', 'preview');
        
        if (isset($_GET[PERCH_PREVIEW_ARG])) {

            if ($_GET[PERCH_PREVIEW_ARG] == 'all') {
                $contentID = 'all';
            }else{
                $contentID  = (int)$_GET[PERCH_PREVIEW_ARG];
            }
            
            $rev        = false;

            $Users          = new PerchUsers;
            $CurrentUser    = $Users->get_current_user();
            
            if (is_object($CurrentUser) && $CurrentUser->logged_in()) {
                $Content = PerchContent::fetch();
                $Content->set_preview($contentID, $rev);
            }
        }
    }
    
    
    function perch_content_search($key=false, $opts=false, $return=false)
    {   
        $key = trim(stripslashes($key));
        
        $Content = PerchContent::fetch();
        
        $defaults = array();
        $defaults['template']        = 'search-result.html';
        $defaults['count']           = 10;
        $defaults['excerpt-chars']   = 250;
        $defaults['from-path']       = '/';
        $defaults['hide-extensions'] = false;

        
        if (is_array($opts)) {
            $opts = array_merge($defaults, $opts);
        }else{
            $opts = $defaults;
        }

        if (isset($opts['hide_extensions']))    $opts['hide-extensions'] = $opts['hide_extensions'];
	    if (isset($opts['from_path']))          $opts['from-path'] = $opts['from_path'];
	    if (isset($opts['excerpt_chars']))      $opts['excerpt-chars'] = $opts['excerpt_chars'];
        
        
        $out = $Content->search_content($key, $opts);
        
        
        // Post processing - if there are still <perch:x /> tags
        if (strpos($out, '<perch:')!==false) {
            $Template   = new PerchTemplate();
            $out        = $Template->apply_runtime_post_processing($out);
        }
        
        if ($return) return $out;
        echo $out;
    }
    

    function perch_page_title($return=false) 
    {
        if ($return) return perch_pages_title(true);
        
        perch_pages_title();
    }

    function perch_pages_title($return=false)
    {
        $Pages = new PerchContent_Pages;
        $Perch = Perch::fetch();
        
        $Page = $Pages->find_by_path($Perch->get_page());
        
        $r = '';
        
        if (is_object($Page)) {
            $r = $Page->pageTitle();
        }
        
        if ($return) return $r;
        
        echo $r;
    }
    
    function perch_pages_navigation($opts=array(), $return=false)
    {
        $Pages = new PerchContent_Pages;
        $Perch = Perch::fetch();
        
        // translate renamed options from Perch v1
        if (isset($opts['from_path']))       $opts['from-path'] = $opts['from_path'];
        if (isset($opts['hide_extensions'])) $opts['hide-extensions'] = $opts['hide_extensions'];
        
        
        $default_opts = array(
            'from-path'=>'/',
            'levels'=>0,
            'hide-extensions'=>false,
            'hide-default-doc'=>true,
            'flat'=>false,
            'template'=>array('item.html'),
            'include-parent'=>false,
            'skip-template'=>false,
            'siblings'=>false,
            'only-expand-selected'=>false
        );
        
        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }
        
        if ($opts['skip-template']) $return = true;
        
        $current_page = $Perch->get_page();
        
        if ($opts['from-path']=='*') {
            $opts['from-path'] = $current_page;
        }
        
        $r = $Pages->get_navigation($opts, $current_page);
        
        if ($return) return $r;
        
        echo $r;
    }

    function perch_pages_breadcrumbs($opts=array(), $return=false)
    {
        $Pages = new PerchContent_Pages;
        $Perch = Perch::fetch();
        
        $default_opts = array(
            'hide-extensions'=>false,
            'hide-default-doc'=>true,
            'template'=>'breadcrumbs.html',
            'skip-template'=>false
        );
        
        if (is_array($opts)) {
            $opts = array_merge($default_opts, $opts);
        }else{
            $opts = $default_opts;
        }
        
        if ($opts['skip-template']) $return = true;
        
        $current_page = $Perch->get_page();

        $opts['from-path'] = $current_page;
        
        $r = $Pages->get_breadcrumbs($opts);
        
        if ($return) return $r;
        
        echo $r;
    }


    /**
     * Get an item from the querystring, or return the default if not found. Defaults to false.
     *
     * @param string $var 
     * @param string $default 
     * @return void
     * @author Drew McLellan
     */
    function perch_get($var, $default=false)
    {
        if (isset($_GET[$var]) && $_GET[$var]!='') {
            return $_GET[$var];
        }
        
        return $default;
    }

?>