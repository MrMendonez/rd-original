<?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int) $_GET['id'];

        $Pages  = new PerchContent_Pages;
        $Page = $Pages->find($id);
    }

    
    if (!$Page || !is_object($Page)) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content');
    }

    // Check permission to delete
    if (!$CurrentUser->has_priv('content.pages.delete')) {
        PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content');
    }



    /* --------- Delete Form ----------- */
    
    $Form = new PerchForm('delete');
    
    if ($Form->posted() && $Form->validate()) {
    	$Page->delete();
    	
    	if ($Form->submitted_via_ajax) {
    	    echo PERCH_LOGINPATH . '/core/apps/content/';
    	    exit;
    	}else{
    	    PerchUtil::redirect(PERCH_LOGINPATH . '/core/apps/content/');
    	}
    	    	
    }

    

?>