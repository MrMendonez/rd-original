<?php

class PerchFieldTypes 
{
    private static $_seen = array();
    
    public static function get($type, $Form, $Tag, $all_tags=false)
    {
        $r = false;
        
        $classname = 'PerchFieldType_'.$type;
        
            
        if (class_exists($classname)){
            $r = new $classname($Form, $Tag);
            if (!in_array($classname, self::$_seen)) {
                $Perch = Perch::fetch();
                if ($Perch->admin) {
                    if ($all_tags) $r->set_sibling_tags($all_tags);
                    $r->add_page_resources();
                }
                
                self::$_seen[] = $classname;
            }
        }else{    
            $path = PerchUtil::file_path(PERCH_PATH.'/addons/fieldtypes/'.$type.'/'.$type.'.class.php');
            if (file_exists($path)) {
                include($path);
                $r =  new $classname($Form, $Tag);

                $Perch = Perch::fetch();
                if ($Perch->admin) {
                    if ($all_tags) $r->set_sibling_tags($all_tags);
                    $r->add_page_resources();
                }

            }
            
        }
        
        if (!is_object($r)) {
            $r = new PerchFieldType($Form, $Tag);
        }
        
        if ($all_tags) {
            $r->set_sibling_tags($all_tags);
        }
        
        return $r;
    }
}

/* ---------------------------- DEFAULT FIELD TYPES ---------------------------- */


/* ------------ URL ------------ */

class PerchFieldType_url extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        return $this->Form->url($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), $this->Tag->size(), $this->Tag->maxlength());
    }
}

/* ------------ EMAIL ------------ */

class PerchFieldType_email extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        return $this->Form->email($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), $this->Tag->size(), $this->Tag->maxlength());
    }
}

/* ------------ SEARCH ------------ */

class PerchFieldType_search extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        return $this->Form->search($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), $this->Tag->size(), $this->Tag->maxlength());
    }
}

/* ------------ DATE ------------ */

class PerchFieldType_date extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        if ($this->Tag->time()) {
            return $this->Form->datetimepicker($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
        }else{
            return $this->Form->datepicker($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
        }
    }
    
    public function get_raw($post=false, $Item=false)
    {
        $id = $this->Tag->id();
        $this->raw_item = $this->Form->get_date($id, $post);
        return $this->raw_item;
    }
    
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        return strftime('%A %d %B %Y', strtotime($raw));
    }
}


/* ------------ TIME ------------ */

class PerchFieldType_time extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        return $this->Form->timepicker($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
    }
    
    public function get_raw($post=false, $Item=false)
    {
        $id = $this->Tag->id();
        $this->raw_item = $this->Form->get_date($id, $post);
        return $this->raw_item;
    }
}

/* ------------ SLUG ------------ */

class PerchFieldType_slug extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        return '';
    }
    
    public function get_raw($post=false, $Item=false)
    {
        if (isset($post[$this->Tag->for()])) {
            return PerchUtil::urlify(trim(stripslashes($post[$this->Tag->for()])));
        }
        
        return '';
    }
    
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();

        $parts = explode('-', $raw);
        return implode(' ', $parts);
    }
}


/* ------------ TEXTAREA ------------ */

class PerchFieldType_textarea extends PerchFieldType
{

    public function add_page_resources()
    {
        $siblings = $this->get_sibling_tags();

        if (is_array($siblings)) {
            $Perch = Perch::fetch();
            $seen_editors = array();
            foreach($siblings as $tag) {
                if ($tag->editor() && !in_array($tag->editor(), $seen_editors)) {
                    $dir = PerchUtil::file_path(PERCH_PATH.'/addons/plugins/editors/'.$tag->editor());
                    $file = PerchUtil::file_path($dir.'/_config.inc');
                    if (is_dir($dir) && is_file($file)) {
                        $Perch->add_head_content(str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents($file)));
                        $seen_editors[] = $tag->editor();
                    }else{
                        PerchUtil::debug('Editor requested, but not installed: '.$this->Tag->editor(), 'error');
                    }
                }
            }
        }else{
            if ($this->Tag->editor()) {
            
                $dir = PerchUtil::file_path(PERCH_PATH.'/addons/plugins/editors/'.$this->Tag->editor());
                $file = PerchUtil::file_path($dir.'/_config.inc');
                if (is_dir($dir) && is_file($file)) {
                    $Perch = Perch::fetch();

                    $Perch->add_head_content(str_replace('PERCH_LOGINPATH', PERCH_LOGINPATH, file_get_contents($file)));
                }else{
                    PerchUtil::debug('Editor requested, but not installed: '.$this->Tag->editor(), 'error');
                }

            } 
        }




          
    }


    public function render_inputs($details=array())
    {
        $classname = 'large ';
        if ($this->Tag->editor())     $classname .= $this->Tag->editor();
        if ($this->Tag->textile())    $classname .= ' textile';
        if ($this->Tag->markdown())   $classname .= ' markdown';
        if ($this->Tag->size())       $classname .= ' '.$this->Tag->size();
        if (!$this->Tag->textile() && !$this->Tag->markdown() && $this->Tag->html()) $classname .= ' html';
        
        $data_atrs = array();
        if ($this->Tag->imagewidth())     $data_atrs['width']   = $this->Tag->imagewidth();
        if ($this->Tag->imageheight())    $data_atrs['height']  = $this->Tag->imageheight();
        if ($this->Tag->imagecrop())      $data_atrs['crop']    = $this->Tag->imagecrop();
        if ($this->Tag->imageclasses())   $data_atrs['classes'] = $this->Tag->imageclasses();

        
        if (isset($details[$this->Tag->input_id()]) && $details[$this->Tag->input_id()]!='') {
            $data = $details[$this->Tag->input_id()];        
            if (is_array($data)) {
                $details = array($this->Tag->id()=>$data['raw']);
            }
            
        }
    
    
        $s = $this->Form->textarea($this->Tag->input_id(), $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()), $classname, $data_atrs);
        $s .= '<div class="clear"></div>';
        
        return $s;
    }
    

    public function get_raw($post=false, $Item=false)
    {
        if ($post===false) {
            $post = $_POST;
        }
        
        $id = $this->Tag->id();
        if (isset($post[$id])) {
            $raw = trim($post[$id]);
            
            $value = stripslashes($raw);
            
            
            $formatting_language_used = false;

            // Strip HTML by default
            if (!is_array($value) && PerchUtil::bool_val($this->Tag->html()) == false) {
                $value = PerchUtil::html($value);
                $value = strip_tags($value);
            }

            // Textile
            if (!$formatting_language_used && PerchUtil::bool_val($this->Tag->textile()) == true) {
                $Textile = new Textile;
                $value  =  $Textile->TextileThis($value);

                if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
        		    $value = str_replace(' />', '>', $value);
        		}

                $formatting_language_used = true;
            }

            // Markdown
            if (!$formatting_language_used && PerchUtil::bool_val($this->Tag->markdown()) == true) {
                // Fix markdown blockquote syntax - > gets encoded.
                $value = preg_replace('/[\n\r]&gt;\s/', "\n> ", $value);
                $Markdown = new Markdown_Parser;
                $value = $Markdown->transform($value);
                $formatting_language_used = true;
            }
            
            
            
            $store = array(
                'raw' => $raw,
                'processed' => $value
            );
            
            $this->raw_item = $store;
        
            return $this->raw_item;
        }
        
        return null;
    }
    

    public function get_processed($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        $value = $raw;
        
        if (is_array($value)) {
            if (isset($value['processed'])) {
                return $value['processed'];
            }
            
            if (isset($value['raw'])) {
                return $value['raw'];
            }
        }else{
            if (!strpos($this->Tag->id(),'HTML')) {
                $value = $this->get_raw(array($this->Tag->id()=>$value));
                return $value['processed'];    
            }
            
        }


            
        return $value;
    }
    
    
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        if (is_array($raw)) {
            
            if (isset($raw['processed'])) {
                return strip_tags($raw['processed']);
            }
                    
            if (isset($raw['raw'])) {
                return $raw['raw'];
            }
            
        }
        
        return $raw;
    }

}

/* ------------ CHECKBOX ------------ */

class PerchFieldType_checkbox extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        $val = ($this->Tag->value() ? $this->Tag->value() : '1');
        return $this->Form->checkbox($this->Tag->input_id(), $val, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
    }

}

/* ------------ SELECT ------------ */

class PerchFieldType_select extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        $options = explode(',', $this->Tag->options());
        $opts = array();
        if (PerchUtil::bool_val($this->Tag->allowempty())== true) {
            $opts[] = array('label'=>'', 'value'=>'');
        }
        if (PerchUtil::count($options) > 0) {
            foreach($options as $option) {
                $val = trim($option);
                $label = $val;
                if (strpos($val, '|')!==false) {
                    $parts = explode('|', $val);
                    $label = $parts[0];
                    $val   = $parts[1];
                }
                $opts[] = array('label'=>$label, 'value'=>$val);
            }
        }
        return $this->Form->select($this->Tag->input_id(), $opts, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
    }

}


/* ------------ RADIO ------------ */

class PerchFieldType_radio extends PerchFieldType
{
    public function render_inputs($details=array())
    {
        $s = '';
        $options = explode(',', $this->Tag->options());
        if (PerchUtil::count($options) > 0) {
            $k = 0;
            foreach($options as $option) {
                $val    = trim($option);
                $label  = $val;
                if (strpos($val, '|')!==false) {
                    $parts = explode('|', $val);
                    $label = $parts[0];
                    $val   = $parts[1];
                }
                $id  = $this->Tag->input_id() . $k;
                $s .= '<span class="radio">';
                $s .= $this->Form->radio($id, $this->Tag->input_id(), $val, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
                $this->Form->disable_html_encoding();
                $s .= $this->Form->label($id, $label, 'radio', false, false);
                $this->Form->enable_html_encoding();
                $s .= '</span>';
                $k++;
            }
        }
        
        return $s;
    }

}


/* ------------ IMAGE ------------ */

class PerchFieldType_image extends PerchFieldType
{
    public static $file_paths = array();
    
    
    public function render_inputs($details=array())
    {
        $PerchImage = new PerchImage;
        $s = $this->Form->image($this->Tag->input_id());
        if (isset($details[$this->Tag->input_id()]) && $details[$this->Tag->input_id()]!='') {
            $json = $details[$this->Tag->input_id()];
            
            PerchUtil::debug($json);

            if (isset($json['sizes']['thumb'])) {
                $image_src  = $json['sizes']['thumb']['path'];
                $image_w    = $json['sizes']['thumb']['w'];
                $image_h    = $json['sizes']['thumb']['h'];
            }else{
                // For items imported from previous version
                $image_src = str_replace(PERCH_RESPATH, '', $PerchImage->get_resized_filename($json, 150, 150, 'thumb'));
                $image_w   = '';
                $image_h   = '';
            }
            
            $image_path = PerchUtil::file_path(PERCH_RESFILEPATH.'/'.$image_src);

            PerchUtil::debug($image_path);
            if (file_exists($image_path)) {
                $s .= '<img class="preview" src="'.PerchUtil::html(PERCH_RESPATH.'/'.$image_src).'" width="'.$image_w.'" height="'.$image_h.'" alt="Preview" />';
                $s .= '<div class="remove">';
                $s .= $this->Form->checkbox($this->Tag->input_id().'_remove', '1', 0).' '.$this->Form->label($this->Tag->input_id().'_remove', PerchLang::get('Remove image'), 'inline');
                $s .= $this->Form->hidden($this->Tag->input_id().'_populated', '1');
                $s .= '</div>';
            }
        }
        return $s;
    }
    
    public function get_raw($post=false, $Item=false) 
    {
        $store = array();
        
        $image_folder_writable = is_writable(PERCH_RESFILEPATH);

        
        $item_id = $this->Tag->input_id();
        
        if ($image_folder_writable && isset($_FILES[$item_id]) && (int) $_FILES[$item_id]['size'] > 0) {
            
            PerchUtil::debug($_FILES[$item_id]);
            
            if (!isset(self::$file_paths[$this->Tag->id()])) {
            
                $filename = PerchUtil::tidy_file_name($_FILES[$item_id]['name']);
                if (strpos($filename, '.php')!==false) $filename .= '.txt'; // diffuse PHP files
                $target = PerchUtil::file_path(PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.$filename);
                if (file_exists($target)) {                                        
                    $dot = strrpos($filename, '.');
                    $filename_a = substr($filename, 0, $dot);
                    $filename_b = substr($filename, $dot);

                    $count = 1;
                    while (file_exists(PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.PerchUtil::tidy_file_name($filename_a.'-'.$count.$filename_b))) {
                        $count++;
                    }

                    $filename = PerchUtil::tidy_file_name($filename_a . '-' . $count . $filename_b);
                    $target = PERCH_RESFILEPATH.DIRECTORY_SEPARATOR.$filename;
            
                }
                                    
                PerchUtil::move_uploaded_file($_FILES[$item_id]['tmp_name'], $target);
                self::$file_paths[$this->Tag->id()] = $target;     
                    
                $store['_default'] = PERCH_RESPATH.'/'.$filename;                        
                $store['path'] = $filename;
                $store['size'] = filesize($target);
                $store['bucket'] = 'default';
                
                $size = getimagesize($target);
                if (PerchUtil::count($size)) {
                    $store['w'] = $size[0];
                    $store['h'] = $size[1];
                }
        
                //$ContentItem->log_resource($var);
        
                // thumbnail
                $PerchImage = new PerchImage;
                $result = $PerchImage->resize_image($target, 150, 150, false, 'thumb');
                if (is_array($result)) {
                    if (!isset($store['sizes'])) $store['sizes'] = array();
                
                    $variant_key = 'thumb';
                    $tmp = array();
                    $tmp['w'] = $result['w'];
                    $tmp['h'] = $result['h'];
                    $tmp['path'] = $result['file_name'];
                    $tmp['size'] = filesize($result['file_path']);
                    
                    $store['sizes'][$variant_key] = $tmp;
                }
                
                
            }
        }

        // Loop through all tags with this ID, get their dimensions and resize the images.
        $all_tags = $this->get_sibling_tags();
        
        if (PerchUtil::count($all_tags)) {
            foreach($all_tags as $Tag) {
                if ($Tag->id()==$this->Tag->id()) {
                    // This is either this tag, or another tag in the template with the same ID.
                    
                    if ($Tag->type()=='image' && ($Tag->width() || $Tag->height()) && isset(self::$file_paths[$Tag->id()])) {
                        $PerchImage = new PerchImage;
                        if ($Tag->quality()) $PerchImage->set_quality($Tag->quality());
                        $result = $PerchImage->resize_image(self::$file_paths[$Tag->id()], $Tag->width(), $Tag->height(), $Tag->crop());
                        
                        if (is_array($result)) {
                            if (!isset($store['sizes'])) $store['sizes'] = array();
                        
                            $variant_key = 'w'.$Tag->width().'h'.$Tag->height().'c'.($Tag->crop() ? '1' : '0');
                            $tmp = array();
                            $tmp['w'] = $result['w'];
                            $tmp['h'] = $result['h'];
                            $tmp['path'] = $result['file_name'];
                            $tmp['size'] = filesize($result['file_path']);
                            
                            $store['sizes'][$variant_key] = $tmp;
                        }
                    }
                }
            }
        }
        

        // If a file isn't uploaded...
        if (!isset($_FILES[$item_id]) || (int) $_FILES[$item_id]['size'] == 0) {
            // If remove is checked, remove it.
            if (isset($_POST[$item_id.'_remove'])) {
                $store = array();
            }else{
                // Else get the previous data and reuse it.
                if (is_object($Item)){
                    $json = PerchUtil::json_safe_decode($Item->itemJSON(), true);
                    if (PerchUtil::count($json) && isset($json[$this->Tag->id()])) {
                        $store = $json[$this->Tag->id()];
                    }
                }else if (is_array($Item)) {
                    $json = $Item;
                    if (PerchUtil::count($json) && isset($json[$this->Tag->id()])) {
                        $store = $json[$this->Tag->id()];
                    }
                }
            }                                
        }

        
        self::$file_paths = array();
        
        return $store;
    }
    
    public function get_processed($raw=false)
    {    
        $json = $raw;
        if (is_array($json)) {
            
            $item = $json;
            
            if ($this->Tag->width() || $this->Tag->height()) {
                $variant_key = 'w'.$this->Tag->width().'h'.$this->Tag->height().'c'.($this->Tag->crop() ? '1' : '0');
                if (isset($json['sizes'][$variant_key])) {
                    $item = $json['sizes'][$variant_key];
                }
            }
            
            
            if ($this->Tag->output() && $this->Tag->output()!='path') {
                switch($this->Tag->output()) {        
                    case 'size':
                        return isset($item['size']) ? $item['size'] : 0; 
                        break;
                        
                    case 'h':
                    case 'height':
                        return isset($item['h']) ? $item['h'] : 0;
                        break;

                    case 'w':
                    case 'width':
                        return isset($item['w']) ? $item['w'] : 0;
                        break;
					
					case 'filename':
						return $item['path'];
						break;
                }
            }

            
            
            
            return PERCH_RESPATH.'/'.str_replace(PERCH_RESPATH.'/', '', $item['path']);
        }

        if ($this->Tag->width() || $this->Tag->height()) {
            $PerchImage = new PerchImage;
            return $PerchImage->get_resized_filename($raw, $this->Tag->width(), $this->Tag->height());
        }

        return PERCH_RESPATH.'/'.str_replace(PERCH_RESPATH.'/', '', $raw);
    }
    
    public function get_search_text($raw=false)
    {
        return '';
    }

}


/* ------------ FILE -- note, extends Image ------------ */

class PerchFieldType_file extends PerchFieldType_image
{
    public function render_inputs($details=array())
    {
        
        
        $s = $this->Form->image($this->Tag->input_id());
        if (isset($details[$this->Tag->input_id()]) && $details[$this->Tag->input_id()]!='') {
            $json = $details[$this->Tag->input_id()];

            if (is_array($json) && isset($json['path'])) {
                $path = $json['path'];
            }else{
                $path = $json;
            }
            
            $s .= '<div class="file icon">'.PerchUtil::html(str_replace(PERCH_RESPATH.'/', '', $path)).'</div>';
            $s .= '<div class="remove">';
            $s .= $this->Form->checkbox($this->Tag->input_id().'_remove', '1', 0).' '.$this->Form->label($this->Tag->input_id().'_remove', PerchLang::get('Remove file'), 'inline');
            $s .= $this->Form->hidden($this->Tag->input_id().'_populated', '1');
            $s .= '</div>';
        }
        
        return $s;
    }

   
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        return str_replace(array('/', '\\', '-', '_', '.'), ' ', $raw);
    }

}


/* ------------ MAP ------------ */

class PerchFieldType_map extends PerchFieldType
{
    public static $mapcount = 1;
	public $processed_output_is_markup = true;
    
    public function add_page_resources()
    {
        $Perch = Perch::fetch();
        $Perch->add_foot_content('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
    }
    
    
    public function render_inputs($details=array())
    {
        $s = $this->Form->text($this->Tag->input_id().'_adr', $this->Form->get((isset($details[$this->Tag->input_id()])? $details[$this->Tag->input_id()] : array()), 'adr', $this->Tag->default()), 'map_adr');                            
        $s .= '<div class="map" data-btn-label="'.PerchLang::get('Find').'" data-mapid="'.PerchUtil::html($this->Tag->input_id()).'" data-width="'.($this->Tag->width() ? $this->Tag->width() : '460').'" data-height="'.($this->Tag->height() ? $this->Tag->height() : '320').'">';
            if (isset($details[$this->Tag->input_id()]['admin_html'])) {
                $s .= $details[$this->Tag->input_id()]['admin_html'];
                $s .= $this->Form->hidden($this->Tag->input_id().'_lat',  $details[$this->Tag->input_id()]['lat']);
                $s .= $this->Form->hidden($this->Tag->input_id().'_lng',  $details[$this->Tag->input_id()]['lng']);
                $s .= $this->Form->hidden($this->Tag->input_id().'_clat', $details[$this->Tag->input_id()]['clat']);
                $s .= $this->Form->hidden($this->Tag->input_id().'_clng', $details[$this->Tag->input_id()]['clng']);
                $s .= $this->Form->hidden($this->Tag->input_id().'_type', $details[$this->Tag->input_id()]['type']);
                $s .= $this->Form->hidden($this->Tag->input_id().'_zoom', $details[$this->Tag->input_id()]['zoom']);
            }
        $s .= '</div>';
        return $s;
    }
    
    public function get_raw($post=false, $Item=false)
    {
        $var = '';
        if (isset($post[$this->Tag->id().'_adr']) && $post[$this->Tag->id().'_adr']!='') {
            $tmp = array();
            $tmp['adr'] = stripslashes(trim($post[$this->Tag->id().'_adr']));
        
            $map_fields = array('lat', 'lng', 'clat', 'clng', 'type', 'zoom');
            foreach($map_fields as $map_field) {
                if (isset($post[$this->Tag->id().'_'.$map_field]) && $post[$this->Tag->id().'_'.$map_field]!=''){
                    $tmp[$map_field] = $post[$this->Tag->id().'_'.$map_field];
                }
            }
                                                
            $var = $this->_process_map($this->unique_id.'-'.self::$mapcount, $this->Tag, $tmp);
            self::$mapcount++;
        }
        
        return $var;
    }
    
    public function get_processed($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        return $raw['html'];
    }
    
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
		if (!PerchUtil::count($raw)) return false;

        return $raw['_title'];
    }
    
    
    private function _process_map($id, $tag, $value)
    {
        $out = array();

        
        if (isset($value['adr'])) {
            
            $out['adr']     = $value['adr'];
            $out['_title']  = $value['adr'];
            $out['_default']= $value['adr'];
            
            if (!isset($value['lat'])) {
            
                $lat = false;
                $lng = false;
            
                $path = '/maps/api/geocode/json?address='.urlencode($value['adr']).'&sensor=false';
                $result = PerchUtil::http_get_request('http://', 'maps.googleapis.com', $path);
                if ($result) {
                    $result = PerchUtil::json_safe_decode($result, true);
                    PerchUtil::debug($result);
                    if ($result['status']=='OK') {
                        if (isset($result['results'][0]['geometry']['location']['lat'])) {
                            $lat = $result['results'][0]['geometry']['location']['lat'];
                            $lng = $result['results'][0]['geometry']['location']['lng'];
                        }
                    }  
                }
            }else{
                $lat = $value['lat'];
                $lng = $value['lng'];
            }
            
            $out['lat'] = $lat;
            $out['lng'] = $lng;
            
            
            if (!isset($value['clat'])) {
                $clat = $lat;
                $clng = $lng;
            }else{
                $clat = $value['clat'];
                $clng = $value['clng'];
            }
            
            $out['clat'] = $clat;
            $out['clng'] = $clng;
            
            if (!isset($value['zoom'])) {
                if ($tag->zoom()) {
                    $zoom = $tag->zoom();
                }else{
                    $zoom = 15;
                }
            }else{
                $zoom = $value['zoom'];
            }
            
            if (!isset($value['type'])) {
                if ($tag->type()) {
                    $type = $tag->type();
                }else{
                    $type = 'roadmap';
                }
            }else{
                $type = $value['type'];
            }
            
                       
            $adr    = $value['adr'];
            $width  = ($tag->width() ? $tag->width() : '460');
            $height = ($tag->height() ? $tag->height() : '320');  
            
            $out['zoom'] = $zoom;
            $out['type'] = $type;
                        
            $r  = '<img id="cmsmap'.PerchUtil::html($id).'" src="http://maps.google.com/maps/api/staticmap';
            $r  .= '?center='.$clat.','.$clng.'&amp;sensor=false&amp;size='.$width.'x'.$height.'&amp;zoom='.$zoom.'&amp;maptype='.$type;
            if ($lat && $lng)   $r .= '&amp;markers=color:red|color:red|'.$lat.','.$lng;    
            $r  .= '" ';
            if ($tag->class())  $r .= ' class="'.PerchUtil::html($tag->class()).'"';
            $r  .= ' width="'.$width.'" height="'.$height.'" alt="'.PerchUtil::html($adr).'" />';
            
            $out['admin_html'] = $r;
            
            // JavaScript
            $r .= '<script type="text/javascript">/* <![CDATA[ */ ';
            $r .= "if(typeof(CMSMap)=='undefined'){var CMSMap={};CMSMap.maps=[];document.write('<scr'+'ipt type=\"text\/javascript\" src=\"".PerchUtil::html(PERCH_LOGINPATH)."/core/assets/js/public_maps.js\"><'+'\/sc'+'ript>');}";
            $r .= "CMSMap.maps.push({'mapid':'cmsmap".PerchUtil::html($id)."','width':'".$width."','height':'".$height."','type':'".$type."','zoom':'".$zoom."','adr':'".addslashes(PerchUtil::html($adr))."','lat':'".$lat."','lng':'".$lng."','clat':'".$clat."','clng':'".$clng."'});";
            $r .= '/* ]]> */';
            $r .= '</script>';

            
            if (defined('PERCH_XHTML_MARKUP') && PERCH_XHTML_MARKUP==false) {
    		    $r = str_replace('/>', '>', $r);
    		}
            
            $out['html'] = $r;
        }
        
        return $out;
    }
    
    

}


/* ---- DATA SELECT ---- */

class PerchFieldType_dataselect extends PerchFieldType
{

    public function render_inputs($details=array())
    {
        $Perch = Perch::fetch();

        $page = false;

        // Find the path path.
        // 
        // Has it been set as an attribute?
        if ($this->Tag->page()) {
            $page = $this->Tag->page();
        }

        // Has the PageID been set from the edit page?
        if (!$page && $this->Tag->page_id()) {
            $Pages = new PerchContent_Pages;
            $Page = $Pages->find($this->Tag->page_id());
            if ($Page) {
                $page = $Page->pagePath();
            }
        }

        // Use the current page.
        if (!$page) {
            $page = $Perch->get_page();
        }

        $region = $this->Tag->region();
        $field_id = $this->Tag->options();
        $values_id = $this->Tag->values();

        $Regions = new PerchContent_Regions;

        $opts = $Regions->find_data_select_options($page, $region, $field_id, $values_id);



        if (PerchUtil::bool_val($this->Tag->allowempty())== true) {
            array_unshift($opts, array('label'=>'', 'value'=>''));
        }
        
        return $this->Form->select($this->Tag->input_id(), $opts, $this->Form->get($details, $this->Tag->id(), $this->Tag->default(), $this->Tag->post_prefix()));
    }

}



?>