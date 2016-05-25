<?php

class PerchContent_Pages extends PerchFactory
{
    protected $singular_classname = 'PerchContent_Page';
    protected $table    = 'pages';
    protected $pk   = 'pageID';

    protected $default_sort_column  = 'pageParentID, pageOrder';  
    
    private $error_messages = array();
    
    private $nav_page_cache = array();
    
    
    /**
     * Find a page based on its path, or create a new one. Used by the Upgrade app.
     * @param  string $path A root-relative site path
     * @return object       Instance of Page class.
     */
    public function find_or_create($path)
    {
        $Page = $this->find_by_path($path);
        
        if (is_object($Page)) return $Page;

        $data = array();
        $data['pagePath']       = $path;
        $data['pageTitle']      = PerchUtil::filename($path, false, false);
        $data['pageNavText']    = $data['pageTitle'];
        $data['pageNew']        = 1;
        
        $Page = $this->create($data);

        $this->order_new_pages();

        return $this->find($Page->id());
    }


    /**
     * Find the site path
     *
     * @return void
     * @author Drew McLellan
     */
    public function find_site_path()
    {
        // Find the site path
        if (!defined('PERCH_SITEPATH')) {
            $login_path_parts = explode('/', PERCH_LOGINPATH);
            $path_parts = explode(DIRECTORY_SEPARATOR, PERCH_PATH);
            foreach($login_path_parts as $part) if ($part!='') array_pop($path_parts);
            $path = implode(DIRECTORY_SEPARATOR, $path_parts);
            define('PERCH_SITEPATH', $path);
        }
        return PERCH_SITEPATH;
    }
    
    
    /**
     * Get the tree of pages
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_page_tree()
    {
        $sql = 'SELECT * FROM '.$this->table.'
                ORDER BY pageTreePosition ASC';
        $rows   = $this->db->get_rows($sql);
        
        return $this->return_instances($rows);
    }
    
    /**
     * Get the page tree, but filter out any items that don't have a parent ID matching those provided
     *
     * @param array $parentIDs 
     * @return void
     * @author Drew McLellan
     */
    public function get_page_tree_collapsed($parentIDs=array())
    {
        if (!PerchUtil::count($parentIDs)) {
            $parentIDs = array(0);
        }

        $sql = 'SELECT p.*, (SELECT COUNT(*) FROM '.$this->table.' WHERE pageParentID=p.pageID) AS subpages
                FROM '.$this->table.' p
                WHERE p.pageParentID IN ('.$this->db->implode_for_sql_in($parentIDs).')
                ORDER BY p.pageTreePosition ASC';
        $rows   = $this->db->get_rows($sql);
        
        return $this->return_instances($rows);
    }

	
	public function get_page_tree_filtered($type='new', $value=false)
	{
		switch($type) {
			
			case 'new':
				$sql = 'SELECT p.*, 1 AS pageDepth
		                FROM '.$this->table.' p
		                WHERE (SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'content_regions WHERE pageID=p.pageID AND regionNew=1) > 0
		                ORDER BY p.pageTreePosition ASC';
			
				break;
			
			case 'template':
				$sql = 'SELECT p.*, 1 AS pageDepth
		                FROM '.$this->table.' p
		                WHERE (SELECT COUNT(*) FROM '.PERCH_DB_PREFIX.'content_regions WHERE pageID=p.pageID AND regionTemplate='.$this->db->pdb($value).') > 0
		                ORDER BY p.pageTreePosition ASC';
			
				break;
			
			
		}
		
		
        $rows   = $this->db->get_rows($sql);
        
        return $this->return_instances($rows);
	}
    
    /**
     * Find the IDs of any child pages from the given page ID
     *
     * @param string $pageID 
     * @return void
     * @author Drew McLellan
     */
    public function find_child_page_ids($pageID)
    {
        $sql = 'SELECT pageTreePosition FROM '.$this->table.' WHERE pageID='.$this->db->pdb($pageID).' LIMIT 1';
        $pageTreePosition = $this->db->get_value($sql);
        
        if ($pageTreePosition) {
            $sql = 'SELECT pageID FROM '.$this->table.' WHERE pageTreePosition LIKE \''.$pageTreePosition.'-%\'';
            $rows = $this->db->get_rows($sql);
            
            if (PerchUtil::count($rows)) {
                $out = array();
                foreach($rows as $row) {
                    $out[] = $row['pageID'];
                }
                return $out;
            }
        }
        return array();
    }
    
    /**
     * Find the IDs of all ancestor pages
     *
     * @param string $pageID 
     * @return void
     * @author Drew McLellan
     */
    public function find_parent_page_ids($pageID)
    {
        $sql = 'SELECT pageTreePosition FROM '.$this->table.' WHERE pageID='.$this->db->pdb($pageID).' LIMIT 1';
        $pageTreePosition = $this->db->get_value($sql);
        
        if ($pageTreePosition) {
            $parts = explode('-', $pageTreePosition);
            $values = array();
            while(count($parts)) {
                $values[] = implode('-', $parts);
                array_pop($parts);
            }
            $sql = 'SELECT pageID FROM '.$this->table.' WHERE pageTreePosition IN ('.$this->db->implode_for_sql_in($values).')';
            $rows = $this->db->get_rows($sql);
            
            if (PerchUtil::count($rows)) {
                $out = array();
                foreach($rows as $row) {
                    $out[] = $row['pageID'];
                }
                return $out;
            }
        }
        
        return false;
    }

    /**
     * Find the IDs of all ancestor pages
     *
     * @param string $pageID 
     * @return void
     * @author Drew McLellan
     */
    public function find_parent_page_ids_by_path($pagePath)
    {
        $sql = 'SELECT pageTreePosition FROM '.$this->table.' WHERE pagePath='.$this->db->pdb($pagePath).' LIMIT 1';
        $pageTreePosition = $this->db->get_value($sql);
        
        if ($pageTreePosition) {
            $parts = explode('-', $pageTreePosition);
            $values = array();
            while(count($parts)) {
                $values[] = implode('-', $parts);
                array_pop($parts);
            }
            $sql = 'SELECT pageID FROM '.$this->table.' WHERE pageTreePosition IN ('.$this->db->implode_for_sql_in($values).')';
            $rows = $this->db->get_rows($sql);
            
            if (PerchUtil::count($rows)) {
                $out = array();
                foreach($rows as $row) {
                    $out[] = $row['pageID'];
                }
                return $out;
            }
        }
        
        return false;
    }
    
    
    /**
     * Get pages by the pageID of their parent.
     *
     * @param string $parentID 
     * @return void
     * @author Drew McLellan
     */
    public function get_by_parent($parentID=0)
    {
        $sql = 'SELECT * FROM '.$this->table.'
                WHERE pageParentID='.$this->db->pdb($parentID).'
                ORDER BY pageTreePosition ASC';
        $rows   = $this->db->get_rows($sql);
        
        return $this->return_instances($rows);
    }
    
    
    /**
     * Find a page based on its path
     *
     * @param string $path 
     * @return void
     * @author Drew McLellan
     */
    public function find_by_path($path)
    {
       $sql = 'SELECT * FROM '.$this->table.' WHERE pagePath='.$this->db->pdb($path).' LIMIT 1';
       $row   = $this->db->get_row($sql);

       return $this->return_instance($row);
    }
    
    
    /**
     * Get a page object for the fake page that represents shared items.
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_mock_shared_page()
    {
        $page = array();
        
        $page['pageID']           = '-1';       
        $page['pageParentID']     = '0'; 
        $page['pagePath']         = '*';     
        $page['pageTitle']        = PerchLang::get('Shared');    
        $page['pageNavText']      = PerchLang::get('Shared');  
        $page['pageNew']          = '0';      
        $page['pageOrder']        = '0';    
        $page['pageDepth']        = '1';    
        $page['pageSortPath']     = '/'; 
        $page['pageTreePosition'] = '000';       
        $page['pageSubpageRoles'] = '';       
        $page['pageSubpagePath']  = '';
        $page['pageHidden']       = '0';     
        $page['pageNavOnly']      = '0';    
        $page['subpages']         = false;    
        
        return $this->return_instance($page);        
        
    }
    
    
    
    /**
     * Find newly registered pages, and figure out their position in the tree
     *
     * @return void
     * @author Drew McLellan
     */
    public function order_new_pages($_count=1)
    {
        
        $sql = 'SELECT *, REPLACE(pagePath, '.$this->db->pdb('/'.PERCH_DEFAULT_DOC).', \'\') as sortPath FROM '.$this->table.'
                WHERE pageNew=1 ORDER BY LENGTH(sortPath)-LENGTH(REPLACE(sortPath, \'/\', \'\')) ASC';
        $rows   = $this->db->get_rows($sql);
        
        if (PerchUtil::count($rows)) {
            
            if ($_count>10) return;
            
            
            $pages = $this->return_instances($rows);
        
            foreach($pages as $Page) {
                $data = array();
                
                if (!$Page->pageDepth()) {
                    $depth = $Page->find_depth();
                    $data['pageDepth'] = $depth;
                }else{
                    $depth = (int)$Page->pageDepth();
                }
                
   
                $data['pageSortPath'] = PerchUtil::strip_file_extension($Page->sortPath());
                
                
                if (!$Page->pageParentID()) {
                    if ($depth==1) {
                        $data['pageParentID'] = 0;
                    }else{
                        // find parent
                    
                        $parts = explode('/', $Page->sortPath());
                        array_pop($parts);
                        $sections = array();
                        while(PerchUtil::count($parts)) {
                            $t = implode('/', $parts);
                            if ($t) $sections[] = $t;
                            array_pop($parts);
                        }
                    
                        PerchUtil::debug($Page->sortPath());
                        $sql = 'SELECT pageID, pageDepth, pageTreePosition FROM '.$this->table.' WHERE pageDepth<'.$depth.' AND pageNew=0 AND pageSortPath IN ('.$this->db->implode_for_sql_in($sections).')
                                ORDER BY LENGTH(pageSortPath)-LENGTH(REPLACE(pageSortPath, \'/\', \'\')) DESC LIMIT 1';
                        $parent = $this->db->get_row($sql);
                    
                        if ($parent) {
                            $data['pageParentID'] = $parent['pageID'];
                            $data['pageDepth'] = $parent['pageDepth']+1;
                            $depth = $data['pageDepth'];
                        }
                    }
                }else{
                    $data['pageParentID'] = $Page->pageParentID();
                    $sql = 'SELECT pageID, pageDepth, pageTreePosition FROM '.$this->table.' WHERE pageID='.$Page->pageParentID().' LIMIT 1';
                    $parent = $this->db->get_row($sql);
                }
                
                if (!isset($data['pageParentID'])) {
                    $data['pageParentID'] = $Page->pageParentID();
                    $sql = 'SELECT pageID, pageTreePosition FROM '.$this->table.' WHERE pageID='.$Page->pageParentID();
                    $parent = $this->db->get_row($sql);
                    
                    // no parent, so reset depth to show at top of tree.
                    $depth = 1;
                    $data['pageDepth'] = $depth;
                }
                
                if (isset($data['pageParentID'])) {
                    // order
                    $sql = 'SELECT COUNT(*) FROM '.$this->table.' WHERE pageNew=0 AND pageParentID='.$this->db->pdb($data['pageParentID']);
                    $data['pageOrder'] = $this->db->get_count($sql)+1;
                    
                                        
                    // Tree position
                    if ($data['pageParentID']==0) {
                        $data['pageTreePosition'] = '000-'.str_pad($data['pageOrder'], 3, '0', STR_PAD_LEFT);
                    }else{
                        $data['pageTreePosition'] = $parent['pageTreePosition'].'-'.str_pad($data['pageOrder'], 3, '0', STR_PAD_LEFT);
                    }
                                        
                    $data['pageNew'] = 0;
                    $Page->update($data);
                }
            }
            
            
            // recurse
            $this->order_new_pages($_count++);
        }
        
        return false;
    }
    
    
    /**
     * Create a new page, including adding a new file to the filesystem
     *
     * @param string $data 
     * @return void
     * @author Drew McLellan
     */
    public function create_with_file($data)
    {
        
        $this->find_site_path();
               
        // Grab the template this page uses
        $Templates = new PerchContent_PageTemplates;
        $Template  = $Templates->find($data['templateID']);
                
        if (is_object($Template)) {
            
            // we don't store this, so unset
            unset($data['templateID']);
            
            // grab the template's file extension, as pages use the same ext as the template.
            $file_extension = PerchUtil::file_extension($Template->templatePath());
            
            // use the file name given (if stated) or create from the title. Sans extension.
            if (isset($data['file_name'])) {
                $parts = explode('.', $data['file_name']);
                $file_name  = PerchUtil::urlify($parts[0]);
                unset($data['file_name']);
            }else{
                $file_name      = PerchUtil::urlify($data['pageTitle']);
            }
            
            // Find the parent page
            $ParentPage = $this->find($data['pageParentID']);

            if (is_object($ParentPage)) {
                if ($ParentPage->pageSubpagePath()) {
                    $pageSection = $ParentPage->pageSubpagePath();
                }else{
                    $pageSection = PerchUtil::strip_file_name($ParentPage->pagePath());
                }
                
                $parentPageID = $ParentPage->id();
                
            }else{
                $pageSection = '/';
                $parentPageID = 0;
            }
            
            
                            
            
            $dir            = PERCH_SITEPATH.str_replace('/', DIRECTORY_SEPARATOR, $pageSection);

            // Can we write to this dir?
            if (is_writable($dir)) {
                
                // Get a new file name
                $new_file = $this->get_unique_file_name($dir, $file_name, $file_extension);
                $template_dir = PerchUtil::file_path(PERCH_TEMPLATE_PATH.'/pages');

                if (file_exists($template_dir)) {
                    $template_file = PerchUtil::file_path($template_dir.'/'.$Template->templatePath());
                    
                    // Is this referenced or copied?
                    if ($Template->templateReference()) {
                        // Referenced, so write a PHP include
                        $contents = '<'.'?php include(\''.$this->get_relative_path($template_file, $dir).'\'); ?'.'>';
                    }else{
                        // Copied, so grab the template's contents
                        $contents = file_get_contents($template_file);
                    }
                                        
                    if ($contents) {
                        
                        // Write the file
                        if (!file_exists($new_file) && file_put_contents($new_file, $contents)) {
                            
                            // Get the new file path
                            $new_url = $pageSection.str_replace($dir, '', $new_file);
                            $r = str_replace(DIRECTORY_SEPARATOR, '/', $new_url);
                            $r = str_replace('//', '/', $r);
                            $data['pagePath'] = $r; 
                            
                            // Insert into the DB
                            $Page =  $this->create($data);
                            
                            // Set its position in the tree
                            $Page->update_tree_position($parentPageID);
                            
                            // Copy page options?
                            if ($Template->optionsPageID() != '0') {
                                
                                $CopyPage = $this->find($Template->optionsPageID());
                                
                                if (is_object($CopyPage)) {
                                
                                    $sql = 'INSERT INTO '.PERCH_DB_PREFIX.'content_regions (
                                            pageID,
                                            regionKey,
                                            regionPage, 
                                            regionHTML,
                                            regionNew,
                                            regionOrder,
                                            regionTemplate,
                                            regionMultiple,
                                            regionSearchable,
                                            regionEditRoles,
                                            regionOptions
                                        ) 
                                        SELECT
                                            '.$this->db->pdb($Page->id()).' AS pageID,
                                            regionKey,
                                            '.$this->db->pdb($r).' AS regionPage,
                                            "<!-- Undefined content -->" AS regionHTML,
                                            regionNew,
                                            regionOrder,
                                            regionTemplate,
                                            regionMultiple,
                                            regionSearchable,
                                            regionEditRoles,
                                            regionOptions
                                        FROM '.PERCH_DB_PREFIX.'content_regions
                                        WHERE regionPage!='.$this->db->pdb('*').' AND pageID='.$this->db->pdb($CopyPage->id());
                                
                                    $this->db->execute($sql);
                                
                                    // Nullify resources list in options
                                    $sql = 'SELECT regionID, regionOptions
                                            FROM '.PERCH_DB_PREFIX.'content_regions 
                                            WHERE pageID='.$this->db->pdb($Page->id());
                                    $rows = $this->db->get_rows($sql);
                                    if (PerchUtil::count($rows)) {
                                        foreach($rows as $row) {
                                            if (isset($row['contentOptions'])) {
                                                $jsonOptions = PerchUtil::json_safe_decode($row['contentOptions'], true);    
                                            }else{
                                                $jsonOptions = array();
                                            }
                                            
                                            $jsonOptions['resources'] = array();
                                            $data = array();
                                            $data['regionOptions'] = PerchUtil::json_safe_encode($jsonOptions);
                                            $this->db->update(PERCH_DB_PREFIX.'content_regions', $data, 'regionID', $row['regionID']);
                                        }
                                    }
                                
                                }
                            }
                            
                            return $Page;
                            
                        }else{
                            PerchUtil::debug('Could not put file contents.');
                            $this->error_messages[] = 'Could not write contents to file: '.$new_file;
                        }
                    }
                }else{
                    PerchUtil::debug('Template folder not found: '.$template_dir);
                    $this->error_messages[] = 'Template folder not found: '.$template_dir;
                }

            }else{
                PerchUtil::debug('Folder is not writable: '.$dir);
                $this->error_messages[] = 'Folder is not writable: '.$dir;
            }
            
            
            
        }else{
            PerchUtil::debug('Template not found.');
            PerchUtil::debug($data);
            $this->error_messages[] = 'Template could not be found.';
        }
        
        return false;
        
    }
    
    /**
     * Create a new page, either from an existing page, or just as a nav link
     *
     * @param string $data 
     * @return void
     * @author Drew McLellan
     */
    public function create_without_file($data)
    {
        if (isset($data['templateID'])) unset($data['templateID']);
                
        $link_only = false;     
        
        // is this a URL or just local file?
        if (isset($data['file_name'])) {
            $url = parse_url($data['file_name']);
            if ($url && is_array($url) && isset($url['scheme']) && $url['scheme']!='') {
                $link_only = true;
                $url = $data['file_name'];
                unset($data['file_name']);
            }
        }
        
        // Find the parent page
        $ParentPage = $this->find($data['pageParentID']);
        
        
        if ($link_only) {
            
            $data['pagePath'] = $url; 
            $data['pageNavOnly'] = '1';
            
            // Insert into the DB
            $Page =  $this->create($data);
            
            // Set its position in the tree
            if (is_object($Page)) {
                $Page->update_tree_position($ParentPage->id());
                return $Page;
            }
            
        }else{
            // use the file name given (if stated) or create from the title. Sans extension.
            if (isset($data['file_name'])) {
                $file_name  = $data['file_name'];
                unset($data['file_name']);
            }else{
                $file_name      = PerchUtil::urlify($data['pageTitle']);
            }
            
            $this->find_site_path();


            // Find the parent page
            $ParentPage = $this->find($data['pageParentID']);

            if (is_object($ParentPage)) {
                if ($ParentPage->pageSubpagePath()) {
                    $pageSection = $ParentPage->pageSubpagePath();
                }else{
                    $pageSection = PerchUtil::strip_file_name($ParentPage->pagePath());
                }
                
                $parentPageID = $ParentPage->id();
                
            }else{
                $pageSection = '/';
                $parentPageID = 0;
            }



            $dir            = PERCH_SITEPATH.str_replace('/', DIRECTORY_SEPARATOR, $pageSection);
            
            // Get the new file path
            $new_url = $pageSection.'/'.str_replace($dir, '', $file_name);
            $r = str_replace(DIRECTORY_SEPARATOR, '/', $new_url);
            $r = str_replace('//', '/', $r);
            $data['pagePath'] = $r; 
            
            // Insert into the DB
            $Page =  $this->create($data);
            
            // Set its position in the tree
            if (is_object($Page)) {
                $Page->update_tree_position($parentPageID);
                return $Page;
            }
            
        }
        
        
        return false;
        
    }
    
    private function get_unique_file_name($dir, $file_name, $file_extension, $count=0)
    {
        if ($count==0) {
            $file = $dir.DIRECTORY_SEPARATOR.$file_name.'.'.$file_extension;
        }else{
            $file = $dir.DIRECTORY_SEPARATOR.$file_name.'-'.$count.'.'.$file_extension;
        }
     
        $file = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);
        
        if (file_exists($file)) {
            $count++;
            return $this->get_unique_file_name($dir, $file_name, $file_extension, $count);
        }else{
            return $file;
        }
    }
    
    // Thanks, jpic in php.net realpath comments!
    private function get_relative_path($path, $compareTo) 
    {
        // clean arguments by removing trailing and prefixing slashes
        if ( substr( $path, -1 ) == DIRECTORY_SEPARATOR ) {
            $path = substr( $path, 0, -1 );
        }
        if ( substr( $path, 0, 1 ) == DIRECTORY_SEPARATOR ) {
            $path = substr( $path, 1 );
        }

        if ( substr( $compareTo, -1 ) == DIRECTORY_SEPARATOR ) {
            $compareTo = substr( $compareTo, 0, -1 );
        }
        if ( substr( $compareTo, 0, 1 ) == DIRECTORY_SEPARATOR ) {
            $compareTo = substr( $compareTo, 1 );
        }

        // simple case: $compareTo is in $path
        if ( strpos( $path, $compareTo ) === 0 ) {
            $offset = strlen( $compareTo ) + 1;
            return substr( $path, $offset );
        }

        $relative  = array(  );
        $pathParts = explode( DIRECTORY_SEPARATOR, $path );
        $compareToParts = explode( DIRECTORY_SEPARATOR, $compareTo );

        foreach( $compareToParts as $index => $part ) {
            if ( isset( $pathParts[$index] ) && $pathParts[$index] == $part ) {
                continue;
            }

            $relative[] = '..';
        }

        foreach( $pathParts as $index => $part ) {
            if ( isset( $compareToParts[$index] ) && $compareToParts[$index] == $part ) {
                continue;
            }

            $relative[] = $part;
        }

        return implode( DIRECTORY_SEPARATOR, $relative );
    }
    
    
    public function get_errors()
    {
        return $this->error_messages;
    }
    
    
    public function get_breadcrumbs($opts)
    {
        $from_path        = $opts['from-path'];
        $hide_extensions  = $opts['hide-extensions'];
        $hide_default_doc = $opts['hide-default-doc'];
        $template         = $opts['template'];
        $skip_template    = $opts['skip-template'];
        
        $template = 'navigation/'.$template;
        
        
        $from_path = rtrim($from_path, '/');
        
        $sql = 'SELECT pageTreePosition FROM '.$this->table.' WHERE pagePath='.$this->db->pdb($from_path).' OR pageSortPath='.$this->db->pdb($from_path).' LIMIT 1';
        $pageTreePosition = $this->db->get_value($sql);
    
        if ($pageTreePosition) {
            $parts = explode('-', $pageTreePosition);
            $values = array();
            while(count($parts)) {
                $values[] = implode('-', $parts);
                array_pop($parts);
            }
            
            $sql = 'SELECT * FROM '.$this->table.' WHERE pageHidden=0 AND pageNew=0 AND pageTreePosition IN ('.$this->db->implode_for_sql_in($values).') ORDER BY pageTreePosition';
            $rows = $this->db->get_rows($sql);
            
            
            if (PerchUtil::count($rows)) {
                foreach($rows as &$page) {
                
                    // hide default doc
                    if ($hide_default_doc) {
                        $page['pagePath'] = preg_replace('/'.preg_quote(PERCH_DEFAULT_DOC).'$/', '', $page['pagePath']);
                    }
                
                    // hide extensions
                    if ($hide_extensions) {
                        $page['pagePath'] = preg_replace('/'.preg_quote(PERCH_DEFAULT_EXT).'$/', '', $page['pagePath']);
                    }
                
                }
            }
            
            
            
            if ($skip_template) {
                return $rows;
            }
            
            $Template = new PerchTemplate($template, 'pages');
            return $Template->render_group($rows, true);
        }

        return '';
    }

    
    public function get_navigation($opts, $current_page)
    {
        $from_path             = $opts['from-path'];
        $levels                = $opts['levels'];
        $hide_extensions       = $opts['hide-extensions'];
        $hide_default_doc      = $opts['hide-default-doc'];
        $flat                  = $opts['flat'];
        $templates             = $opts['template'];
        $include_parent        = $opts['include-parent'];
        $skip_template         = $opts['skip-template'];
        $siblings              = $opts['siblings'];
        $only_expand_selected  = $opts['only-expand-selected'];

        
        if (!is_array($templates)) {
            $templates = array($templates);
        }
        
        foreach($templates as &$template) {
            $template = 'navigation/'.$template;
        }
        
        
        // from path
        if ($from_path && $from_path != '/') {
            
            $from_path = rtrim($from_path, '/');
            
            $sql = 'SELECT pageID, pageParentID, pageDepth, pageTreePosition FROM '.$this->table.' WHERE pagePath='.$this->db->pdb($from_path).' OR pageSortPath='.$this->db->pdb($from_path).' LIMIT 1';
            $root = $this->db->get_row($sql);

            if ($siblings) {
                // show siblings, so we actually want to select the parent page
                $sql = 'SELECT pageID, pageParentID, pageDepth, pageTreePosition FROM '.$this->table.' WHERE pageID='.$this->db->pdb($root['pageParentID']).' LIMIT 1';
                $root = $this->db->get_row($sql);
            }

            
            $min_level = (int)$root['pageDepth'];
            $max_level = $min_level + $levels;
                    
        }else{
            $root = false;
            
            $min_level = 0;
            $max_level = $min_level + $levels;
        }

                
    
        // cache page list
        $sql = 'SELECT * FROM '.$this->table.' WHERE pageHidden=0 AND pageNew=0 ';
        
        // if from path is set
        if ($root) {
            $sql .= ' AND pageTreePosition LIKE '.$this->db->pdb($root['pageTreePosition'].'%').' ';
        }
        
        // levels
        if ($levels) {
            $sql .= ' AND pageDepth >='.$min_level.' AND pageDepth<='.$max_level.' ';
        }
        
        
        $sql .= ' ORDER BY pageTreePosition ASC';
        $this->nav_page_cache = $this->db->get_rows($sql);
        
        
        if (PerchUtil::count($this->nav_page_cache)) {

            $selected_ids = array();
            
            $ext_length = strlen(PERCH_DEFAULT_EXT);

            // select the tree
            $selected_ids = $this->find_parent_page_ids_by_path($current_page);

            // find current page
            foreach($this->nav_page_cache as &$page) {
                if ($page['pagePath']==$current_page) {
                    
                    if (is_array($selected_ids)) {
                        $selected_ids[] = $page['pageID'];
                    }else{
                        $selected_ids = array($page['pageID']);
                    }
                                       
                }
                
                // hide default doc
                if ($hide_default_doc) {
                    $page['pagePath'] = preg_replace('/'.preg_quote(PERCH_DEFAULT_DOC).'$/', '', $page['pagePath']);
                }
                
                // hide extensions
                if ($hide_extensions) {
                    $page['pagePath'] = preg_replace('/'.preg_quote(PERCH_DEFAULT_EXT).'$/', '', $page['pagePath']);
                }
                
            }
        

            if ($flat) {
                
                // Template them all flat.
                
                $rows = $this->nav_page_cache;
                foreach($rows as &$row) {
                    if (in_array($row['pageID'], $selected_ids)) {
                        $row['current_page'] = true;
                    }
                }
                
                if ($skip_template) {
                    return $rows;
                }
                
                $Template = new PerchTemplate($templates[0], 'pages');
                return $Template->render_group($rows, true);
                
            }else{
                
                // Template nested
                
                if ($root) {
                    if ($include_parent) {
                        $parentID = $root['pageParentID'];
                    }else{
                        $parentID = $root['pageID'];
                    }
                    
                }else{
                    $parentID = 0;
                }
                
                if ($skip_template) $templates = false;
                
                return $this->_template_nav($templates, $selected_ids, $parentID, $level=0, $skip_template, $only_expand_selected);
            }

            
        }
        
        
        
        return '';
    }
    
    
    private function _template_nav($templates, $selected_ids, $parentID=0, $level=0, $Template=false, $only_expand_selected=false)
    {
        $rows = array();
        foreach($this->nav_page_cache as $page) {
            if ($page['pageParentID']==$parentID) {
                $rows[] = $page;
            }
        }
        
        if (PerchUtil::count($rows)) {
            
            if ($templates) {
                if (isset($templates[$level])) {
                    $template = $templates[$level];
                }else{
                    $template = $templates[count($templates)-1];
                }
            
                if ($Template==false || $Template->current_file!=$template) {
                    $Template = new PerchTemplate($template, 'pages');
                }
                
            }
            
            foreach($rows as &$row) {

                if ($only_expand_selected) {
                    if (in_array($row['pageID'], $selected_ids)) {
                        $row['current_page'] = true;
                        $row['subitems'] = $this->_template_nav($templates, $selected_ids, $row['pageID'], $level+1, $Template, $only_expand_selected);
                    }
                }else{
                    $row['subitems'] = $this->_template_nav($templates, $selected_ids, $row['pageID'], $level+1, $Template, $only_expand_selected);
                    if (in_array($row['pageID'], $selected_ids)) {
                        $row['current_page'] = true;
                    }
                }
                
            }
            
            if ($templates) {
                return $Template->render_group($rows, true);
            }
            
            return $rows;
            
        }
        
        return '';
    }
}



?>