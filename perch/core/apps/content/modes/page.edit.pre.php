<?php

    $Pages = new PerchContent_Pages;
    $Page  = false;
    
    // Find the page
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];
        $Page = $Pages->find($id);
    }
    
    // Check we have a page
    if (!$Page || !is_object($Page)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
    }
    
    // Check permissions
    if (!$CurrentUser->has_priv('content.pages.edit')) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
    }

    
    $Form = new PerchForm('editpage');

    $req = array();
    $req['pageTitle']   = "Required";
    $req['pageNavText'] = "Required";
    $req['pagePath']    = "Required";

    $Form->set_required($req);
    
    if ($Form->posted() && $Form->validate()) {
    	$postvars = array('pageTitle', 'pageNavText', 'pagePath', 'pageSubpagePath', 'pageHidden');
    	$data = $Form->receive($postvars);
    	
    	if (!isset($data['pageHidden'])) $data['pageHidden'] = '0';
    	
    	$data['pageSubpagePath'] = '/'.ltrim($data['pageSubpagePath'], '/');
    	$_POST['pageSubpagePath'] = $data['pageSubpagePath'];

    	
    	if (isset($_POST['subpage_roles']) && PerchUtil::count($_POST['subpage_roles'])) {
    	    $roles = $_POST['subpage_roles'];
    	    $new_roles = array();
    	    foreach($roles as $role) {
    	        $role = trim($role);
    	        if ($role=='*') {
    	            $new_roles = array('*');
    	            break;
    	        }
    	        
    	        $new_roles[] = (int) $role;
    	    }
    	    
    	    if (PerchUtil::count($new_roles)) {
    	        $data['pageSubpageRoles'] = implode(',', $new_roles);
    	    }
    	}else{
    	    $data['pageSubpageRoles'] = '';
    	}
    	
    	
    	
    	$Page->update($data);
    	
    	if (isset($_POST['pageParentID'])) {
    	    $parentID = (int) $_POST['pageParentID'];
    	    
    	    if ($parentID != $Page->pageParentID()) {
    	        $Page->update_tree_position($parentID, false, $cascade=true);
    	    }
    	    
    	}
    	
    	
    	// update regions on this page
    	$Regions = new PerchContent_Regions;
    	$regions = $Regions->get_for_page($Page->id());
    	
    	if (PerchUtil::count($regions)) {
    	    foreach($regions as $Region) {
                if ($Region->regionPage()!='*') {
                    $region_data = array();
                    $region_data['regionPage'] = $data['pagePath'];
                    $Region->update($region_data);
                }
    	    }
    	}
    	
    	
    	

    	
    	
    	
    	$Alert->set('success', PerchLang::get('Successfully updated'));
    }

    $created = false;
    
    if (isset($_GET['created'])) {
        $Alert->set('success', PerchLang::get('Your page has been successfully created. Return to %spage listing%s', '<a href="'.PERCH_LOGINPATH .'/core/apps/content/">', '</a>'));
        $created = true;
    }


    $Roles = new PerchUserRoles();
    $roles = $Roles->all();
    
    $details = $Page->to_array();
?>