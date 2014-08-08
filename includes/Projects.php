<?php

class Projects{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $tax_class;
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($tax_class = null)
	{
		$this->tax_class = $tax_class;
		// =========================================================
		// CHECK LOGGED IN
		// =========================================================
		$this->checkLoggedIn();			
	}

	/**
	 * Check logged in
	 */
	public function checkLoggedIn()
	{
		if(!is_user_logged_in()) wp_redirect(TimeKeeper::getPageLink(TimeKeeper::PAGE_SIGN_IN));
	}

	/**
	 * Get owner peojects
	 * @return mixed --- Row ID on successful update, false on failure.
	 */
	public function getOwnerProject()
	{
		$projects       = $this->tax_class->getTerms();
		$user_id        = get_current_user_id();
		$owner_projects = array();

		if(count($projects))
		{
			foreach ($projects as $project) 
			{
				if($user_id == intval($project->meta['session_owner']))
				{
					array_push($owner_projects, $project);
				}
			}
		}		
		return $owner_projects;
	}

	/**
	 * Get projects table
	 * @return string --- html code
	 */
	public function getTable($projects = -1)
	{
		$projects     = $projects == -1 ? $this->tax_class->getTerms() : $projects;		
		$table_head[] = array(
			'fields' => array(
				'#', 
				__('Name'),
				__('Description'), 
				__('Workers'), 
				__('Owner'), 
				__('Count'), 
				__('Create date'),
				__('Requests to join')
			)
		);		

		$active  	  = $this->getActive();			

		if(!$active) $table_head[0]['fields'][] = __('Buttons');

		if(!is_array($projects) || !count($projects)) return false;		
		foreach ($projects as $project) 
		{		
			$p            = new Project($project->term_id, $this->tax_class);
			$workers      = $p->exportWorkers();					
			$workers      = $this->wrapWorkers($workers);	
			$workers      = $workers != '' ? '<i class="fa fa-users"></i> '.$workers : '';
			$link         = get_term_link($project->slug, $project->taxonomy);
			$user         = get_user_by('id', $project->meta['session_owner']);
			$count_html   = '<a href="%s"><i class="fa fa-clock-o"></i> %s</a>';
			$display_name = '<i class="fa fa-user"></i> '.$user->data->display_name;
			$create_date  = '<i class="fa fa-calendar-o"></i> '.$project->meta['session_create_date'];
			$row_class    = '';
			
			if($active)
			{					
				if($active->term_id == $project->term_id)
				{
					$count     = sprintf($count_html, $link, $project->count);	
					$row_class = 'active';		
				}			
				else
				{
					$count = $project->count;	
				}

				$table_body[] = array(
				'fields' => array(
					$project->term_id, 
					$project->name, 
					$project->description, 
					$workers, 
					$display_name, 
					$count, 
					$create_date,	
					$p->getJoinUsers(TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS))
					), 
				'class'      => $row_class,
				'data-owner' => $user->ID
				);
				$colgroup = array(
					array('width' => 20),
					array('width' => 80),
					array('width' => 300),
					array('width' => 150),
					array('width' => 100),
					array('width' => 20),
					array('width' => 150),			
					array('width' => 100)
				);	
			}
			else
			{
				$count = sprintf($count_html, $link, $project->count);	
				$table_body[] = array(
				'fields' => array(
					$project->term_id, 
					$project->name, 
					$project->description, 
					$workers, 
					$display_name, 
					$count, 
					$create_date,
					$p->getJoinUsers(TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)),
					$this->getAllowedButtons($p)
					), 
				'class'      => $row_class,
				'data-owner' => $user->ID
				);	
				
				$colgroup = array(
					array('width' => 20),
					array('width' => 80),
					array('width' => 300),
					array('width' => 150),
					array('width' => 100),
					array('width' => 20),
					array('width' => 150),			
					array('width' => 100),
					array('width' => 130)
				);	
			}
		}

		
		$table_generator = new TableGenerator($table_head, $table_body, $colgroup);
		echo $table_generator->getTable();			
	}

	/**
	 * Logout current user
	 */
	public function logout()
	{
		wp_logout();
		wp_redirect(get_bloginfo('url'));				
	}

	/**
	 * Get allowed buttons
	 * @param  object $project --- project 
	 * @return string          --- HTML buttons code
	 */
	public function getAllowedButtons($project)
	{	
		$project_owner   = intval($project->meta['session_owner']);		
		
		return $this->getEditButton($project->term_id, $project_owner).
				$this->getRemoveButton($project->term_id, $project_owner).
				$this->getJoinButton($project);
	}

	/**
	 * Get edit button
	 * @param  integer $project_id    --- project ID
	 * @param  integer $project_owner --- owner id
	 * @return string                 --- HTML button code
	 */
	public function getEditButton($project_id, $project_owner)
	{
		if($project_owner == get_current_user_id())
		{
			return sprintf('<a href="%s" class="btn btn-default">%s</a>', add_query_arg('id', $project_id, TimeKeeper::getPageLink(TimeKeeper::PAGE_EDIT_PROJECT)), __('Edit'));
		}
		return sprintf('<a href="#" class="btn btn-default disabled" role="disabled" >%s</a>', __('Edit'));
	}

	/**
	 * Get remove button
	 * @param  integer $project_id    --- project ID
	 * @param  integer $project_owner --- owner id
	 * @return string                 --- HTML button code
	 */
	public function getRemoveButton($project_id, $project_owner)
	{
		if($project_owner == get_current_user_id())
		{
			return sprintf(
				'<a href="#modal-remove-project" data-href="%s" class="btn btn-danger boxer">%s</a>', 
				add_query_arg(
					array(
						'action' => 'removeProject',
						'id'     => $project_id
					),
					TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
				), 
				__('Remove')
			);
		}
		return sprintf('<a href="#" class="btn btn-danger button-remove-project disabled" role="disabled">%s</a>', __('Remove'));
	}

	/**
	 * Get remove button
	 * @param  integer $project       --- project object
	 * @return string                 --- HTML button code
	 */
	public function getJoinButton($project)
	{			
		if(!$project->isAllowWorker() AND !$project->isJoined())
		{
			return sprintf(
				'<a href="#modal-join" data-title="%s" data-href="%s" class="btn btn-primary button-join boxer">%s</a>', 
				$project->name, 
				add_query_arg(
					array(
						'action' => 'joinToProject',
						'id'     => $project->term_id 
					), 
					TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
				), 
				__('Join')
			);
		}
		return sprintf('<a href="#" class="btn btn-primary disabled" role="disabled">%s</a>', __('Join'));
	}

	/**
	 * Get Join modal
	 * @return string --- HTML code
	 */
	public function getJoinModal()
	{
		?>
		<div id="modal-join" style="display: none;">
		    <div class="content modal-primary" style="width: 500px">
		    	<form action="#">
			    	<header>
			    		<h1><?php _E('Join'); ?></h1>	
			    	</header>
			    	<section>
			    		<?php _E('Do you really want join to this project?'); ?>
			    		<div class="clear"></div>
			    		<br>
			    		<input type="text" name="cost" placeholder="<?php _E('Enter your cost per hour of work'); ?>" value="" id="modal-join-cost" autocomplete="off">		    		
			    	</section>
			    	<footer>
			    		<button class="btn btn-primary modal-join-ok" type="submit" onclick="join(event);"><?php _E('Join'); ?></button>
			    		<button class="btn btn-default button-close-modal" onclick="closeBoxer(event);"><?php _E('Cancel'); ?></button>		    		
			    	</footer>
		    	</form>
		    </div>
		</div>
		<?php
	}

	/**
	 * Get Allow Join modal
	 * @return string --- HTML code
	 */
	public function getAllowJoinModal()
	{
		?>
		<div id="modal-allow-join" style="display: none;">
		    <div class="content modal-success" style="width: 500px">		    	
		    	<form action="#">
			    	<header>
			    		<h1><?php _E('Allow join'); ?></h1>	
			    	</header>
			    	<section>
			    		<?php _E('Do you really want allow this user to work with you?'); ?>
			    	</section>
			    	<footer>
			    		<button class="btn btn-success modal-allow-join-ok" type="submit" onclick="allowJoin(event);"><?php _E('Allow'); ?></button>
			    		<button class="btn btn-default button-close-modal" onclick="closeBoxer(event);"><?php _E('Cancel'); ?></button>		    		
			    	</footer>	
		    	</form>	    	
		    </div>
		</div>
		<?php
	}

	/**
	 * Get Remove project modal
	 * @return string --- HTML code
	 */
	public function getRemoveProjectModal()
	{
		?>
		<div id="modal-remove-project" style="display: none;">
		    <div class="content modal-danger" style="width: 500px">		    	
		    	<form action="#">
			    	<header>
			    		<h1><?php _E('Remove project'); ?></h1>	
			    	</header>
			    	<section>
			    		<?php _E('You realy want remove this project?'); ?>
			    	</section>
			    	<footer>
			    		<button class="btn btn-danger modal-allow-join-ok" type="submit" onclick="removeProject(event);"><?php _E('Remove'); ?></button>
			    		<button class="btn btn-default button-close-modal" onclick="closeBoxer(event);"><?php _E('Cancel'); ?></button>		    		
			    	</footer>	
		    	</form>	    	
		    </div>
		</div>
		<?php
	}

	/**
	 * Get active project
	 * @return mixed --- Active project [object] | false
	 */
	public function getActive()
	{
		$active = Session::getActive();
		if($active) return $active->getProject();
		return false;
	}

	/**
	 * Wrap workers 
	 * @param  array $workers --- workers
	 * @return string         --- wrapped string
	 */
	public function wrapWorkers($workers)
	{
		return $workers ? implode(', ', $workers) : '';
	}
}