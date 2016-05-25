<?php

class PerchContent_Items extends PerchFactory
{
    protected $singular_classname = 'PerchContent_Item';
    protected $table    = 'content_items';
    protected $pk   = 'itemRowID';

    protected $default_sort_column  = 'itemOrder';  

    
    /**
     * Find an item by region, ID and revision
     *
     * @param string $regionID 
     * @param string $itemID 
     * @param string $rev 
     * @return void
     * @author Drew McLellan
     */
    public function find_item($regionID, $itemID, $rev)
    {
        $sql = 'SELECT * FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($rev).' AND itemID='.$this->db->pdb($itemID);
        
        $row =  $this->db->get_row($sql);
        
        return $this->return_instance($row);
    }


    /**
     * Get a flat array of the items (or single item) in a region, for the edit form
     *
     * @param string $regionID 
     * @param string $rev 
     * @param string $item_id 
     * @return void
     * @author Drew McLellan
     */
    public function get_flat_for_region($regionID, $rev, $item_id=false, $limit=false)
    {
        $sql = 'SELECT * FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($rev);
                
        if ($item_id!==false) {
            $sql .= ' AND itemID='.$this->db->pdb($item_id);
        }
        
        $sql .= ' ORDER BY itemOrder ASC';

        if ($limit!==false) {
            $sql .= ' LIMIT '.intval($limit);
        }
        
        $rows =  $this->db->get_rows($sql);
        
        if (PerchUtil::count($rows)) {
            foreach($rows as &$row) {
                $fields = PerchUtil::json_safe_decode($row['itemJSON'], true);
                if (is_array($fields)) $row = array_merge($fields, $row);
            }
        }
    
        return $rows;
    }
    
    /**
     * Get a items (or single item) in a region, for the edit form update process
     *
     * @param string $regionID 
     * @param string $rev 
     * @param string $item_id 
     * @return void
     * @author Drew McLellan
     */
    public function get_for_region($regionID, $rev, $item_id=false)
    {
        $sql = 'SELECT * FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($rev);
                
        if ($item_id!==false) {
            $sql .= ' AND itemID='.$this->db->pdb($item_id);
        }
        
        $sql .= ' ORDER BY itemOrder ASC';
        
        return $this->return_instances($this->db->get_rows($sql));
    }
    
	
	/**
	 * Get a count of the number of items in the region for the given revision
	 *
	 * @param string $regionID 
	 * @param string $rev 
	 * @return void
	 * @author Drew McLellan
	 */
	public function get_count_for_region($regionID, $rev)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->table.'
                WHERE itemJSON!=\'\' AND regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($rev);
        
        return $this->db->get_count($sql);
	}

    
    /**
     * Duplicate all the region items with a new revision number
     *
     * @param string $regionID 
     * @param string $old_rev 
     * @param string $new_rev 
     * @return void
     * @author Drew McLellan
     */
    public function create_new_revision($regionID, $old_rev, $new_rev)
    {
        $sql = 'INSERT INTO '.$this->table.' (itemID, regionID, pageID, itemRev, itemOrder, itemJSON, itemSearch)
                    SELECT itemID, regionID, pageID, '.$this->db->pdb($new_rev).' AS itemRev, itemOrder, itemJSON, itemSearch
                    FROM '.$this->table.'
                    WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($old_rev).'
                    ORDER BY itemOrder ASC';
        $this->db->execute($sql);
        
        $this->renumber_items($regionID, $new_rev);
    }
    
    /**
     * Renumber the sort order for items, to keep them tidy.
     *
     * @param string $regionID 
     * @param string $rev 
     * @return void
     * @author Drew McLellan
     */
    public function renumber_items($regionID, $rev)
    {
        $sql = 'SELECT itemRowID FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($rev).'
                ORDER BY itemOrder ASC';
        $rows = $this->db->get_rows($sql);
        
        
        
        if (PerchUtil::count($rows)) {
            $i = 0;
            foreach($rows as $row) {
                $data = array();
                $data['itemOrder'] = 1000 + $i;
                $this->db->update($this->table, $data, 'itemRowID', $row['itemRowID']);
                $i++;
            }
        }
    }
    
    /**
     * Get the highest or lowest item order index for the region. Default is highest.
     *
     * @param string $regionID 
     * @param string $rev 
     * @param string $lowest 
     * @return void
     * @author Drew McLellan
     */
    public function get_order_bound($regionID, $rev, $lowest=false)
    {
        $sql = 'SELECT itemOrder FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.$this->db->pdb($rev);
                
        if ($lowest) {
            $sql .= ' ORDER BY itemOrder ASC ';
        }else{
            $sql .= ' ORDER BY itemOrder DESC ';
        }
        
        $sql .= ' LIMIT 1 ';
        
        $val = (int) $this->db->get_value($sql);
        
        if ($val==0) $val = 999;
        
        return $val;
    }
    
    /**
     * Reorder the items within a region by the given field
     *
     * @param string $regionID 
     * @param string $rev 
     * @param string $field 
     * @param string $desc 
     * @return void
     * @author Drew McLellan
     */
    public function sort_for_region($regionID, $rev, $field, $desc=false)
    {
        $items = $this->get_flat_for_region($regionID, $rev);
        
        if (PerchUtil::count($items)) {
            $sorted = PerchUtil::array_sort($items, $field, $desc);
            
            $i = 0;
            
            foreach($sorted as $item) {
                $sql = 'UPDATE '.$this->table.' SET itemOrder='.(1000+$i).' WHERE itemID='.$this->db->pdb($item['itemID']).' AND itemRev='.$this->db->pdb($rev).' LIMIT 1';
                $this->db->execute($sql);
                $i++;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the previous stored revision number for the given region
     *
     * @param string $regionID 
     * @param string $rev 
     * @return void
     * @author Drew McLellan
     */
    public function get_previous_revision_number($regionID, $rev)
    {
        $sql = 'SELECT itemRev FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev<'.(int)$rev.'
                ORDER BY itemRev DESC LIMIT 1';
        return $this->db->get_value($sql);
    }
    
    
    /**
     * Delete all the items for the given region and revision
     *
     * @param string $regionID 
     * @param string $rev 
     * @return void
     * @author Drew McLellan
     */
    public function delete_revision($regionID, $rev)
    {
        $sql = 'DELETE FROM '.$this->table.'
                WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.(int)$rev;
        return $this->db->execute($sql);
    }
    
    /**
     * Remove all but x items from the region.
     *
     * @param string $regionID 
     * @param string $rev 
     * @param string $resulting_item_count 
     * @return void
     * @author Drew McLellan
     */
    public function truncate_for_region($regionID, $rev, $resulting_item_count=1)
    {
        $sql = 'DELETE FROM '.$this->table.'
                WHERE itemRowID IN 
                    (SELECT itemRowID FROM 
                        (SELECT itemRowID FROM '.$this->table.'
                        WHERE regionID='.$this->db->pdb($regionID).' AND itemRev='.(int)$rev.'
                        ORDER BY itemOrder ASC
                        LIMIT '.$resulting_item_count.', 99999999 
                        ) AS t2
                    )';
        return $this->db->execute($sql);
    }
    
    
    /**
     * Get the next itemID
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_next_id()
    {
        $sql = 'SELECT MAX(itemID)+1 FROM '.$this->table;
        $r = $this->db->get_count($sql);
        
        if ($r==0) $r++;
        
        return $r;
    }

}

?>