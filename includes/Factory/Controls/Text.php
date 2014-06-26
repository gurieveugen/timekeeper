<?php
namespace Factory\Controls;

class Text extends Control{	
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
	}

	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHtml($value = '')
	{
		return sprintf('<input type="%1$s" name="%2$s" id="%2$s" value="%3$s" class="%4$s" />', $this->input_type, $this->name, $value, $this->class);
	}
}