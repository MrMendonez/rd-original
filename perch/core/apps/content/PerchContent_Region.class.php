<?php

class PerchContent_Region extends PerchBase
{
    protected $table  = 'content_regions';
    protected $pk     = 'regionID';

    private $options  = false;
    private $current_userID = false;


    /**
     * Get a flat array of items
     *
     * @param string $item_id 
     * @return void
     * @author Drew McLellan
     */
    public function get_items_for_editing($item_id=false)
    {
        $Items = new PerchContent_Items;
        return $Items->get_flat_for_region($this->id(), $this->regionLatestRev(), $item_id);
    }

    /**
     * Get item object instances for doing an update
     *
     * @param string $item_id 
     * @return void
     * @author Drew McLellan
     */
    public function get_items_for_updating($item_id=false)
    {
        return $this->get_items($item_id);
    }
    
    /**
     * Get items in region
     *
     * @param string $item_id 
     * @return void
     * @author Drew McLellan
     */
    public function get_items($item_id=false)
    {
        $Items = new PerchContent_Items;
        return $Items->get_for_region($this->id(), $this->regionLatestRev(), $item_id);
    }

	/**
	 * Get a count of the number of items for this rev of the region
	 *
	 * @return void
	 * @author Drew McLellan
	 */
	public function get_item_count()
	{
		$Items = new PerchContent_Items;
		return $Items->get_count_for_region($this->id(), $this->regionLatestRev());
	}

    
    /**
     * Set the current userID. Stored against edits.
     *
     * @param string $userID 
     * @return void
     * @author Drew McLellan
     */
    public function set_current_user($userID)
    {
        $this->current_userID = $userID;
    }
    
    /**
     * Are there items in the history stack to undo?
     *
     * @return void
     * @author Drew McLellan
     */
    public function is_undoable()
    {
        // TODO
        return true;
    }

    
    /**
     * Does the region have a newer draft than the published version?
     *
     * @return void
     * @author Drew McLellan
     */
    public function has_draft()
    {
        return ((int)$this->regionLatestRev() > (int)$this->regionRev());
    }
    
    /**
     * Does the given roleID have permission to edit this region?
     *
     * @param string $roleID 
     * @return void
     * @author Drew McLellan
     */
    public function role_may_edit($User)
    {

        if ($User->roleMasterAdmin()) return true;

        $roleID = $User->roleID();

        $str_roles = $this->regionEditRoles();
    
        if ($str_roles=='*') return true;
        
        $roles = explode(',', $str_roles);

        return in_array($roleID, $roles);
    }

    /**
     * Does the current role have permission to even see this region?
     * @param  obj $User     User object
     * @param  obj $Settings Settings object
     * @return bool           View or not
     */
    public function role_may_view($User, $Settings)
    {
        if ($this->role_may_edit($User)) return true;

        if ($Settings->get('content_hideNonEditableRegions')->val()) return false;

        return true;
    }
    
    /**
     * Get region options
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_options()
    {
        if (is_array($this->options)) return $this->options;
        $arr = PerchUtil::json_safe_decode($this->regionOptions(), true);
        if (!is_array($arr)) $arr = array();
        $this->options = $arr;
        return $arr;
    }
    
    /**
     * Get an option by key
     *
     * @param string $optKey 
     * @return void
     * @author Drew McLellan
     */
    public function get_option($optKey)
    {
        $options = $this->get_options();
        if (array_key_exists($optKey, $options)) {
            $opt = $options[$optKey];
            if ($opt === 'false') return false;
            return $opt;
        }
        return false;
    }
    
    /**
     * Set region options
     *
     * @param string $options 
     * @return void
     * @author Drew McLellan
     */
    public function set_options($options)
    {
        $existing = $this->get_options();
        if (!is_array($existing)) $existing = array();
        
        $opts = array_merge($existing, $options);
        
        $data = array();
        $data['regionOptions'] = PerchUtil::json_safe_encode($opts);
        $this->update($data);
        
        // clear cache
        $this->options = false;
    }
    
    /**
     * Set a single option
     *
     * @param string $optKey 
     * @param string $val 
     * @return void
     * @author Drew McLellan
     */
    public function set_option($optKey, $val)
    {
        return $this->set_options(array($optKey=>$val));
    }
    
    
    /**
     * Add a new, empty item to the region
     *
     * @return void
     * @author Drew McLellan
     */
    public function add_new_item()
    {
        //$this->create_new_revision();
        
        $new_item   = array(
            'itemID'=>$this->_get_next_item_id(),
            'regionID'=>$this->id(),
            'pageID'=>$this->pageID(),
            'itemRev'=>$this->regionLatestRev(),
            'itemJSON'=>'',
            'itemSearch'=>''
        );
        
        if ($this->get_option('addToTop')==true) {
            $new_item['itemOrder'] = $this->get_lowest_item_order()-1;
        }else{
            $new_item['itemOrder'] = $this->get_highest_item_order()+1;
        }

        $Items = new PerchContent_Items();
        $Item = $Items->create($new_item);
        
        return $Item;
    }
    
    
    /**
     * Delete an item. If the current revision is not a draft, publish the region too.
     *
     * @param string $itemID 
     * @return void
     * @author Drew McLellan
     */
    public function delete_item($itemID)
    {
        $is_draft   = $this->has_draft();
        
        $new_rev    = $this->create_new_revision();
        
        $Items = new PerchContent_Items();
        
        $Item = $Items->find_item($this->id(), $itemID, $new_rev);
        
        $Item->delete();
        
        if (!$is_draft) {
            $this->publish($new_rev);
        }
        
        return true;
        
    }
    
    
    /**
     * Delete items, leaving only x items in the region. Used for converting multi-item to single item. Undoable.
     *
     * @param string $resulting_item_count 
     * @return void
     * @author Drew McLellan
     */
    public function truncate($resulting_item_count=1)
    {
        $new_rev = $this->create_new_revision();
        $Items = new PerchContent_Items();
        $Items->truncate_for_region($this->id(), $new_rev, $resulting_item_count);
    }
    
    /**
     * Make a region into a shared region
     *
     * @return void
     * @author Drew McLellan
     */
    public function make_shared()
    {    
        $data = array();
    	$data['regionPage'] = '*';
    	$this->update($data);
    	
    	$Regions = new PerchContent_Regions;
    	$Regions->delete_with_key($this->regionKey(), true);
    }
    
    /**
     * Unshare the region, reverting to original page where possible
     *
     * @return void
     * @author Drew McLellan
     */
    public function make_not_shared()
    {        
        $Pages = new PerchContent_Pages;
        $Page = $Pages->find($this->pageID());
        
        if (is_object($Page)) {
            $data = array();
            $data['regionPage'] = $Page->pagePath();
            $this->update($data);
            
            return true;
        }
        
        return false;
    }

    
    /**
     * Duplicate all content items to create a new revision
     *
     * @return void
     * @author Drew McLellan
     */
    public function create_new_revision()
    {
        $old_rev = (int) $this->regionLatestRev();
        $new_rev = $old_rev+1;
        
        $Items = new PerchContent_Items();
        $Items->create_new_revision($this->id(), $old_rev, $new_rev);
        
        
        $data = array();
        $data['regionLatestRev'] = $new_rev;
        
        // if this is a new region
        if ($new_rev==1) {
            $data['regionRev'] = $new_rev;
        }
        
        $this->update($data);
        
        return $new_rev;
    }
    
    /**
     * Reorder the items in the region based on the sortField option.
     *
     * @return void
     * @author Drew McLellan
     */
    public function sort_items()
    {
        $sortField = $this->get_option('sortField');
        
        // Sort order
        if ($sortField && $sortField!='') {
            
            $sortOrder = $this->get_option('sortOrder');
            
            $desc = false;
            if ($sortOrder && strtoupper($sortOrder)=='DESC') {
                $desc = true;
            }
            
            $Items = new PerchContent_Items();
            
            $Items->sort_for_region($this->id(), $this->regionLatestRev(), $sortField, $desc);        
        }
    }
    
    /**
     * Generate HTML for region, make current revision non-draft.
     *
     * @return void
     * @author Drew McLellan
     */
    public function publish($rev=false)
    {
        if ($rev===false) $rev = $this->regionLatestRev();
        
        $html = $this->render($rev);
        
        $data = array();
        $data['regionHTML']      = $html;
        $data['regionRev']       = $rev;
        $data['regionLatestRev'] = $rev;
        
        $this->update($data);
    }
    
    /**
     * Render the output HTML for the given revision (or latest if not specified)
     *
     * @param string $rev 
     * @return void
     * @author Drew McLellan
     */
    public function render($rev=false)
    {
        if ($rev===false) $rev = $this->regionLatestRev();
        
        // get limit
        $limit = false;

        $set_limit = (int)$this->get_option('limit');
        if ($set_limit>0) {
            $limit = $set_limit;
        }


        $Items = new PerchContent_Items();
        $vars  = $Items->get_flat_for_region($this->id(), $rev, false, $limit);
        
        $Template = new PerchTemplate('content/'.$this->regionTemplate(), 'content');
        
        return $Template->render_group($vars, true);
    }

    /**
     * An undo
     *
     * @return void
     * @author Drew McLellan
     */
    public function revert_most_recent()
    {
        $undo_rev = $this->regionLatestRev();
        
        $Items = new PerchContent_Items();
        $prev_rev = $Items->get_previous_revision_number($this->id(), $undo_rev);
        
        if ($prev_rev) {
            $this->publish($prev_rev);
        
            $Items->delete_revision($this->id(), $undo_rev);
            
            return true;
        }
        
        return false;
    }
    
    public function get_lowest_item_order()
    {
        $Items = new PerchContent_Items();
        return $Items->get_order_bound($this->id(), $this->regionLatestRev(), true);
    }
    
    public function get_highest_item_order()
    {
        $Items = new PerchContent_Items();
        return $Items->get_order_bound($this->id(), $this->regionLatestRev(), false);
    }
    
    private function _get_next_item_id()
    {
        $Items = new PerchContent_Items();
        return $Items->get_next_id();
    }

}

?>