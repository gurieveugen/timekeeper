<?php
namespace Factory\Controls;

class Textarea extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{	
		$defaults = array('cols' => 30, 'rows' => 10);
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
		return sprintf('<textarea name="%1$s" id="%1$s" class="%2$s" cols="%4$s" rows="%5$s">%3$s</textarea>', $this->name, $this->class, $value, $this->cols, $this->rows);
	}
}