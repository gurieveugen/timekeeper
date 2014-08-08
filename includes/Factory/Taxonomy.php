<?php
namespace Factory;
// =========================================================
// HOW TO USE
// $t_test_fields = array(
// 	array('type' => 'text', 'title' => 'Text label', 'description' => 'Some description'),
// 	array('type' => 'text', 'title' => 'Text label 2', 'description' => 'Some description 2', 'name' => 'textlbl2'),
// 	array('type' => 'email', 'title' => 'Sample email', 'description' => 'Email description'),
// 	array('type' => 'select', 'title' => 'Sample select', 'description' => 'Some description 3', 'items' => array('option 1', 'option 2')),
// 	array('type' => 'checkbox', 'title' => 'Sample checkbox'),
// 	array('type' => 'table', 'title' => 'Sample table', 'description' => 'Type table items', 'items' => array('col 1', 'column 2')),
// 	array('type' => 'textarea', 'title' => 'Sample textarea', 'description' => 'Type some text to textarea')
// 	);
// $GLOBALS['t_test'] = new Taxonomy('session', 'Project', array(), $t_test_fields);
// =========================================================

class Taxonomy extends Factory{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $title;
	private $options;
	private $controls;
	private $name;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($post_type, $title,  $options = array(), $controls = array())
	{	
		$defaults = array(
            'hierarchical'   => true,
            'label'          => $title.'s',    
            'singular_name'  => $title,        
            'show_ui'        => true,
            'query_var'      => true,
            'rewrite'        => array('slug' => $this->formatName($title)));

		$this->options   = array_merge($defaults, $options);
		$this->name      = $this->options['rewrite']['slug'];
		$this->post_type = $post_type;
		$this->controls  = $controls;								
		$this->init(array(&$this, 'registerTaxonomy'));

		// =========================================================
		// HOOKS
		// =========================================================        
        if(is_a($this->controls, 'Factory\Controls\ControlsCollection'))
        {

            if($this->controls->getCount() > 0)
            {           
                add_action($this->name.'_edit_form_fields', array(&$this, 'editFormFields')); 
                add_action($this->name.'_add_form_fields', array(&$this, 'addFormFields'));
                add_action('edited_'.$this->name, array(&$this, 'save'));
                add_action('created_'.$this->name, array(&$this, 'save'));
                add_filter('deleted_term_taxonomy', array(&$this, 'delete'));
            }    
        }
		
		add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndStyles')); 
	}

	/**
	 * Get all field names
	 * @return mixed --- field names [array] | false [boolean]
	 */
	public function getFieldNames()
	{	
		$names = array();	
		if(!count($this->controls)) return false;		
		foreach ($this->controls as $field) 
		{			
			array_push($names, $field['name']);		
		}
		return $names;
	}

	/**
     * Add scripts and styles to admin panel
     */
    public function adminScriptsAndStyles()
    {        
    	wp_enqueue_style('taxonomy', $this->getFolderURL().'/css/taxonomy.css');
    }

    /**
     * Registers a new taxonomy, associated with the instantiated post type.
     */
    public function registerTaxonomy()
    {        
        register_taxonomy($this->name, $this->post_type, $this->options);
    }

    /**
     * Add form fields
     * @param object $term --- term object
     */
    public function addFormFields($term)
    {
        $controls = $this->controls->getControls();
    	foreach ($controls as $ctrl) 
    	{    	
            $tmp       = clone $ctrl;
            $tmp->name = $this->formatControlName($tmp->name);					
    		?>
    		<div class="form-field">
    			<label for="<?php echo $tmp->name; ?>"><?php _e($tmp->title); ?></label>
    			<?php echo $tmp->getHtml(); ?>
    			<p><?php echo $tmp->description; ?></p>
    		</div>
    		<?php
    		
    	}
    }

	/**
	* Edit form fields
	* @param object $term --- term object
	*/
    public function editFormFields($term)
    {
        $controls = $this->controls->getControls();    	
    	foreach ($controls as $ctrl) 
    	{    		
            $tmp       = clone $ctrl;
            $tmp->name = $this->formatControlName($tmp->name);  			
			$value = get_option(sprintf('tax_%s_%s', $term->term_id, $tmp->name));           
    		?>
    		<tr class="form-field">
				<th scope="row"><label for="<?php echo $tmp->name; ?>"><?php _e($tmp->title); ?></label></th>
				<td><?php echo $tmp->getHtml($value); ?><br>
				<span class="description"><?php echo $tmp->description; ?></span></td>
			</tr>    		
    		<?php
    		
    	}
    }

    /**
     * Save taxonomy
     * @param  integer $term_id --- term id
     */
    public function save($term_id) 
    {        
        $controls = $this->controls->getControls();             
        foreach ($controls as $ctrl) 
        {           
            $tmp       = clone $ctrl;
            $tmp->name = $this->formatControlName($tmp->name);
            if(isset($_POST[$tmp->name])) update_option(sprintf('tax_%s_%s', $term_id, $tmp->name), $_POST[$tmp->name]);
            else delete_option(sprintf('tax_%s_%s', $term_id, $tmp->name));
        }
    }

    /**
     * Delete taxonomy
     * @param  integer $term_id --- term id
     */
    public function delete($term_id) 
    {
    	if($_POST['taxonomy'] == $this->name)
    	{
            $controls = $this->controls->getControls();     
            foreach ($controls as $ctrl) 
            {           
                $tmp       = clone $ctrl;
                $tmp->name = $this->formatControlName($tmp->name);
                if(get_option(sprintf('tax_%s_%s', $term_id, $tmp->name))) delete_option(sprintf('tax_%s_%s', $term_id, $tmp->name));                
            }
    	}
    }

    /**
     * Get terms with additional fields
     * @param  array  $args --- properties
     * @return mixed
     */
    public function getTerms($args = array())
    {    	
    	$defaults = array(
    		'orderby'      => 'name', 
    		'order'        => 'ASC',
    		'hide_empty'   => false, 
    		'exclude'      => array(), 
    		'exclude_tree' => array(), 
    		'include'      => array(),
    		'number'       => '', 
    		'fields'       => 'all', 
    		'slug'         => '', 
    		'parent'       => '',
    		'hierarchical' => true, 
    		'child_of'     => 0, 
    		'get'          => '', 
    		'name__like'   => '',
    		'pad_counts'   => false, 
    		'offset'       => '', 
    		'search'       => '', 
    		'cache_domain' => 'core'); 
    	$args  = array_merge($defaults, $args);
    	$controls = $this->controls->getControls();    
    	$terms = get_terms(array($this->name), $args);

    	if($terms)
    	{
    		foreach ($terms as &$term) 
    		{                
                foreach ($controls as $ctrl) 
                {           
                    $tmp       = clone $ctrl;
                    $tmp->name = $this->formatControlName($tmp->name);
                    $term->meta[$tmp->name] = get_option(sprintf('tax_%s_%s', $term->term_id,  $tmp->name));
                }    			
    		}
    	}
    	return $terms;
    }

    /**
     * Get single term
     * @param  integer $id --- term id
     * @return mixed       --- term [object] | false [boolean]
     */
    public function getTerm($id)
    {
        $term     = get_term($id, $this->name);
        $controls = $this->controls->getControls(); 
        if(!is_wp_error($term))
        {
            foreach ($controls as $ctrl) 
            {           
                $tmp       = clone $ctrl;
                $tmp->name = $this->formatControlName($tmp->name);
                $term->meta[$tmp->name] = get_option(sprintf('tax_%s_%s', $term->term_id,  $tmp->name));
            }   
        }
        return $term;
    }
}