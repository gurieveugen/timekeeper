<?php

class Timer{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $request;
	private $action;
	private $project;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($request, $project)
	{
		$this->request   = $request;
		$this->action    = null;
		$allowed_actions = array('start', 'stop');
		$this->project   = $project;	

		// =========================================================
		// INITIALIZE ACTION TYPE
		// =========================================================
		if(isset($this->request['action']))
		{
			foreach ($allowed_actions as $action) 
			{
				if(strtolower($this->request['action']) == $action) $this->action = strtolower($this->request['action']);
			}
		}	
		$this->execute();			
	}

	/**
	 * Start/Stop timer
	 * @return mixed --- action type | false [boolean]
	 */
	public function execute()
	{
		if(!$this->action) return false;
		$action = $this->action;
		$this->$action();
		return $action;
	}

	/**
	 * Start session
	 */
	public function start()
	{	
		if(!Session::getActive())
		{
			$p = array(
				'post_title'    => sprintf('Session [%s]', $this->project->count+1),
				'post_status'   => 'publish',
				'post_type'     => 'session');
			$res = wp_insert_post($p);			
			if($res)
			{
				wp_set_object_terms($res, $this->project->term_id, $this->project->taxonomy);
				update_post_meta($res, Session::TIMER_START, date('Y-m-d H:i:s'));				
			}		
		}
	}

	/**
	 * Stop session
	 */
	public function stop()
	{
		$active = Session::getActive();				
		if($active)
		{
			$timer_start = $active->getStartTime();
			$timer_stop  = date('Y-m-d H:i:s');			

			$active->setSpendTime((strtotime($timer_stop) - strtotime($timer_start)));
			$active->setStopTime($timer_stop);			
		}
	}

	/**
	 * is mode total
	 * @return boolean --- true if mode == total | false if not
	 */
	public function isTotalMode()
	{
		return isset($_GET['mode']) AND strtolower($_GET['mode']) == 'total';
	}

	/**
	 * Get execute button
	 * @return string --- html code button
	 */
	public function getButton()
	{
		$self_url = $_SERVER['REQUEST_URI'];
		if(!Session::getActive())
		{
			return sprintf(
				'<a href="%s" class="btn btn-green">Start</a>', 
				add_query_arg('action', 'start', $self_url)				
			);
		}
		return sprintf(
			'<a href="%s" class="btn btn-red">Stop</a>', 
			add_query_arg('action', 'stop', $self_url)				
		);		
	}

	/**
	 * Get total button
	 * @return string --- HTML code button
	 */
	public function getTotalButton()
	{
		$active   = '';
		$url      = $_SERVER['REQUEST_URI'];
		$template = '<a id="total" href="%s" class="btn btn-total %s">Total</a>';
		
		if($this->isTotalMode())
		{
			$active = 'active';				
			return sprintf(
				$template, 
				add_query_arg('mode', 'single', $url),
				$active
			);
		}
					
		return sprintf(
			$template, 
			add_query_arg('mode', 'total', $url),
			$active
		);
	}

	/**
	 * Get start DateTime
	 * @return mixed --- date time [string] | false [boolean]
	 */
	public function getStartTime()
	{
		$active = Session::getActive();	
		if($active)
		{			
			return $active->getStartTime();
		}
		return false;
	}
}