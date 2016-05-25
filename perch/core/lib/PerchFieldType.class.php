<?php
/**
 * Default field type
 *
 * @package default
 * @author Drew McLellan
 */
class PerchFieldType
{
    /**
     * The form object the field is used with
     *
     * @var string
     */
    protected $Form = false;
    
    /**
     * The tag object for the field
     *
     * @var string
     */
    protected $Tag = false;
    
    
    /**
     * The field ID used with setting the required validation
     *
     * @var string
     */
    protected $required_id = false;
    
    
    /**
     * A unique ID for using when e.g. outputting unique elements to the HTML
     *
     * @var string
     */
    protected $unique_id = false;
    
    
    /**
     * The un-processed item
     *
     * @var string
     */
    protected $raw_item = false;
    
    
    /**
     * All the tags from the template
     *
     * @var string
     */
    protected $sibling_tags = false;


	/**
	 * Is the processed output from this field pre-encoded markup? Use by template for safe encoding.
	 *
	 * @var string
	 */
	public $processed_output_is_markup = false;

    
    public function __construct($Form, $Tag)
    {
        $this->Form = $Form;
        $this->Tag  = $Tag;
        
        $this->required_id = $Tag->input_id();
    
    }
    
    public function add_page_resources()
    {
        
    }
    
    /**
     * Set the unique ID used by this field for rendering
     *
     * @param string $id 
     * @return void
     * @author Drew McLellan
     */
    public function set_unique_id($id)
    {
        $this->unique_id = $id;
    }
    
    /**
     * Get the field ID for required valiation
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_required_id()
    {
        return $this->required_id;
    }

    /**
     * Generate HTML string of form input controls
     *
     * @param string $details 
     * @return void
     * @author Drew McLellan
     */
    public function render_inputs($details=array())
    {
        $s = '';
        $id = $this->Tag->id();
        $s = $this->Form->text($this->Tag->input_id(), $this->Form->get($details, $id, $this->Tag->default(), $this->Tag->post_prefix()), $this->Tag->size(), $this->Tag->maxlength());
                
        return $s;
    }
    
    /**
     * Get raw value
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_raw($post=false, $Item=false)
    {
        if ($post===false) {
            $post = $_POST;
        }
        
        $id = $this->Tag->id();
        if (isset($post[$id])) {
            $this->raw_item = trim(stripslashes($post[$id]));
            return $this->raw_item;
        }
        
        return null;
    }
    
    /**
     * Get processed value
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_processed($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        $value = $raw;
    
        return $value;
    }
    
    /**
     * Get the text used for search indexing
     *
     * @param string $raw 
     * @return void
     * @author Drew McLellan
     */
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        
        return $raw;
    }
    
    /**
     * Set sibling tags
     *
     * @param string $tags 
     * @return void
     * @author Drew McLellan
     */
    public function set_sibling_tags($tags)
    {
        $this->sibling_tags = $tags;
    }
    
    
    /**
     * Get sibling tags from the template, if set.
     *
     * @return void
     * @author Drew McLellan
     */
    public function get_sibling_tags()
    {
        return $this->sibling_tags;
    }
    
}

?>