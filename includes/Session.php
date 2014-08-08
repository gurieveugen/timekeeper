<?php

class Session extends Gibrid{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const TIMER_START = 'session_start_time';
	const TIMER_STOP  = 'session_stop_time';
	const SPEND_TIME  = 'session_spend_time';
	const PROJECT_TAX = 'project';
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($session_id)
	{		
		$this->base_object = get_post($session_id);
	}

	/**
	 * Get session post
	 * @return mixed --- post [object] | null
	 */
	public function getSession()
	{
		return $this->base_object;
	}

	/**
	 * Get seesion project	 
	 * @return mixed --- project [object] | false [boolean]
	 */
	function getProject()
	{
		$terms = wp_get_post_terms($this->base_object->ID, self::PROJECT_TAX);
		if($terms)
		{
			return $terms[0];
		}
		else return false;
	}

	/**
	 * Get start time from session
	 * @return mixed --- start time [string] | false [boolean]
	 */
	public function getStartTime()
	{
		return get_post_meta($this->base_object->ID, self::TIMER_START, true);
	}

	/**
	 * Set start time for session
	 * @param string $time --- start time
	 */
	public function setStartTime($time)
	{
		update_post_meta($this->base_object->ID, self::TIMER_START, $time);
	}

	/**
	 * Get stop time from session
	 * @return mixed --- stop time [string] | false [boolean]
	 */
	public function getStopTime()
	{
		return get_post_meta($this->base_object->ID, self::TIMER_STOP, true);
	}

	/**
	 * Set stop time for session
	 * @param string $time --- stop time
	 */
	public function setStopTime($time)
	{
		update_post_meta($this->base_object->ID, self::TIMER_STOP, $time);
	}

	/**
	 * Get spend time from session
	 * @return mixed --- spend time [string] | false [boolean]
	 */
	public function getSpendTime()
	{
		return get_post_meta($this->base_object->ID, self::SPEND_TIME, true);
	}

	/**
	 * Set spend time for session
	 * @param string $time --- spend time
	 */
	public function setSpendTime($time)
	{
		update_post_meta($this->base_object->ID, self::SPEND_TIME, $time);
	}
	
	/**
	 * Get active Session
	 * @return mixed --- post [object] | false [boolean]
	 */
	public static function getActive()
	{
		$args = array(
			'posts_per_page' => 1,			
			'author'         => get_current_user_id(),
			'orderby'        => 'post_date',
			'order'          => 'DESC',			
			'meta_query' => array(	
				'relation' => 'OR',			
				array(
					'key'     => self::TIMER_STOP,					
					'compare' => 'NOT EXISTS'
				),
				array(
					'key'     => self::TIMER_STOP,
					'compare' => 'IN',
					'value'   => array('')
				)	
			),
			'post_type'        => 'session',			
			'post_status'      => 'publish',
			'fields' 		   => 'ids',
			'suppress_filters' => true );		
		$active = get_posts($args);		
		
		if(count($active) == 0) return false;
		$session = new Session($active[0]);
		return $session;
	}	 

	/**
	 * Allow user to edit this Session
	 * @return boolean --- allow or no
	 */
	public function isOwner()
	{
		return (intval($this->post_author) == get_current_user_id());
	}
}