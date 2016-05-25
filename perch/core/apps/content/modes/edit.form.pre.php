<?php
    $place_token_on_main = false;
    
    // test to see if image folder is writable
    $image_folder_writable = is_writable(PERCH_RESFILEPATH);

    // set the current user
	$Region->set_current_user($CurrentUser->id());    

    // get options
    $options = $Region->get_options();

	// get Page
	$Pages = new PerchContent_Pages;
	$Page = $Pages->find($Region->pageID());

    // get details
    
    if (isset($item_id) && $item_id) {
        // Get the specified item ('detail' mode);
        $details    = $Region->get_items_for_editing($item_id);
    }else{
        $details    = $Region->get_items_for_editing();
        
        if (PerchUtil::count($details)==0) {
            $Region->add_new_item();
        }
        
        $details    = $Region->get_items_for_editing();
    }
        
    $item_count = PerchUtil::count($details);

    $template_help_html = '';
    $mapcount = 0;
    $has_map = false;


    /* --------- Undo Form ----------- */
    
    if ($Region->regionTemplate() != '') {
        
        $fUndo = new PerchForm('undo');

        if ($fUndo->posted()) {
        	if ($Region->revert_most_recent()) {
            
                if (isset($item_id) && $item_id) {
                    $details    = $Region->get_items_for_editing($item_id);
                }else{
                    $details    = $Region->get_items_for_editing();
                }
        	    
        	    $Alert->set('success', PerchLang::get('Your most recent change has been reverted.'));
        	}else{
        	    $Alert->set('error', PerchLang::get('There was nothing to undo.'));
        	}
            
        }   
    }
    
    
        

    /* --------- Edit Form ----------- */
    
    
    if ($Region->regionTemplate() != '') {

        $Template = new PerchTemplate('content/'.$Region->regionTemplate(), 'content');

		if ($Template->status==404) {
			$Alert->set('error', PerchLang::get('The template for this region (%s) cannot be found.', '<code>'.$Region->regionTemplate().'</code>'));
		}

        $tags   = $Template->find_all_tags('content');
        
        $template_help_html = $Template->find_help();
        
        $Form = new PerchForm('edit');
        
        $req = array();
        
        // Check for required content
        if (is_array($tags)) {
            foreach($details as $item) {
                $seen_tags = array();
                $postitems = $Form->find_items('perch_'.$item['itemID'].'_');
                
                foreach($tags as $tag) {

                    // initialising the field type here makes sure editor plugins are kicked of in the <head>
                    $FieldType = PerchFieldTypes::get($tag->type(), $Form, $tag, $tags);

                    $input_id = 'perch_'.$item['itemID'].'_'.$tag->id();
                    if (!in_array($tag->id(), $seen_tags)) {
                        if (PerchUtil::bool_val($tag->required())) {
                            if ($tag->type() == 'date') {
                                if ($tag->time()) {
                                    $req[$input_id.'_minute'] = "Required";
                                }else{
                                    $req[$input_id.'_year'] = "Required";
                                }
                            }else{
                                $req[$input_id] = "Required";
                            }
                        
                        }
                    
                        $seen_tags[] = $tag->id();
                    }
                }
            }
        }

        
        $Form->set_required($req);
        
        
        if ($Form->posted() && $Form->validate()) {
            
            // New rev
            $Region->create_new_revision();
            
            // Get items
            if (isset($item_id) && $item_id) {
                $items    = $Region->get_items_for_updating($item_id);
            }else{
                $items    = $Region->get_items_for_updating();
            }
            
            	

            if (is_array($tags)) {

                if (PerchUtil::count($items)) {
                    
                    foreach($items as $Item) {
                    
                        $id = $Item->itemID();
                        
                        $form_vars      = array();
                        $file_paths     = array();
                    	
                    	$search_text    = ' ';
                    	
                    	$form_vars['_id'] = $id;
                    
                        $seen_tags = array();
                        $postitems = $Form->find_items('perch_'.$id.'_');
                                            
                        $i = 0;
                    
                        foreach($tags as $Tag) {
                        
                            if (!in_array($Tag->id(), $seen_tags)) {
                            
                                $var = false;
                            
                                $Tag->set('input_id', 'perch_'.$Item->itemID().'_'.$Tag->id());
                        
                                $FieldType = PerchFieldTypes::get($Tag->type(), $Form, $Tag, $tags);
                                $FieldType->set_unique_id($Item->id());
                                
                                $var            = $FieldType->get_raw($postitems, $Item);
                                $search_text    .= $FieldType->get_search_text($var).' ';
                            
                
                                if ($var || (is_string($var) && strlen($var))) {
                                    if (!is_array($var)) $var = stripslashes($var);
                                    $form_vars[$Tag->id()] = $var;
                                
                                    // title
                                    if ($Tag->title()) {
                                        $title_var = $var;
                                    
                                        if (is_array($var)) {

                                            if (isset($var['_title'])) {
                                                $title_var = $var['_title'];
                                            }elseif(isset($var['_default'])) {
                                                $title_var = strip_tags($var['_default']);
                                            }elseif(isset($var['processed'])) {
                                                $title_var = strip_tags($var['processed']);
                                            }


                                        }
                                    
                                        if (isset($form_vars['_title'])) {
                                            $form_vars['_title'] .= ' '.$title_var;
                                        }else{
                                            $form_vars['_title'] = $title_var;
                                        }
                                    
                                    }
                                }
                                $seen_tags[] = $Tag->id();
                            }
                            $i++;
                        }

                        $data = array();
                        $data['itemJSON'] = PerchUtil::json_safe_encode($form_vars);
                        $data['itemSearch'] = $search_text;
                        
                        $Item->update($data);
                        
                    }
                }
            }
            
            // Sort based on region options
            $Region->sort_items();
            
            // Publish (or not if draft)
            if (isset($_POST['save_as_draft'])) {        
                $Alert->set('success', PerchLang::get('Draft successfully updated'));     
            }else{
                $Region->publish();        
                $Alert->set('success', PerchLang::get('Content successfully updated'));
            }
            
            
            // Add a new item if Save & Add Another
            if ($Region->regionMultiple()=='1' && isset($_POST['add_another'])) {    
        	    $NewItem = $Region->add_new_item();   
                if ($Region->get_option('edit_mode')=='listdetail') {
                    PerchUtil::redirect(PERCH_LOGINPATH.'/core/apps/content/edit/?id='.$Region->id().'&itm='.$NewItem->itemID().'&created=true');    
                }     
				
        	}
            
            
	        // Alert any file upload errors
        	if ($_FILES) { 
        	    foreach($_FILES as $file) {
        	        if ($file['error']!=UPLOAD_ERR_NO_FILE && $file['error']!=UPLOAD_ERR_OK) {
        	            $Alert->set('error', PerchLang::get('File failed to upload'));
        	        }
        	    }
        	}

            /*
        	
        	$ContentItem->clean_up_resources();
        
            */
            
            
            if (isset($item_id) && $item_id) {
                $details    = $Region->get_items_for_editing($item_id);
            }else{
                $details    = $Region->get_items_for_editing();
            }
            
            // Check for required content, again
            if (is_array($tags)) {
                foreach($details as $item) {
                    $seen_tags = array();
                    $postitems = $Form->find_items('perch_'.$item['itemID'].'_');
                    
                    foreach($tags as $tag) {
                        $input_id = 'perch_'.$item['itemID'].'_'.$tag->id();
                        if (!in_array($tag->id(), $seen_tags)) {
                            if (PerchUtil::bool_val($tag->required())) {
                                if ($tag->type() == 'date') {
                                    if ($tag->time()) {
                                        $req[$input_id.'_minute'] = "Required";
                                    }else{
                                        $req[$input_id.'_year'] = "Required";
                                    }
                                }else{
                                    $req[$input_id] = "Required";
                                }
                            
                            }
                        
                            $seen_tags[] = $tag->id();
                        }
                    }
                }
            }

            
            $Form->set_required($req);




            
        }else{
            PerchUtil::debug('Form not posted or did not validate');
        }
        

    }
    
    if (!$image_folder_writable) {
        $Alert->set('error', PerchLang::get('Your resources folder is not writable. Make this folder (') . PerchUtil::html(PERCH_RESPATH) . PerchLang::get(') writable if you want to upload files and images.'));
    }
    
    // is it a draft?
    if ($Region->has_draft()) {
        $draft = true;
        
        if ($Region->regionPage() == '*') {
            $Alert->set('draft', PerchLang::get('You are editing a draft.'));
        }else{
            $path = rtrim($Settings->get('siteURL')->val(), '/');
            $Alert->set('draft', PerchLang::get('You are editing a draft.') . ' <a href="'.PerchUtil::html($path.$Region->regionPage()).'?'.PERCH_PREVIEW_ARG.'=all" class="action draft-preview">'.PerchLang::get('Preview').'</a>');
        }
        
        
    }else{
        $draft = false;
    }
    

	if (isset($_GET['created'])) {
        $Alert->set('success', PerchLang::get('Content successfully updated and a new item added.'));
    }



    $Perch->add_javascript(PERCH_LOGINPATH.'/core/assets/js/maps.js');
    
    
    if (PerchUtil::count($details)) {
        $details_flat = array();

        foreach($details as $detail) {
            if (PerchUtil::count($detail)) {
                $i = $detail['itemID'];
                $tmp = $detail;
                foreach($detail as $key=>$val) {
                    $tmp['perch_'.$i.'_'.$key] = $val;
                }
                $details_flat[] = $tmp;
            }
        }
        $details = $details_flat;
    }
    
    //PerchUtil::debug($details);
?>