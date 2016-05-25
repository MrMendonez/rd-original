<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>

    
    <h3 class="em"><span><?php echo PerchLang::get('About this region'); ?></span></h3>
    <p>
        <?php 
            if ($Region->regionMultiple()=='1') {
                echo PerchLang::get("This region may contain one or more items.");
            }else{
                echo PerchLang::get("This region only has a single item.");
            }
            
            echo ' '. PerchLang::get("Required fields are marked with an asterisk.");
        ?>
    </p>
    

<?php




    if (false && $Region->regionMultiple()=='1') {
        if (PerchUtil::count($details)>1) echo '<div id="content-reorder" data-id="'.PerchUtil::html($Region->id()).'">';
        
        if ($Region->get_option('edit_mode')=='listdetail') {
            echo '<h4><a href="'.PERCH_LOGINPATH.'/core/apps/content/edit/?id='.$Region->id().'">' .  PerchLang::get('Items') .'</a>';
        }else{
            echo '<h4>'  . PerchLang::get('Items');
        }
        
        
            if (PerchUtil::count($details)>1) echo '<span class="buttons"><a class="reorder" href="reorder/?id='.PerchUtil::html($Region->id()).'">'.PerchLang::get('Reorder').'</a></span>';
        echo '</h4>';
        echo '<ul>';
        if ($item_id) {
            $all_items = $Region->get_items_for_editing();
            $details_flat = array();

            foreach($all_items as $detail) {
                if (PerchUtil::count($detail)) {
                    $i = $detail['itemID'];
                    $tmp = $detail;
                    foreach($detail as $key=>$val) {
                        $tmp['perch_'.$i.'_'.$key] = $val;
                    }
                    $details_flat[] = $tmp;
                }
            }
            $all_items = $details_flat;
        }else{
            $all_items = $details;
        }
        
        $k = 0;
        foreach($all_items as $item) {
            $i = $item['itemID'];
            
            if ($item_id) {
                $link = PERCH_LOGINPATH.'/core/apps/content/edit/?id='.$Region->id().'&amp;itm='.$i;
            }else{
                $link = '#item'.$i;
            }
            
            if (isset($item['perch_'.$i.'__title'])) {
                echo '<li data-idx="'.$i.'"><a href="'.($link).'">' . PerchUtil::html($item['perch_'.$i.'__title']) . '</a></li>';
            }else{
                echo '<li data-idx="'.$i.'"><a href="'.($link).'">'.PerchLang::get('Item') . ' ' . PerchUtil::html($item['perch_'.$i.'_itemOrder']-999) . '</a></li>';
            }
            
            $k++;
        }
        echo '</ul>';
        
        if (PerchUtil::count($details)>1) echo '</div>';
        
        if (array_key_exists('limit', $options) && $options['limit']!=false) {
            echo '<p>';
            printf(PerchLang::get('This region is configured to display the first <strong>%s items only</strong>.'), PerchUtil::html($options['limit']));
            echo '</p>';
        }

    }




    if ($Region->regionTemplate() != '') {


        echo '<h3>' . PerchLang::get('Page assignment') . '</h3>';

        if ($Region->regionPage() == '*') {
            echo '<p>' . PerchLang::get('This region is shared across all pages.') . '</p>';
        }else{
            echo '<p>' . PerchLang::get('This region is only available within') . ':</p><p><code><a href="' . PerchUtil::html($Region->regionPage()) . '">' . PerchUtil::html($Region->regionPage()) . '</a></code></p>';
        }

    }
    
    


    
    

?>
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<?php include ('_subnav.php'); ?>


    <h1><?php 
            if ($Region->regionPage()=='*') {
                printf(PerchLang::get('Editing Shared Regions')); 
            }else{
                printf(PerchLang::get('Editing %s Page'),' &#8216;' . PerchUtil::html($Page->pageTitle()) . '&#8217; ');     
            }

            
        ?></h1>


   <?php echo $Alert->output(); ?>

		<ul class="smartbar">
            <li class="selected">
				<span class="set">
				<a class="sub" href="<?php 
                    if ($Region->regionPage()=='*') {
                        echo PERCH_LOGINPATH . '/core/apps/content/page/?id=-1';
                    }else{
                        echo PERCH_LOGINPATH . '/core/apps/content/page/?id='.PerchUtil::html($Region->pageID());
                    }
                ?>"><?php echo PerchLang::get('Regions'); ?></a> 
				<span class="sep icon"></span> 
				

                <?php
                    if ($Region->regionMultiple() && $Region->get_option('edit_mode')=='listdetail') {
                ?>
                <a class="sub" href="<?php echo PERCH_LOGINPATH . '/core/apps/content/edit/?id='.PerchUtil::html($Region->id());?>"><?php echo PerchUtil::html($Region->regionKey()); ?></a>
                <span class="sep icon"></span> 
                <a href="<?php echo PERCH_LOGINPATH . '/core/apps/content/edit/?id='.PerchUtil::html($Region->id()).'&amp;itm='.$details[0]['itemID'];?>"><?php 
                        
                        $item = $details[0];
                        $id = $item['itemID'];                   
                
                        if (isset($item['perch_'.$id.'__title'])) {
                            echo PerchUtil::html(PerchUtil::excerpt($item['perch_'.$id.'__title'], 10));
                        }else{
                            if (isset($item['itemOrder'])) {
                                echo PerchLang::get('Item'). ' '.PerchUtil::html($item['itemOrder']-999);
                            }else{
                                echo PerchLang::get('New Item');
                            }
                        }
                ?></a>
                <?php
                    }else{
                ?>
                    <a href="<?php echo PERCH_LOGINPATH . '/core/apps/content/edit/?id='.PerchUtil::html($Region->id());?>"><?php echo PerchUtil::html($Region->regionKey()); ?></a>
                <?php
                    }
                ?>

				</span>
			</li>
			<?php
				if ($CurrentUser->has_priv('content.regions.options')) {
		            echo '<li><a href="'.PERCH_LOGINPATH . '/core/apps/content/options/?id='.PerchUtil::html($Region->id()).'">' . PerchLang::get('Region Options') . '</a></li>';
		        }
			?>
			<?php
                if ($Region->regionMultiple()) {
                    echo '<li class="fin">';
                    echo '<a href="'.PERCH_LOGINPATH . '/core/apps/content/reorder/region/?id='.PerchUtil::html($Region->id()).'" class="icon reorder">Reorder</a>';
                    echo '</li>';
                }

			
				if ($Region->is_undoable()) {
					echo '<li class="fin">';
			        echo '<form method="post" action="'.PerchUtil::html($fUndo->action()).'">';
			        echo '<div>'.$fUndo->submit('btnUndo', 'Undo', 'unbutton icon undo', true, true).'</div>';
			        echo '</form>';
					echo '</li>';
			    }
			
			?>
        </ul>
		





<form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" <?php echo $Form->enctype(); ?> id="content-edit" class="magnetic-save-bar">

    <div id="main-panel"<?php if ($place_token_on_main) echo 'data-token="'.PerchUtil::html($place_token_on_main->get_token()).'"'; ?>>
 

<?php
    /*  ------------------------------------ EDIT CONTENT ----------------------------------  */

 
    if ($template_help_html) {
        echo '<h2><span>' . PerchLang::get('Help') .'</span></h2>';
        echo '<div id="template-help">' . $template_help_html . '</div>';
    }
    
?>
    
        <div class="items">
<?php

        if (is_array($tags)) {
            
            // loop through each item (usually one, sometimes more)
            $i = 0;
            foreach($details as $item) {
                $id = $item['itemID'];
                
                echo '<div class="edititem">';
                if ($Region->regionMultiple()) {
                    echo '<div class="h2" id="item'.($id).'">';
                        if (isset($item['perch_'.$id.'__title'])) {
                            echo '<h2>'. PerchUtil::html($item['perch_'.$id.'__title']) .'</h2>';
                        }else{
                            if (isset($item['itemOrder'])) {
                                echo '<h2>'. PerchLang::get('Item'). ' '.PerchUtil::html($item['itemOrder']-999).'</h2>';
                            }else{
                                PerchUtil::debug($item);
                                echo '<h2>'. PerchLang::get('New Item'). '</h2>';
                            }
                            
                        }
                        
                        echo '<a href="'.PERCH_LOGINPATH.'/core/apps/content/delete/item/?id='.PerchUtil::html($Region->id()).'&amp;itm='.$id.'" class="delete action inline-delete">'.PerchLang::get('Delete').'</a>';
                    echo '</div>';
                }else{
                    echo '<h2 class="em">'. PerchUtil::html($Region->regionKey()).'</h2>';
                }
                $seen_tags = array();
            
                foreach($tags as $tag) {
                    
                    $item_id = 'perch_'.$id.'_'.$tag->id();
                    $tag->set('input_id', $item_id);
                    $tag->set('post_prefix', 'perch_'.$id.'_');
                    if (is_object($Page)) $tag->set('page_id', $Page->id());

                    if (!in_array($tag->id(), $seen_tags) && $tag->type()!='hidden' && $tag->type()!='slug') {
                        echo '<div class="field '.$Form->error($item_id, false).'">';
                        
                        $label_text  = PerchUtil::html($tag->label());
                        if ($tag->type() == 'textarea') {
                            if (PerchUtil::bool_val($tag->textile()) == true) {
                                $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/core/help/textile" class="assist">Textile</a></span>';
                            }
                            if (PerchUtil::bool_val($tag->markdown()) == true) {
                                $label_text .= ' <span><a href="'.PERCH_LOGINPATH.'/core/help/markdown" class="assist">Markdown</a></span>';
                            }
                        }
                        $Form->disable_html_encoding();
                        echo $Form->label($item_id, $label_text, '', false, false);
                        $Form->enable_html_encoding();
                        
                        
                        $FieldType = PerchFieldTypes::get($tag->type(), $Form, $tag);
                        
                        echo $FieldType->render_inputs($item);
                                               
                            
                        if ($tag->help()) {
                            echo $Form->hint($tag->help());
                        }
                        
                        
                
                        echo '</div>';
                
                        $seen_tags[] = $tag->id();
                    }
                }
                
                
                echo '</div>';
                
                $i++; // item count
            }
        }
?>        
        </div>
        <p class="submit<?php if (defined('PERCH_NONSTICK_BUTTONS') && PERCH_NONSTICK_BUTTONS) echo ' nonstick'; ?><?php if ($Form->error) echo ' error'; ?>">
            <?php 
                echo $Form->submit('btnsubmit', 'Save Changes', 'button'); 
                
                if ($Region->regionMultiple()=='1') {
                    echo '<input type="submit" name="add_another" value="'.PerchUtil::html(PerchLang::get('Save & Add another')).'" id="add_another" class="button" />';
                }
                
                echo '<label class="save-as-draft" for="save_as_draft"><input type="checkbox" name="save_as_draft" value="1" id="save_as_draft" '.($draft?'checked="checked"':'').'  /> '.PerchUtil::html(PerchLang::get('Save as Draft')).'</label>';
                

				if ($Region->regionMultiple()=='1' && $Region->get_option('edit_mode')=='listdetail') {
                	echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'/core/apps/content/edit/?id='.$Region->id().'">' . PerchLang::get('Cancel'). '</a>'; 
            	}else{
				    if ($Region->regionPage() == '*') {
                        $pageID = '-1';
                    }else{
                        $pageID = $Page->id();
                    }

                	echo ' ' . PerchLang::get('or') . ' <a href="'.PERCH_LOGINPATH.'/core/apps/content/page/?id='.$pageID.'">' . PerchLang::get('Cancel'). '</a>'; 
				}
                
            ?>
        </p>

        <?php
            if ($Region->regionMultiple()=='1') {
                echo '<input type="submit" name="add_another" value="'.PerchUtil::html(PerchLang::get('Save & Add another')).'" class="add button topadd" />';
            }
        ?>
        
    </div>



</form>
<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>