<?php
namespace Factory\Controls;

class Select extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{
		$defaults = array('options' => null);
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
		$args = array(
			'name'    => $this->name,
			'id'      => $this->name,
			'current' => $value
			);
		return getSelectCtrl($args, $this->options);
	}
}