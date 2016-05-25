<?php
    
    $Templates  = new PerchContent_PageTemplates;
    $Pages      = new PerchContent_Pages;

    $Form = new PerchForm('edit');
	
    $message = false;
        
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $templateID = (int) $_GET['id'];    
        $Template = $Templates->find($templateID);
    }else{
        PerchUtil::redirect(PERCH_LOGINPATH.'/core/apps/content/page/templates/');
    }
    
    
    $Form = new PerchForm('editpage');

    $req = array();
    $req['templateTitle']   = "Required";

    $Form->set_required($req);
    
    if ($Form->posted() && $Form->validate()) {
    
		$postvars = array('templateTitle', 'optionsPageID', 'templateReference');
		
    	$data = $Form->receive($postvars);
    	
    	if (is_object($Template)) {
    	    $Template->update($data);
    	    $Alert->set('success', PerchLang::get('Your master page has been successfully edited.'));
    	}	    

    }
    
    
    
    $details = $Template->to_array();

?>