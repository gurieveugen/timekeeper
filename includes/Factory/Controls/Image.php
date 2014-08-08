<?php
namespace Factory\Controls;

class Image extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{
		$defaults = array('input_type' => 'text');
		$args     = array_merge($defaults, $args);
		parent::__construct($title, $args);		

		// =========================================================
		// HOOKS
		// =========================================================					
		add_action('admin_print_scripts', array(&$this, 'adminScripts'));
		add_action('admin_print_styles', array(&$this, 'adminStyles'));
	}

	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHtml($value = '')
	{		
		return sprintf('<div><input class="%3$s" id="%1$s" name="%1$s" type="text" value="%2$s" /></div><button type="button" onclick="setPhoto(this);" data-name="%1$s" class="button">%4$s</button>', $this->name, $value, $this->class, __('Select image'));		
	}

	/**
	 * Add scripts to admin panel	
	 */
	public function adminScripts()
	{
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('image-uploader', getCurrentUrl(dirname(dirname(__FILE__))).'/js/image.js', array('jquery','media-upload','thickbox'));		
	}

	/**
	 * Add styles to admin panel
	 */
	public function adminStyles()
	{
		wp_enqueue_style('thickbox');
	}
}