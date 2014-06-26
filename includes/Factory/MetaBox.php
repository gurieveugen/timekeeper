<?php
namespace Factory;

// =========================================================
// HOW TO USE
// $test_meta_box = new MetaBox('session', 'Test meta box', array(
//     'Text label'     => 'text',
//     'Email label'    => 'email',
//     'Checkbox'       => 'checkbox',
//     'Test image'     => 'file',
//     'Some table'     => array('table', array('col 1', 'col 2')),
//     'Textarea label' => 'textarea',
//     'Select label'   => array('select', array('option 1', 'option 2'))
//     ), 'test_meta', 'normal');
// =========================================================

class MetaBox extends Factory{
	//                                       __  _          
	//     ____  ____  _________  ___  _____/ /_(_)__  _____
	//    / __ \/ __ \/ ___/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /_/ / /  / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/\____/_/  / .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $title;
	private $controls;
	private $name;
	private $context;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	function __construct($post_type, $title, $controls = null, $name = null, $context = null)
	{
        $this->post_type = $post_type;		
        $this->title     = $title;
        $this->controls  = $controls;      
        $this->name      = $name ? $name : $this->formatName($title);
        $this->context   = $context ? $context : 'normal';
        $this->adminInit(array($this, 'configureMetaBox'));

        // =========================================================
        // HOOKS
        // =========================================================
        add_action('post_edit_form_tag', array(&$this, 'postEditFormTag'));  
	}

    /**
     * Add tag to form
     */
    public function postEditFormTag()
    {
        echo ' enctype="multipart/form-data"';
    }

	/**
	 * Configure meta box	 
	 */
	public function configureMetaBox()
    {   
    	add_action('save_post', array(&$this, 'savePost'));
    	$id = $this->name;        
        add_meta_box($id, $this->title, array(&$this, 'renderMetaBox'), $this->post_type, $this->context, 'default');

        add_filter('manage_edit-'.$this->post_type.'_columns', array(&$this, 'columnThumb'));   
        add_action('manage_'.$this->post_type.'_posts_custom_column', array($this, 'columnThumbShow'), 10, 2);           
    }

    /**
     * Register new columns
     * @param  array $columns 
     * @return array
     */
    public function columnThumb($columns)
    {
        $arr = array();
        $ctrls = $this->controls->getControls();
        if($ctrls)
        {
            foreach ($ctrls as $ctrl) 
            {
                $arr[$ctrl->name] = $ctrl->title;
            }
        }        
        
        return array_merge($columns, $arr);
    }

    /**
     * Display new column
     * @param  string  $column  
     * @param  integer $post_id           
     */
    public function columnThumbShow($column, $post_id)
    {          
        $display_types = array(
			"text"     => "%s",
			"email"    => "%s",
			"textarea" => "%s",
			"checkbox" => '<i class="fa %s"></i>',
			"select"   => '%s',
			"file"     => "%s",
            'table'    => "%s");
        $ctrl = $this->controls->getControlByName($column);
        if($ctrl)
        {
            $meta = get_post_meta($post_id, $this->formatControlName($column), true);
            $out  = is_array($meta) ? sprintf('items (%s)', count($meta)) : $meta; 
            $out  = $ctrl->getType() == 'checkbox' ? $this->circleCheckbox($meta) : $meta; 
            printf($display_types[$ctrl->getType()], $out);
        }
    }

    /**
     * Render meta box
     * PRINT CONTROLS HTML CODE
     * @param  object $post
     * @param  array $data
     */
    public function renderMetaBox($post)
    {
        wp_nonce_field(plugin_basename(__FILE__), 'jw_nonce');

        $meta  = get_post_custom($post->ID);       
        $ctrls = $this->controls->getControls();        

        if(!$ctrls) return '';       
        foreach ($ctrls as $ctrl) 
        {   
            $ctrl = clone $ctrl;
            $ctrl->name = $this->formatControlName($ctrl->name);
            $value      = isset($meta[$ctrl->name][0]) ? $meta[$ctrl->name][0] : ''; 
           
            ?>
            <p>
                <label for="<?php echo $ctrl->name; ?>"><?php echo $ctrl->title.':'; ?></label>
                <?php echo $ctrl->getHtml($value); ?>
            </p>            
            <p>
                <?php
                    if (strtolower($ctrl->getType()) === 'file') 
                    {                        
                        $file = get_post_meta($post->ID, $ctrl->name, true);
                        $file        = get_post_meta($post->ID, $id_name, true);
                        $file_type   = wp_check_filetype($file);
                        $image_types = array('jpeg', 'jpg', 'bmp', 'gif', 'png');
                        if (isset($file)) 
                        {
                            if (in_array($file_type['ext'], $image_types)) 
                            {
                                echo "<img src='$file' alt='' style='max-width: 400px;' />";
                            } 
                            else 
                            {
                                echo "<a href='$file'>$file</a>";
                            }
                        }
                    }
                ?>
            </p>
            <?php
            if($_SESSION['taxonomy_data'] == null) $_SESSION['taxonomy_data'] = array();
            array_push($_SESSION['taxonomy_data'], $ctrl->name);
        }
    }    

    /**
     * When a post saved/updated in the database, this methods updates the meta box params in the db as well.
     */
    public function savePost()
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        global $post;

        if ($_POST && !wp_verify_nonce($_POST['jw_nonce'], plugin_basename(__FILE__))) return;

        // Get all the form controls that were saved in the session,
        // and update their values in the db.
        if (isset($_SESSION['taxonomy_data'])) 
        {
            foreach ($_SESSION['taxonomy_data'] as $form_name) 
            {
                if (!empty($_FILES[$form_name]) ) 
                {
                    if ( !empty($_FILES[$form_name]['tmp_name']) ) 
                    {
                        $upload = wp_upload_bits($_FILES[$form_name]['name'], null, file_get_contents($_FILES[$form_name]['tmp_name']));

                        if (isset($upload['error']) && $upload['error'] != 0) 
                        {
                            wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                        } 
                        else 
                        {
                            update_post_meta($post->ID, $form_name, $upload['url']);
                        }
                    }
                } 
                else 
                {
                    // Make better. Have to do this, because I can't figure
                    // out a better way to deal with checkboxes. If deselected,
                    // they won't be represented here, but I still need to
                    // update the value to false to blank in the table. Hmm...
                    if (!isset($_POST[$form_name])) $_POST[$form_name] = '';
                    if (isset($post->ID) ) 
                    {                       
                        update_post_meta($post->ID, $form_name, $_POST[$form_name]);
                    }
                }
            }

            $_SESSION['taxonomy_data'] = array();

        }
    }
}