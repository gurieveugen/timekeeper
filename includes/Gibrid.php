<?php

class Gibrid{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $base_object;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	                                             
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
		else if(property_exists($this->base_object, $property))
		{
			return $this->base_object->$property;
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
    	else if(property_exists($this->base_object, $property))
    	{
    		$this->base_object->$property = $value;
    	}   	
    }

    /**
	 * Get elapsed time array
	 * @return array --- elapsed time
	 */
	public function getElapsedTime($time)
	{
		$time = intval($time);
		$res  = array(
			'year'   => 0,
			'month'  => 0,
			'day'    => 0,
			'hour'   => 0,
			'minute' => 0,
			'second' => 0
		);
	    $tokens = array(
	        31536000 => 'year',
	        2592000  => 'month',        
	        86400    => 'day',
	        3600     => 'hour',
	        60       => 'minute',
	        1        => 'second');

	    foreach ($tokens as $unit => $text) 
	    {
	        if ($time < $unit) continue;
	        $res[$text] = floor($time / $unit);  
	        $time = $time-($res[$text]*$unit);
	    }	    
	    return $res;
	}

	/**
	 * Get elapsed time string like 0000-00-00 00:00:00
	 * @return [type] [description]
	 */
	public function getElapsedTimeStr($time)
	{		
		$elapsed_time = $this->getElapsedTime($time);
		return sprintf('%04d-%02d-%02d %02d:%02d:%02d', 
					$elapsed_time['year'], 
					$elapsed_time['month'], 
					$elapsed_time['day'], 
					$elapsed_time['hour'], 
					$elapsed_time['minute'], 
					$elapsed_time['second']);
	}
}