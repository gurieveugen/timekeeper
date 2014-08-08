<?php

class Authorization{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $response;
	private $msg;
	private $msg_class;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($response)
	{
		$this->response  = $response;
		$this->msg_class = 'hide';
		if(is_user_logged_in()) wp_redirect(get_bloginfo('url'));
	}

	/**
	 * User sign in
	 */
	public function signIn()
	{
		if(count($this->response) == 0) return false;
		$this->response['remember'] = isset($this->response);
		$user                       = wp_signon($this->response, false);
		if(is_wp_error($user))
		{		
			$this->msg       = sprintf('Invalid username. <a href="%s">%s</a>', TimeKeeper::getPageLink(TimeKeeper::PAGE_LOST_PASSWORD), __('Lost your password?'));
			$this->msg_class = '';
		}
		else
		{				
			wp_redirect(get_bloginfo('url'));
		}
	}

	/**
	 * User sign up
	 */
	public function signUp()
	{	
		if(count($this->response) == 0) return false;	
		$user_id  = username_exists($this->response['user_login']);
		$email_id = email_exists($this->response['user_email']);

		if($user_id) 
		{
			$this->msg       = 'That LOGIN is registered! Try entering a different username.';
			$this->msg_class = '';
			return false;
		}

		if($email_id) 
		{
			$this->msg       = 'That EMAIL is registered! Try entering a different email.';
			$this->msg_class = '';
			return false;
		}

		$user_id = wp_create_user($this->response['user_login'], $this->response['user_password'], $this->response['user_email']);
		if(is_wp_error($user_id)) 
		{
    		$this->msg       = $user_id->get_error_message();
    		$this->msg_class = '';
    		return false;
		}
		else
		{
			wp_redirect(TimeKeeper::getPageLink(TimeKeeper::PAGE_SIGN_IN));
		}
	}

	/**
	 * Get response
	 * @return mixed --- [array] | null
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Get old user name from response.
	 * @return string --- user name
	 */
	public function getUserName()
	{
		return isset($this->response['user_login']) ? $this->response['user_login'] : '';
	}

	/**
	 * Get message
	 * @return string --- error message
	 */
	public function getMsg()
	{
		return $this->msg;
	}

	/**
	 * Get message css class
	 * @return string --- css class
	 */
	public function getMsgClass()
	{
		return $this->msg_class;
	}
}