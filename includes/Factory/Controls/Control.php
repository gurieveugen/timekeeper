<?php
namespace Factory\Controls;

abstract class Control{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	protected $type;
	public $meta;	
	public $title;	

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{
		$defaults = array(
			'name'        => '',
			'class'       => 'widefat',
			'description' => '');

		$this->meta         = array_merge($defaults, $args);
		$this->title        = $title;
		$this->type         = get_called_class();		
		$this->meta['name'] = $this->meta['name'] ? $this->meta['name'] : $this->formatName($title);		
	}  

	/**
	 * Magic getter
	 * @param  string $property --- property to get
	 * @return mixed            --- property object
	 */
	public function __get($property)
    {
		if(property_exists($this, $property))
		{
			return $this->$property;
		}
		else
		{
			return isset($this->meta[$property]) ? $this->meta[$property] : null;
		}
    }

    /**
     * Magic setter
     * @param string $property --- property to set
     * @param mixed $value     --- value to set
     */
    public function __set($property, $value)
    {
    	if(property_exists($this, $property))
    	{
    		$this->$property = $value;
    	}
    	else
    	{
    		if(isset($this->meta[$property]))
    		{
    			$this->meta[$property] = $value;	
    		}
    	}
    }

	/**
	 * Get control type
	 * @return string --- control type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
     * Format name.
     * From: Some name
     * To:   some_name
     * @param  string $name --- entry name
     * @return string       --- exit name
     */
    public function formatName($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }

    /**
     * Get Factory folder url
     * @return string --- url
     */
    protected function getFolderURL()
    {
        $url = getCurrentUrl(__FILE__);  
        return dirname($url);
    }

	/**
	 * This function must be return control html code
	 * @return string --- html code
	 */
	abstract public function getHtml($value = '');

}