<?php

class TimeKeeper{
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const OPTION_INSTALLED            = 'installed_time_keeper';
	const MENU_LOCATION_PRIMARY       = 'primary_nav';
	const MENU_LOCATION_PRIMARY_RIGHT = 'primary_nav_right';
	const PAGE_LOGOUT                 = 'logout';
	const PAGE_NEW_PROJECT            = 'new-project';
	const PAGE_MY_PROJECTS            = 'my-projects';
	const PAGE_LOST_PASSWORD          = 'lost-password';
	const PAGE_SIGN_IN                = 'sign-in';
	const PAGE_SIGN_UP                = 'sign-up';	
	const PAGE_ACTIONS                = 'actions';
	const PAGE_EDIT_PROJECT           = 'edit-project';

	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $menus;
	private $pages;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct()
	{

		$this->pages = array(
			self::PAGE_LOGOUT => array(
				'post_content' => 
					'You are attempting to log out of Time keeper'."\n\n".
					'Do you really want to <a href="?action=logout">log out</a>?',
				'post_name'      => self::PAGE_LOGOUT,
				'post_title'     => 'Logout',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-logout.php'
			),
			self::PAGE_NEW_PROJECT => array(
				'post_content'   => '',
				'post_name'      => self::PAGE_NEW_PROJECT,
				'post_title'     => 'New project',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-new-project.php'
			),
			self::PAGE_MY_PROJECTS => array(
				'post_content'   => '',
				'post_name'      => self::PAGE_MY_PROJECTS,
				'post_title'     => 'My projects',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-my-projects.php'
			),
			self::PAGE_SIGN_IN => array(
				'post_content'   => '',
				'post_name'      => self::PAGE_SIGN_IN,
				'post_title'     => 'Sign in',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-sign-in.php'
			),
			self::PAGE_SIGN_UP => array(
				'post_content'   => '',
				'post_name'      => self::PAGE_SIGN_UP,
				'post_title'     => 'Sign up',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-sign-up.php'
			),
			self::PAGE_ACTIONS => array(
				'post_content'   => '',
				'post_name'      => self::PAGE_ACTIONS,
				'post_title'     => 'Actions',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-join-to-project.php'
			),
			self::PAGE_EDIT_PROJECT => array(
				'post_content'   => '',
				'post_name'      => self::PAGE_EDIT_PROJECT,
				'post_title'     => 'Edit project',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'comment_status' => 'closed',
				'page_template'  => 'page-edit-project.php'
			)

		);

		if(!$this->isInstalled())
		{
			$this->install();
		}
	}

	/**
	 * Installed or no
	 * @return boolean --- yes or no
	 */
	public function isInstalled()
	{
		return (bool) get_option(self::OPTION_INSTALLED);
	}

	/**
	 * Install TimeKeeper data
	 */
	public function install()
	{
		$this->createPages();	
		$this->createMenus();	
		$this->setMenuLocation();

		update_option(self::OPTION_INSTALLED, true);		
	}

	/**
	 * Create menus
	 */
	private function createMenus()
	{
		$this->menus = array(
			'primary_nav' => array(
				'title' => 'Main menu',
				'items' => array(
					array(
						'menu-item-status'     => 'publish',
						'menu-item-title'      => 'Dashboard',
						'menu-item-url'        => '/',
						'menu-item-icon_class' => 'fa fa-tachometer',
					),
					array(
						'menu-item-status'     => 'publish',
						'menu-item-title'      => 'My projects',
						'menu-item-url'        => TimeKeeper::getPageLink(self::PAGE_MY_PROJECTS),
						'menu-item-icon_class' => 'fa fa-clock-o',
					),
					array(
						'menu-item-status'     => 'publish',
						'menu-item-title'      => 'New project',
						'menu-item-url'        => TimeKeeper::getPageLink(self::PAGE_NEW_PROJECT),
						'menu-item-icon_class' => 'fa fa-plus',
					)
				)
			),
			'primary_nav_right' => array(
				'title' => 'Main menu right',
				'items' => array(
					array(
						'menu-item-status'     => 'publish',
						'menu-item-title'      => 'Logout',
						'menu-item-url'        => add_query_arg(
								'action',
								'showLogout',
								TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
							),
						'menu-item-icon_class' => 'fa fa-power-off',
					)
				)
			)
		);

		foreach ($this->menus as &$menu) 
		{			
			$menu_exists = wp_get_nav_menu_object($menu['title']);			
			if(!$menu_exists)
			{
				$menu['id'] = wp_create_nav_menu($menu['title']);
				
				if(is_array($menu['items']))
				{
					foreach ($menu['items'] as $item) 
					{
						$item_id = wp_update_nav_menu_item($menu['id'], 0, $item);
						update_post_meta($item_id, '_menu_item_icon_class', $item['menu-item-icon_class']);
					}
				}
			}	
			else
			{
				$menu['id'] = $menu_exists->term_id;
			}	
		}		
	}

	/**
	 * Set menu to location
	 */
	private function setMenuLocation()
	{
		$locations = get_theme_mod('nav_menu_locations');
		foreach ($this->menus as $location => $menu) 
		{
			$locations[$location] = $menu['id'];
		}
		set_theme_mod('nav_menu_locations', $locations);
	}

	/**
	 * Create system pages
	 */
	private function createPages()
	{		
		foreach ($this->pages as $slug => $p) 
		{			
			if(!$this->isPageExists($p['post_name'])) 
			{ 
				$id = wp_insert_post($p); 
				if (is_wp_error($id))
				{
				   $error_string = $result->get_error_message();
				   echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';				   
				}								
			}
		}		
	}

	/**
	 * Get page permalink by slug
	 * @param  string $slug --- page slug
	 * @return string       --- url
	 */
	public static function getPageLink($slug)
	{
		$obj = get_page_by_path($slug);		
		return get_permalink($obj->ID);
	}

	/**
	 * Page exists
	 * @param  string $slug --- page slug
	 * @return boolean      --- true if exists | false if no
	 */
	private function isPageExists($slug)
	{
		global $wpdb;
		$res = $wpdb->get_row( "SELECT ID FROM wp_posts WHERE post_name = '".$slug."' && post_type = 'page' ", 'ARRAY_N');		
		return empty($res) ? false : true;
	}
}