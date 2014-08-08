<?php

class Actions{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $error;
	private $request;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($request)
	{
		$this->request = $request;
		$this->error   = array();		
		$action        = $this->request['action'];
		if($action) $this->$action();
	}

	/**
	 * Not allowed to work on this project
	 */
	public function accessDeniedProject()
	{
		$this->error['title'] = 'ACCESS DENIED';
		$this->error['msg']   = 'You are not allowed to work on this project!';
	}

	/**
	 * Not allowed to work on this project
	 */
	public function accessDeniedAllow()
	{
		$this->error['title'] = 'ACCESS DENIED';
		$this->error['msg']   = 'You can not allow users to work on this project!';
	}

	/**
	 * Access denied session
	 */
	public function accessDeniedSession()
	{
		$this->error['title'] = 'ACCESS DENIED';
		$this->error['msg']   = 'You are not allowed to edit this session!';
	}

	/**
	 * Before Logout
	 * Show logout message
	 */
	public function showLogout()
	{
		$this->error['title'] = 'Logout';
		$this->error['msg']   = 
			sprintf(
				'You are attempting to log out of Time keeper! '."\n\n".
				'Do you really want to <a href="%s">log out</a>?',
				add_query_arg(
					'action',
					'logout',
					TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
				)
			);			
	}

	/**
	 * Logout
	 */
	public function logout()
	{		
		$projects = new Projects($GLOBALS['gc_session']['t']);
		$projects->logout();
	}

	/**
	 * Remove worker from the project
	 */
	public function removeWorker()
	{		
		$project = new Project($this->request['project_id'], $GLOBALS['gc_session']['t']);		
		if($project->removeWorker($this->request['user_id']))
		{
			wp_redirect(
				add_query_arg(
					'id',
					$this->request['project_id'],
					TimeKeeper::getPageLink(TimeKeeper::PAGE_EDIT_PROJECT)
				)
			);
		}

	}

	/**
	 * Join to project
	 */
	public function joinToProject()
	{
		$project = new Project($this->request['id'], $GLOBALS['gc_session']['t']);
		$project->join(intval($this->request['cost']));
	}

	/**
	 * Allow joined
	 */
	public function allowJoin()
	{
		$project = new Project($this->request['id'], $GLOBALS['gc_session']['t']);
		if($project->allowToJoin($this->request['user_id']))
		{
			wp_redirect('/');
		}
		else
		{
			wp_redirect(
				add_query_arg(
					'action', 
					'accessDeniedAllow', 
					TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
				)
			);
		}
	}

	/**
	 * Remove project
	 */
	public function removeProject()
	{
		$project = new Project($_GET['id'], $GLOBALS['gc_session']['t']);
		$project->remove();
	}

	/**
	 * Get error object
	 * @return string --- error object
	 */
	public function getError()
	{
		return $this->error;
	}
}