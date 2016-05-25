<?php include (PERCH_PATH.'/core/inc/sidebar_start.php'); ?>
    <h3 class="em"><span><?php echo PerchLang::get('About this region'); ?></span></h3>
    
    <p><?php 
            echo PerchLang::get("This region may contain one or more items.");
            echo ' ';
            echo PerchLang::get("Select an item to edit its content.");
    ?></p>
    
    <?php
        if ($Region->regionTemplate() != '') {
            
            if ($CurrentUser->has_priv('content.regions.options')) {
                echo '<h4>'.PerchLang::get('Options').'</h4>';
            }else{
                echo '<h4>' . PerchLang::get('Page assignment') . '</h4>';
            }

            if ($Region->regionPage() == '*') {
                echo '<p>' . PerchLang::get('This region is shared across all pages.') . '</p>';
            }else{
                echo '<p>' . PerchLang::get('This region is only available within') . ':</p><p><code><a href="' . PerchUtil::html($Region->regionPage()) . '">' . PerchUtil::html($Region->regionPage()) . '</a></code></p>';
            }

            if ($CurrentUser->has_priv('content.regions.options')) {
                echo '<p>';
                echo ' <a href="'.PERCH_LOGINPATH . '/core/apps/content/options/?id='.PerchUtil::html($Region->id()).'">' . PerchLang::get('Set your options for this region.') . '</a></p>';
            }
        
        
        }
    ?>
<?php include (PERCH_PATH.'/core/inc/sidebar_end.php'); ?>
<?php include (PERCH_PATH.'/core/inc/main_start.php'); ?>
<?php include ('_subnav.php'); ?>

    <h1><?php 
            printf(PerchLang::get('Editing %s Region'),' &#8216;' . PerchUtil::html($Region->regionKey()) . '&#8217; '); 
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
                ?>">Regions</a> 
			<span class="sep icon"></span> 
			<a href="<?php echo PERCH_LOGINPATH . '/core/apps/content/edit/?id='.PerchUtil::html($Region->id());?>"><?php echo PerchUtil::html($Region->regionKey()); ?></a>
			</span>
		</li>
		<?php
			if ($CurrentUser->has_priv('content.regions.options')) {
	            echo '<li><a href="'.PERCH_LOGINPATH . '/core/apps/content/options/?id='.PerchUtil::html($Region->id()).'">' . PerchLang::get('Region Options') . '</a></li>';
	        }
		?>
		<li class="fin"><a class="icon reorder" href="<?php echo PERCH_LOGINPATH . '/core/apps/content/reorder/region/?id='.PerchUtil::html($Region->id());?>"><?php echo PerchLang::get('Reorder'); ?></a></li>
    </ul>





    <form method="post" action="<?php echo PerchUtil::html($Form->action()); ?>" class="sectioned">
    <?php
        if (PerchUtil::count($items)) {
            
            echo '<table class="d">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th class="first">'.PerchLang::get('Title').'</th>';
                        echo '<th class="last action"></th>';
                    echo '</tr>';
                echo '</thead>';
            
                echo '<tbody>';
                $i = 1;
                foreach($items as $item) {
                    echo '<tr>';
                        echo '<td>';
                            if (isset($item['_title'])) {
                                $title = $item['_title'];
                            }else{
                                $title = PerchLang::get('Item').' '.$i;
                            }
                        
                            echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH).'/core/apps/content/edit/?id=' . PerchUtil::html($Region->id()) . '&amp;itm='.PerchUtil::html($item['itemID']).'" class="">'.PerchUtil::html($title).'</a>';
                        echo '<td>';
                        
                            echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH).'/core/apps/content/delete/item/?id=' . PerchUtil::html($Region->id()) . '&amp;itm='.PerchUtil::html($item['itemID']).'" class="delete inline-delete">'.PerchLang::get('Delete').'</a>';
                            
                        echo '</td>';
                    echo '</tr>';
                    $i++;
                }
                echo '</tbody>';
            
            
            echo '</table>';
            
            
        }
    
    ?>

        <p class="submit<?php if ($Form->error) echo ' error'; ?>">
            <?php 
                echo $Form->submit('add_another', 'Add another item', 'button'); 
            ?>
        </p>
    </form>

<?php include (PERCH_PATH.'/core/inc/main_end.php'); ?>
