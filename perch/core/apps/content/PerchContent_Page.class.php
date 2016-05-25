<?php

class PerchContent_Page extends PerchBase
{
    protected $table  = 'pages';
    protected $pk     = 'pageID';


    /**
     * Calculate the depth of the current page
     *
     * @return void
     * @author Drew McLellan
     */
    public function find_depth()
    {
        $path = str_replace('/'.PERCH_DEFAULT_DOC, '', $this->pagePath());
        
        if ($path=='') return 1;
        
        return substr_count($path, '/');
    }
    
    /**
     * Update the page's position in the tree
     *
     * @param string $parentID 
     * @param string $order 
     * @return void
     * @author Drew McLellan
     */
    public function update_tree_position($parentID, $order=false, $cascade=false)
    {
        PerchUtil::debug('updating tree position');
        
        $Pages = new PerchContent_Pages;
        $ParentPage = $Pages->find($parentID);
        
        $data = array();
        $data['pageParentID'] = $parentID;
        
        if ($order===false) {
            if (is_object($ParentPage)) {
                $data['pageOrder'] = $ParentPage->find_next_child_order();
            }else{
                $data['pageOrder'] = $this->find_next_child_order(0);
            }
            
        }else{
            $data['pageOrder'] = $order;
        }
        
        
        
        if (is_object($ParentPage)) {
            $data['pageDepth'] = ($ParentPage->pageDepth()+1);
            $data['pageTreePosition'] = $ParentPage->pageTreePosition().'-'.str_pad($data['pageOrder'], 3, '0', STR_PAD_LEFT);
        }else{
            PerchUtil::debug('Could not find parent page');
            $data['pageDepth'] = 1;
            $data['pageTreePosition'] = '000-'.str_pad($data['pageOrder'], 3, '0', STR_PAD_LEFT);
        }
        
        
        $this->update($data);
        
        if ($cascade) {
            $child_pages = $Pages->get_by('pageParentID', $this->id());
            if (PerchUtil::count($child_pages)) {
                foreach($child_pages as $ChildPage) {
                    $ChildPage->update_tree_position($this->id());
                }
            }
        }
        

    }
    
    /**
     * Find the next pageOrder value for subpages of the current page.
     *
     * @return void
     * @author Drew McLellan
     */
    public function find_next_child_order($parentID=false)
    {
        if ($parentID===false) {
            $parentID = $this->id();
        }
        
        $sql = 'SELECT MAX(pageOrder) FROM '.$this->table.' WHERE pageParentID='.$this->db->pdb($parentID);
        $max = $this->db->get_count($sql);
        
        return $max+1;
    }
    
    
    /**
     * Does the given roleID have permission to create a subpage?
     *
     * @param string $roleID 
     * @return void
     * @author Drew McLellan
     */
    public function role_may_create_subpages($User)
    {
        if ($User->roleMasterAdmin()) return true;

        $roleID = $User->roleID();

        $str_roles = $this->pageSubpageRoles();
    
        if ($str_roles=='*') return true;
        
        $roles = explode(',', $str_roles);

        return in_array($roleID, $roles);
    }

    /**
     * Delete the page, along with its file
     * @return nothing
     */
    public function delete()
    {
        $Pages = new PerchContent_Pages;

        $site_path = $Pages->find_site_path();

        $file = PerchUtil::file_path($site_path.'/'.$this->pagePath());
        if (!$this->pageNavOnly() && file_exists($file)) {
            if (defined('PERCH_DONT_DELETE_FILES') && PERCH_DONT_DELETE_FILES==true) {
                // don't delete files!
            }else{
                unlink($file);   
            } 
        }
        return parent::delete();
    }

}

?>