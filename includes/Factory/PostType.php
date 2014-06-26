<?php
namespace Factory;

// =========================================================
// HOW TO USE
// $sessions = new PostType('session', array(
// 	'icon_code' => 'f017',
// 	'supports'  => array('title', 'editor')
// 	));
// =========================================================

class PostType extends Factory{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $options;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($post_type, $options = array())
	{
		$this->post_type = $post_type;
		$n = ucwords($this->post_type);

        $defaults = array(
            'label'              => $n.'s',
            'singular_name'      => $n,
            'public'             => true,
            'publicly_queryable' => true,
            'query_var'          => true,            
            'rewrite'            => true,
            'capability_type'    => 'post',
            'hierarchical'       => true,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'thumbnail'),
            'has_archive'        => true);

		$this->options = array_merge($defaults, $options);

		if(!post_type_exists($this->post_type_name))
		{
		    $this->init(array(&$this, 'registerPostType'));    
		}

		add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndStyles'));
	}

	/**
     * Add scripts and styles to admin panel
     */
    public function adminScriptsAndStyles()
    {
        wp_enqueue_style('font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');        
    }

    /**
     * Registers a new post type in the WP db.
     */
    public function registerPostType()
    {        
        register_post_type($this->formatName($this->post_type), $this->options);

        if(isset($this->options['icon_code'])) add_action('admin_enqueue_scripts', array(&$this, 'addMenuIcon'));
    }

    /**
     * Add menu icon
     */
    public function addMenuIcon()
    {
        $n = str_replace(' ', '', $this->post_type);
        ?>
        <style>
            #adminmenu #menu-posts-<?php echo $n; ?> .wp-menu-image:before {
                content: "\<?php echo $this->options['icon_code']; ?>";  
                font-family: 'FontAwesome' !important;
                font-size: 18px !important;
                text-shadow: 0 1px 0 rgba(0, 0, 0, 0.7) !important;
            }
        </style>
        <?php
    }
}