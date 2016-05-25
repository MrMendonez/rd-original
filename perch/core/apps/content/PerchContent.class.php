<?php

class PerchContent extends PerchApp
{
    protected $table     = 'content_regions';
	protected $pk        = 'regionID';
    
	private $registered = array();
	private $raw_content_cache = array();
	
	private $preview = false;
	
	private $tmp_url_vars = false;
	
	private $api;
	
	private $key_requests   = array();
	private $keys_reordered = array();
	private $new_keys_registered = false;
	
	private $pageID = false;
    
	public static function fetch()
	{	    
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}
	
	public function set_preview($contentID, $rev=false)
	{
	    $this->preview = true;
	    $this->preview_contentID = $contentID;
	    $this->preview_rev = $rev;
	}
	
	public function get($key=false)
	{
	    if ($key === false) return ' ';
	    
	    if ($this->cache === false) {
	        $this->_populate_cache_with_page_content();
	    }
	    
	    if (!in_array($key, $this->key_requests)) $this->key_requests[] = $key;
	    
	    $r = '';
	    
        if (isset($this->cache[$key])) {
            $r = $this->cache[$key];
        }else{
            $this->_register_new_key($key);
        }
        
        if ($this->new_keys_registered) {
            // re-order keys in light of the new key
            $this->_reorder_keys();
        }
        
        return $r;
	    
	}
	
	public function get_custom($key=false, $opts=false)
	{
	    if ($key === false) return ' ';
	    
	    if ($opts===false) return $this->get($key);
	    
	    if (isset($opts['page'])) {
	        $content_item = $this->get_content_raw($key, $opts['page']);
	    }else{
	        $content_item = $this->get_content_raw($key);
	    }


	    if (is_array($content_item) && isset($content_item['content'])) {
	        
            $content = PerchUtil::json_safe_decode($content_item['content'], true);
	        
	        // return blank string if no content
    	    if (!is_array($content)) return ' ';
	    }else{
	        return ' ';
	    }
	    
	    // trim empty items
	    $content = array_filter($content, "count");
	    

	    // find specific _id
	    if (isset($opts['_id'])) {
	        if (PerchUtil::count($content)) {
	            $out = array();
	            foreach($content as $item) {
	                if (isset($item['_id']) && $item['_id']==$opts['_id']) {
	                    $out[] = $item;
	                    break;
	                }
	            }
	            $content = $out;
	        }   
	    }else{
	        
	        // if not picking an _id, check for a filter
	        if (isset($opts['filter']) && isset($opts['value'])) {
	            if (PerchUtil::count($content)) {
    	            $out = array();
    	            $key = $opts['filter'];
    	            $val = $opts['value'];
    	            $match = isset($opts['match']) ? $opts['match'] : 'eq';
    	            foreach($content as $item) {
    	                if (isset($item[$key])) {
    	                    switch ($match) {
                                case 'eq': 
                                case 'is': 
                                case 'exact': 
                                    if ($item[$key]==$val) $out[] = $item;
                                    break;
                                case 'neq': 
                                case 'ne': 
                                case 'not': 
                                    if ($item[$key]!=$val) $out[] = $item;
                                    break;
                                case 'gt':
                                    if ($item[$key]>$val) $out[] = $item;
                                    break;
                                case 'gte':
                                    if ($item[$key]>=$val) $out[] = $item;
                                    break;
                                case 'lt':
                                    if ($item[$key]<$val) $out[] = $item;
                                    break;
                                case 'lte':
                                    if ($item[$key]<=$val) $out[] = $item;
                                    break;
                                case 'contains':
                                    $value = str_replace('/', '\/', $val);
                                    if (preg_match('/\b'.$value.'\b/i', $item[$key])) $out[] = $item;
                                    break;
                                case 'regex':
                                case 'regexp':
                                    if (preg_match($val, $item[$key])) $out[] = $item;
                                    break;
                                case 'between':
                                case 'betwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>trim($vals[0]) && $item[$key]<trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'eqbetween':
                                case 'eqbetwixt':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)==2) {
                                        if ($item[$key]>=trim($vals[0]) && $item[$key]<=trim($vals[1])) $out[] = $item;
                                    }
                                    break;
                                case 'in':
                                case 'within':
                                    $vals  = explode(',', $val);
                                    if (PerchUtil::count($vals)) {
                                        foreach($vals as $value) {
                                            if ($item[$key]==trim($value)) {
                                                $out[] = $item;
                                                break;
                                            }
                                        }
                                    }
                                    break;

    	                    }
    	                }
    	            }
    	            $content = $out;
    	        }
	        }
	    }
    
	    // sort
	    if (isset($opts['sort'])) {
	        if (isset($opts['sort-order']) && $opts['sort-order']=='DESC') {
	            $desc = true;
	        }else{
	            $desc = false;
	        }
	        $content = PerchUtil::array_sort($content, $opts['sort'], $desc);
	    }
    
	    if (isset($opts['sort-order']) && $opts['sort-order']=='RAND') {
            shuffle($content);
        }
    
        // Pagination
        if (isset($opts['paginate'])) {
            if (isset($opts['pagination_var'])) {
                $Paging = new PerchPaging($opts['pagination_var']);
            }else{
                $Paging = new PerchPaging();
            }
            
            $Paging->set_per_page(isset($opts['count'])?(int)$opts['count']:10);
            
            $opts['count'] = $Paging->per_page();
            $opts['start'] = $Paging->lower_bound()+1;
            
            $Paging->set_total(PerchUtil::count($content));
        }else{
            $Paging = false;
        }
                
        // limit
	    if (isset($opts['count']) || isset($opts['start'])) {

            // count
	        if (isset($opts['count'])) {
	            $count = (int) $opts['count'];
	        }else{
	            $count = PerchUtil::count($content);
	        }
            
	        // start
	        if (isset($opts['start'])) {
	            if ($opts['start'] === 'RAND') {
	                $start = rand(0, PerchUtil::count($content)-1);
	            }else{
	                $start = ((int) $opts['start'])-1; 
	            }
	        }else{
	            $start = 0;
	        }

	        // loop through
	        $out = array();
	        for($i=$start; $i<($start+$count); $i++) {
	            if (isset($content[$i])) {
	                $out[] = $content[$i];
	            }else{
	                break;
	            }
	        }
	        $content = $out;
	    }
        
    
	    
	    
        if (isset($opts['skip-template']) && $opts['skip-template']==true) {
            if (isset($opts['raw']) && $opts['raw']==true) {
                if (PerchUtil::count($content)) {
                    foreach($content as &$item) {
                        if (PerchUtil::count($item)) {
                            foreach($item as &$field) {
                                if (is_array($field) && isset($field['raw'])) {
                                    $field = $field['raw'];
                                }
                            }
                        }
                    }
                }
                return $content; 
            }
	    }
    
	    
	    // template
	    if (isset($opts['template'])) {
	        $template = $opts['template'];
	    }else{
	        $template = $content_item['regionTemplate'];
	    }
	    
	    $Template = new PerchTemplate('content/'.$template, 'content');
	    
	    if (!$Template->file) {
	        return 'The template <code>' . PerchUtil::html($template) . '</code> could not be found.';
	    }
	    
	    // post process
	    
        $tags   = $Template->find_all_tags('content');
        $processed_vars = array();
        $used_items = array();
        foreach($content as $item) {
            $tmp = $item;
            foreach($tags as $Tag) {
                if (isset($item[$Tag->id()])) {    
                    //$FieldType = PerchFieldTypes::get($Tag->type(), false, $Tag);
                    //$tmp[$Tag->id()] = $FieldType->get_processed($item[$Tag->id()]);
                    
                    $used_items[] = $item;
                }
            }
            if ($tmp) $processed_vars[] = $tmp;
        }
        
        
	    // Paging to template
        if (is_object($Paging) && $Paging->enabled()) {
            $paging_array = $Paging->to_array($opts);
            // merge in paging vars
	        foreach($processed_vars as &$item) {
	            foreach($paging_array as $key=>$val) {
	                $item[$key] = $val;
	            }
	        }
        }
	    
	    
	    if (isset($opts['skip-template']) && $opts['skip-template']==true) {
	        $out = array();

            if (PerchUtil::count($processed_vars)) {
                foreach($processed_vars as &$item) {
                    if (PerchUtil::count($item)) {
                        foreach($item as &$field) {
                            if (is_array($field) && isset($field['processed'])) {
                                $field = $field['processed'];
                            }
                        }
                    }
                }
            }

	        for($i=0; $i<PerchUtil::count($content); $i++) {
	            $out[] = array_merge($content[$i], $processed_vars[$i]);
	        }
            return $out;
	    }else{
            if (PerchUtil::count($processed_vars)) {
                $html = $Template->render_group($processed_vars, true);
            }else{
                $Template->use_noresults();
                $html = $Template->render(array());
            }

	        
	    }
	    
	    return $html;
	}
	

	
	public function search_content($key, $opts)
	{
	    PerchUtil::debug('Search term: '.$key);
	    
	    
	    $search_handlers = PerchSystem::get_registered_search_handlers();
	    
	    $out = array();

        if ($key!='') {
    	    if (!$this->api) {
    	        $this->api = new PerchAPI(1.0, 'content');
    	    }
    	    
    	    $encoded_key = str_replace('"', '', PerchUtil::json_safe_encode($key));
	    
    	    $Paging = $this->api->get('Paging');
	    
    	    if (isset($opts['count'])) {
    	        $Paging->set_per_page($opts['count']);
                if (isset($opts['start']) && $opts['start']!='') {
                    $Paging->set_start_position($opts['start']);
                }
    	    }else{
    	        $Paging->disable();
    	    }
	    
    	    // Proper query using FULLTEXT
    	    $sql = $Paging->select_sql(); 
    	            
    	    $sql .= '   "content" AS source, MATCH(ci.itemSearch) AGAINST('.$this->db->pdb($key).') AS score, 
    	            r.regionPage AS col1, r.regionHTML AS col2, ci.itemJSON AS col3, r.regionOptions AS col4, p.pageNavText AS col5, p.pageTitle AS col6, "" AS col7, "" AS col8
    	            FROM '.$this->table.' r, '.PERCH_DB_PREFIX.'content_items ci, '.PERCH_DB_PREFIX.'pages p
    	            WHERE r.regionID=ci.regionID AND r.regionRev=ci.itemRev AND r.pageID=p.pageID AND r.regionPage!=\'*\' AND r.regionSearchable=1 
    	                AND (MATCH(ci.itemSearch) AGAINST('.$this->db->pdb($key).') OR MATCH(ci.itemSearch) AGAINST('.$this->db->pdb($encoded_key).') )
    	                AND r.regionPage LIKE '.$this->db->pdb($opts['from-path'].'%').' ';
    	                
    	    if (PerchUtil::count($search_handlers)) {
    	        foreach($search_handlers as $handler) {	            
    	            //$handler_sql = $handler::get_search_sql($key);
    	            $handler_sql = call_user_func(array($handler, 'get_search_sql'), $key);
    	            if ($handler_sql) {
        	            $sql .= ' 
        	            UNION 
        	            '.$handler_sql.' ';
	                }
	                $handler_sql = false;
    	        }
    	    }
    	                
    	    $sql .= ' ORDER BY score DESC';
	            
            if ($Paging->enabled()) {
                $sql .= ' '.$Paging->limit_sql();
            }        
	            
    	    $rows = $this->db->get_rows($sql);
	    
    	    if (PerchUtil::count($rows)==0) {
	        
    	        // backup query using REGEXP
    	        $sql = $Paging->select_sql() . ' "content" AS source, 0-(LENGTH(r.regionPage)-LENGTH(REPLACE(r.regionPage, \'/\', \'\'))) AS score, 
    	                r.regionPage AS col1, r.regionHTML AS col2, ci.itemJSON AS col3, r.regionOptions AS col4, p.pageNavText AS col5, p.pageTitle AS col6, "" AS col7, "" AS col8
        	            FROM '.$this->table.' r, '.PERCH_DB_PREFIX.'content_items ci, '.PERCH_DB_PREFIX.'pages p
        	            WHERE r.regionID=ci.regionID AND r.regionRev=ci.itemRev AND r.pageID=p.pageID AND r.regionPage!=\'*\' AND r.regionSearchable=1 
        	                AND ci.itemSearch REGEXP '.$this->db->pdb('[[:<:]]'.$key.'[[:>:]]').' 
        	                AND r.regionPage LIKE '.$this->db->pdb($opts['from-path'].'%').' ';
        	                
	            if (PerchUtil::count($search_handlers)) {
        	        foreach($search_handlers as $handler) {
        	            $handler_sql = call_user_func(array($handler, 'get_backup_search_sql'), $key);
        	            if ($handler_sql) {
            	            $sql .= ' 
            	            UNION 
            	            '.$handler_sql.' ';
    	                }
    	                $handler_sql = false;
        	        }
        	    }
        	    
        	    $sql .= ' ORDER BY score ASC ';

                if ($Paging->enabled()) {
                    $sql .= ' '.$Paging->limit_sql();
                }        

        	    $rows = $this->db->get_rows($sql);
	        
    	    }
	    
	    
    	    if ($Paging->enabled()) {
    	        $Paging->set_total($this->db->get_count($Paging->total_count_sql()));
    	    }
	    	    
    	    if (PerchUtil::count($rows)) {
    	        foreach($rows as $row) {
    	            switch($row['source']) {
    	                    case 'content':
    	                        $out[] = $this->format_search_result($key, $opts, $row);
    	                        break;
    	                    default:
    	                        $className = $row['source'];
    	                        $out[] =  call_user_func(array($className, 'format_result'), $key, $opts, $row);
    	            }
    	            
    	        }
    	    }
    	}
    	
        if (isset($opts['skip-template']) && $opts['skip-template']) {
            return $out;
        }
        
        $Template = new PerchTemplate('search/'.$opts['template'], 'search');
        $Template->enable_encoding();
        
        if (PerchUtil::count($out)) {
            foreach($out as &$row) {
                if ($opts['hide-extensions'] && strpos($row['url'], '.')) {
                    $parts = explode('.', $row['url']);
                    $ext = array_pop($parts);
                    $query = '';
                    if (strpos($ext, '?')!==false) {
                        $qparts = explode('?', $ext);
                        array_shift($qparts);
                        if (PerchUtil::count($qparts)) {
                            $query = '?'.implode('?', $qparts);
                        }
                    }
                    $row['url'] = implode('.', $parts).$query;
                }
            }
            

            if (isset($Paging) && $Paging->enabled()) {
                $paging_array = $Paging->to_array();
                // merge in paging vars
    	        foreach($out as &$item) {
    	            foreach($paging_array as $key=>$val) {
    	                $item[$key] = $val;
    	            }
    	        }
            }
            
            return $Template->render_group($out, 1);
        }else{
            $Template->use_noresults();
            return $Template->render(array('key'=>$key));
        }
	}
	
	
	/**
	 * Load all content for page, and cache it.
	 *
	 * @return void
	 * @author Drew McLellan
	 */
	private function _populate_cache_with_page_content()
	{
	    
	    if ($this->preview) {
	        if ($this->preview_contentID != 'all') {
	            $this->cache = $this->get_content_latest_revision();
	        }else{
	            $this->cache = $this->get_content_latest_revision();
	        }
	    }else{
	        $this->cache = $this->_get_content();
	    }
	    
	}
	
	
	/**
	 * Get all content for the given page, or this page.
	 *
	 * @param string $page 
	 * @return void
	 * @author Drew McLellan
	 */
    private function _get_content($page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }
        
        $db     = PerchDB::fetch();
        
        $sql    = 'SELECT regionKey, regionHTML
                    FROM '.PERCH_DB_PREFIX.'content_regions
                    WHERE regionPage='.$db->pdb($page).' OR regionPage='.$db->pdb('*');
        $results    = $db->get_rows($sql);
        
        if (PerchUtil::count($results) > 0) {
            $out = array();
            foreach($results as $row) {
                if (!array_key_exists($row['regionKey'], $out)) {
                    $out[$row['regionKey']] = $row['regionHTML'];
                }
            }
            return $out;
        }else{
            return array();
        }
    }
    
    private function get_content_latest_revision($page=false)
    {
        $Perch  = Perch::fetch();
        
        if ($page===false) {
            $page   = $Perch->get_page();
        }        
        
        $Regions = new PerchContent_Regions;
        $regions = $Regions->get_for_page_path($page);
        
        if (PerchUtil::count($regions)) {
            $out  = array();
            foreach($regions as $Region) {
                $out[$Region->regionKey()] = $Region->render();
            }
            return $out;
        }else{
            return array();
        }
    }
	
	private function get_content_raw($key, $page=false)
	{
	    $Perch  = Perch::fetch();
	    
	    if ($page===false) {
	        $page   = $Perch->get_page();
	    }
	    
	    $cache_key = $page.':'.$key;
	    
	    if (array_key_exists($cache_key, $this->raw_content_cache)) {
	        return $this->raw_content_cache[$cache_key];
	    }else{
	        $db     = PerchDB::fetch();

    	    $sql    = 'SELECT regionID, regionTemplate';
    	    
    	    if ($this->preview){ 
    	        $sql .= ', regionLatestRev AS rev';
    	    }else{
    	        $sql .= ', regionRev AS rev';
    	    }
    	    
    	    $sql    .= ' FROM '.$this->table. '
    	                WHERE regionKey='.$db->pdb($key).' AND (regionPage='.$db->pdb($page).' OR regionPage='.$db->pdb('*') .')';
    	    $region    = $db->get_row($sql);
    	    
    	    if (PerchUtil::count($region)) {
    	        
    	        $sql = 'SELECT itemJSON FROM '.PERCH_DB_PREFIX.'content_items WHERE regionID='.$this->db->pdb($region['regionID']).' AND itemRev='.$this->db->pdb($region['rev']).' ORDER BY itemOrder ASC';
    	        $result = $this->db->get_rows($sql);
    	        
    	        if (PerchUtil::count($result)) {
    	            
    	            $content = array();
    	            foreach($result as $item) {
    	                if (trim($item['itemJSON'])!='') $content[] = $item['itemJSON'];
    	            }
    	            $region['content'] = '['.implode(',', $content).']';
    	            
        	        $this->raw_content_cache[$cache_key] = $region;
        	        return $region;
        	    }
    	    }

    	    
    	    
	    }
	    
	    return false;
	}
	
	/**
	 * Add a new key to the regions table
	 *
	 * @param string $key 
	 * @return void
	 * @author Drew McLellan
	 */
	private function _register_new_key($key)
	{
	    if (!isset($this->registered[$key])) {	    
    	    
    	    $Perch  = Perch::fetch();
    	    $page   = $Perch->get_page();
	    
    	    $data = array();
    	    $data['regionKey'] = $key;
    	    $data['regionPage'] = $page;
    	    $data['regionHTML'] = '<!-- Undefined content: '.PerchUtil::html($key).' -->';
    	    $data['regionOptions'] = '';
    	    $data['pageID'] = $this->_find_or_create_page($page);
	    
    	    $db = PerchDB::fetch();
    	    
    	    $cols	= array();
    		$vals	= array();

    		foreach($data as $key => $value) {
    			$cols[] = $key;
    			$vals[] = $db->pdb($value).' AS '.$key;
    		}

    		$sql = 'INSERT INTO ' . $this->table . '(' . implode(',', $cols) . ') 
    		        SELECT '.implode(',', $vals).' 
    		        FROM (SELECT 1) AS dtable
    		        WHERE (
    		                SELECT COUNT(*) 
    		                FROM '.$this->table.' 
    		                WHERE regionKey='.$db->pdb($data['regionKey']).' 
    		                    AND regionPage='.$db->pdb($data['regionPage']).'
    		                )=0
    		        LIMIT 1';
    		                
    		$db->execute($sql);
    	    
    	    $this->registered[$key] = true;
    	    $this->new_keys_registered = true;
    	}
	}
	
	
	/**
	 * Find the page by its path, or create it if it's new.
	 *
	 * @param string $path 
	 * @return void
	 * @author Drew McLellan
	 */
    private function _find_or_create_page($path)
    {
        if ($this->pageID) return $this->pageID;
        
        $db = PerchDB::fetch();
        $table = PERCH_DB_PREFIX.'pages';
        $sql = 'SELECT pageID FROM '.$table.' WHERE pagePath='.$db->pdb($path).' LIMIT 1';
        $pageID = $db->get_value($sql);
        
        if ($pageID) {
            $this->pageID = $pageID;
            return $pageID;
        }
        
        $data = array();
        $data['pagePath'] = $path;
        $data['pageTitle'] = PerchUtil::filename($path, false, false);
        $data['pageNavText'] = $data['pageTitle'];
        $data['pageNew'] = 1;
        
        return $db->insert($table, $data);
    }
	
	/**
	 * Reorder keys into source order
	 *
	 * @return void
	 * @author Drew McLellan
	 */
	private function _reorder_keys()
	{
	    if (PerchUtil::count($this->key_requests)) {
	        $Perch  = Perch::fetch();
    	    $page   = $Perch->get_page();
    	    $db = PerchDB::fetch();
    	    $i = 0;
    	    foreach($this->key_requests as $key) {
    	        if (!in_array($key, $this->keys_reordered)) {
    	            $sql = 'UPDATE '.$this->table.' SET regionOrder='.$i.' WHERE regionPage='.$db->pdb($page).' AND regionKey='.$db->pdb($key).' LIMIT 1';
        	        $db->execute($sql);
        	        $this->keys_reordered[] = $key;
    	        }
    	        $i++;
    	    }
	    }
	}
	
	// Used for custom searchURLs e.g. /example.php?id={_id}
	private function substitute_url_vars($matches)
	{
	    $url_vars = $this->tmp_url_vars;
    	if (isset($url_vars[$matches[1]])){
    		return $url_vars[$matches[1]];
    	}
	}

    private function format_search_result($key, $opts, $row)
    {
        $_contentPage = 'col1';
        $_contentHTML = 'col2';
        $_contentJSON = 'col3';
        $_contentOptions = 'col4';
        $_pageNavText     = 'col5';
        $_pageTitle     = 'col6';
        
        $this->mb_fallback();
        
        $lowerkey = strtolower($key);
        $json = PerchUtil::json_safe_decode($row[$_contentJSON], 1);
        if (PerchUtil::count($json)) {
            $item = $json;

            foreach($item as $subitem) {
                
                // maps and other complex data types
                if (is_array($subitem)) {
                    $subitem = implode(' ', $subitem);
                }
                
                $lowersubitem = strtolower($subitem);

                if (true || mb_stripos($lowersubitem, $lowerkey)!==false) { // doesn't match multi-word queries. I don't think that's a problem

                    $excerpt_chars = (int) $opts['excerpt-chars'];
                    $first_portion = floor(($excerpt_chars/4));

                    $match = array();
                    $match['url'] = $row[$_contentPage];
                
                    $regionOptions = PerchUtil::json_safe_decode($row[$_contentOptions]);
                    if ($regionOptions) {
                        if (isset($regionOptions->searchURL) && $regionOptions->searchURL!='') {
                            $match['url'] = $regionOptions->searchURL;
                            $this->tmp_url_vars = $item;
                            $match['url'] = preg_replace_callback('/{([A-Za-z0-9_\-]+)}/', array($this, "substitute_url_vars"), $match['url']);
                            $this->tmp_url_vars = false;
                        }
                    }
                
                    if (isset($item['_title'])) {
                        $match['title'] = $item['_title'];
                    }else{
                        $match['title'] = $row[$_pageNavText];
                    }
                    $html = strip_tags($row[$_contentHTML]);
                    $html = preg_replace('/\s{2,}/', ' ', $html);
                    $pos = mb_stripos($html, $key);
                    if ($pos<$first_portion){
                        $lower_bound = 0;
                    }else{
                        $lower_bound = $pos-$first_portion;
                    }
                
                    $html = mb_substr($html, $lower_bound, $excerpt_chars);
                
                    // trim broken works
                    $parts = explode(' ', $html);
                    array_pop($parts);
                    array_shift($parts);
                    $html = implode(' ', $parts);
                
                    // keyword highlight
                    $html = preg_replace('/('.preg_quote($key, '/').')/i', '<span class="keyword">$1</span>', $html);
                
                    $match['excerpt'] = $html;
                
                    $match['key'] = $key;
                    
                    $match['pageTitle'] = $row[$_pageTitle];
                    $match['pageNavText'] = $row[$_pageNavText];
                
                    return $match;
                
                }
            }
        }
        return false;
    }
    
    private function mb_fallback()
    {
        if (!function_exists('mb_stripos')) {
            function mb_stripos($a, $b) {
                return stripos($a, $b);
            }
        }
        
        if (!function_exists('mb_substr')) {
            function mb_substr($a, $b, $c) {
                return substr($a, $b, $c);
            }
        }
        
    }
	
}
?>