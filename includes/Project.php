<?php

class Project extends Gibrid{	
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const OWNER_OPTION       = 'session_owner';
	const CREATE_DATE_OPTION = 'session_create_date';  
	const FIELD_JOINS        = 'session_joins';    	
	const FIELD_WORKERS      = 'session_workers'; 
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	private $msg;
	private $msg_class;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($project_id, $taxonomy_class)
	{
		$this->base_object = $taxonomy_class->getTerm($project_id);
		$this->msg_class   = 'hide';
	}

	/**
	 * Get total time and money per each user
	 * @return mixed --- user meta data [array] | null 
	 */
	public function getTotal()
	{
		$users = $this->meta[self::FIELD_WORKERS];
		if(is_array($users))
		{
			foreach ($users as &$user) 
			{
				$user['time']  = $this->getTotalSpendTime($user['user']);
				$user['money'] = intval($user['time'])/3600*floatval($user['cost']); 
			}
		}
		return $users;	
	}

	/**
	 * Get total field
	 * @param  integer $user  --- if -1 function sum all users
	 * @param  string  $field --- field what need to sum
	 * @return float          --- summed value
	 */
	public function getTotalField($user_id = 0, $field = 'time')
	{
		$field_val = 0;
		$users = $this->getTotal();
		if(!is_array($users)) return 0;
		foreach ($users as $user) 
		{
			if($user_id == 0)
			{
				$field_val += floatval($user[$field]);
			}
			else
			{
				if($user_id == $user['user']) $field_val = $user[$field];
			}
		}
		
		return $field_val;
	}

	/**
	 * Get workers ID's
	 * @return mixed --- ID's [array] | false [boolean]
	 */
	public function getWorkers()
	{
		$res     = array();
		$workers = $this->meta[self::FIELD_WORKERS];

		if($workers)
		{
			foreach ($workers as $worker) 
			{
				array_push($res, $worker['user']);
			}
			return $res;
		}	
		return false;	
	}

	/**
	 * Get sessions table html
	 * @param array $args --- query arguments
	 * @return string --- html code
	 */
	public function getSessionsTable($args = array())
	{		
		$thead[] = array( 
		'fields' => array(
			'#', 
			__('Name'), 
			__('Description'), 
			__('Worker'), 
			__('Start time'), 
			__('Stop time'), 
			__('Spend time')));
		$tbody = array();

		$tg = new TableGenerator();
		$ss = $this->getSessions($args);
		if(is_array($ss) AND count($ss))
		{
			foreach ($ss as $session) 
			{
				$user = get_user_by('id',  $session->post_author);
				$tbody[]  = array(
					'fields' => array(
						$session->ID, 
						sprintf('<a href="%s">%s</a>', get_permalink($session->ID), $session->post_title), 
						sprintf('<div class="description">%s</div>', $session->post_content), 
						$user->data->display_name, 
						$session->getStartTime(), 
						$session->getStopTime(), 
						$session->getElapsedTimeStr($session->getSpendTime())
					)
				);	
			}
		}

		$tg->setHead($thead);
		$tg->setBody($tbody);

		return $tg->getTable();
	}

	/**
	 * Get Session pagination
	 * @return string --- html code
	 */
	public function getSessionPagination()
	{
		$page  = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$link  = get_term_link($this->term_id, $this->taxonomy);		
		$pages = ceil($this->count/intval(get_option('posts_per_page')));	
		if($pages)
		{
			$out = '<ul class="pagination">';
			// =========================================================
			// PREVIOUS BUTTON
			// =========================================================
			if($page > 1)
			{
				$out.= sprintf('<li class="prev"><a href="%s"><i class="fa fa-angle-left"></i></a>', $this->getPagenumLink($page-1));
			}
			else
			{
				$out.= '<li class="prev"><span><i class="fa fa-angle-left"></i></span></li>';
			}			

			for ($i=1; $i <= $pages; $i++) 
			{ 
				// =========================================================
				// IF IS ACTIVE ITEM
				// =========================================================
				if($i == $page)
				{
					$out.= sprintf('<li class="active"><span>%s</span></li>', $i);	
				}
				// =========================================================
				// IF IS NOT ACTIVE ITEM
				// =========================================================
				else
				{
					$out.= sprintf('<li><a href="%s">%s</a></li>', $this->getPagenumLink($i), $i);
				}				
			}

			// =========================================================
			// NEXT BUTTON
			// =========================================================
			if($page < $pages)
			{
				$out.= sprintf('<li clsas="next"><a href="%s"><i class="fa fa-angle-right"></i></a>', $this->getPagenumLink($page+1));
			}
			else
			{
				$out.= '<li clsas="next"><span><i class="fa fa-angle-right"></i></span></li>';
			}

			$out.= '</ul>';
		}
		return $out;
	}

	private function getPagenumLink($index)
	{
		$link = get_term_link($this->term_id, $this->taxonomy);		
		return sprintf('%spage/%s', $link, $index);
	}

	/**
	 * Get sessions from this project
	 * @param  array  $args --- query properties
	 * @return mixed        --- objects [array] | false [boolean]
	 */
	public function getSessions($args = array())
	{
		$defaults = array(
			'posts_per_page'   => get_option('posts_per_page'),
			'offset'           => 0,			
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'session',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true,
			'tax_query' 	   => array(
				array(
					'taxonomy' => 'project',
					'field'    => 'id',
					'terms'    => array($this->term_id)
				)
		));

		$args = array_merge($defaults, $args);		
		$sessions = get_posts($args);
		if(is_array($sessions) AND count($sessions))
		{
			foreach ($sessions as &$session) 
			{
				$session = new Session($session->ID);				
			}
			return $sessions;
		}
		

		return false;
	}

	/**
	 * Get total spend time
	 * @return integer --- total spend time
	 */
	public function getTotalSpendTime($author = '')
	{
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,			
			'orderby'          => 'post_date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'session',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => true,
			'tax_query' 	   => array(
				array(
					'taxonomy' => 'project',
					'field' => 'id',
					'terms' => array($this->term_id)
				)
			));
		if($author != '') $args['author'] = $author;		
		$sessions = get_posts($args);
		if(!$sessions) return 0;
		foreach ($sessions as $session) 
		{
			$tmp    = new Session($session->ID);
			$total += $tmp->getSpendTime();
		}
		return $total;
	}

	/**
	 * Get project term
	 * @return mixed --- term [object] | null
	 */
	public function getProject()
	{
		return $this->base_object;
	}

	/**
	 * Allowed, whether the user is working on this project
	 * @param  integer $user_id --- user id (optional)
	 * @return                  --- allowed or not allowed [boolean]
	 */
	public function isAllowWorker($user_id = 0)
	{		
		if(!$user_id) $user_id = get_current_user_id();
		$allow   = false;		
		if(!isset($this->base_object->meta['session_workers']) OR $this->base_object->meta['session_workers'] === false) return false;
		foreach ($this->base_object->meta['session_workers'] as $worker) 
		{			
			if($worker['user'] == $user_id) $allow = true;
		}
		return $allow;
	}	

	/**
	 * You are create this project?
	 * @param  integer $user_id --- user ID to check
	 * @return boolean          --- owner or no
	 */
	public function isOwner($user_id = 0)
	{
		if(!$user_id) $user_id = get_current_user_id();
		if(intval($this->meta['session_owner']) == $user_id) return true;
		return false;
	}

	/**
	 * Check actual this active project
	 * @param  integer $project_id --- project to check
	 * @return mixed               --- null - if don't have active project
	 *                                 true - if this actual project
	 *                                 false - if this not actual project
	 */
	public function isActual()
	{
		$active = Session::getActive();
		// =========================================================
		// Don't have active session ?
		// =========================================================
		if(!$active) return null;
		$active_project = $active->getProject();
		// =========================================================
		// Session don't have project?
		// Yeah! I'm paranoic
		// =========================================================
		if(!$active_project) return null;	
		// =========================================================
		// Project is actual?
		// =========================================================		
		if($active_project->term_id == $this->term_id) return true;
		return false;
	}

	/**
	 * Wrap timer panel to html code
	 * @param  array  $args --- timer
	 * @return mixed        --- html code [string] | false [boolean]
	 */
	public function wrapPanelTimer($args = array())
	{		
		$defaults = array(			
			'small' => array(
				'hours'   => 0,
				'minutes' => 0,
				'seconds'  => 0),
			'big'   => array(
				'days'    => 0,
				'hours'   => 0,
				'minutes' => 0)
			);


		$args = array_merge($defaults, $args);		
		if(!count($args)) return false;
		ob_start(); 
		?>
		<div id="time-panel-time" class="time-panel">
		<?php 
		foreach ($args as $timer_name => $timer) 
		{			
			?>
			<div class="<?php echo strtolower($timer_name); ?>">	
			<?php
			foreach ($timer as $el => $val) 
			{
				$separator = $el != end(array_keys($timer)) ? 'separator' : '';
				?>
				<div class="<?php echo $el; ?>">
					<span class="time <?php echo $separator; ?>"><?php printf('%02d', $val); ?></span>
					<span class="description"><?php _E(ucwords($el)); ?></span>
				</div>
				<?php
			}
			?>
			</div>
			<?php
		}
		?>
		</div>
		<?php
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}     

	/**
	 * Wrap money panel to html code
	 * @param  array  $args --- money
	 * @return mixed        --- html code [string] | false [boolean]
	 */
	public function wrapPanelMoney($args = array())
	{
		$defaults = array(			
			'small' => 0,
			'big'   => 0
			);
		$args = array_merge($defaults, $args);		
		if(!count($args)) return false;
		ob_start(); 
		?>
		<div id="time-panel-money" class="time-panel hide">
		<?php 
		foreach ($args as $name => $val) 
		{			
			?>
			<div class="<?php echo $name; ?>">
				<div class="money">
					<i class="fa fa-dollar"></i>	
					<span class="digits"><?php printf('%.2F', $val); ?></span>
				</div>
			</div>
			<?php
		}
		?>
		</div>
		<?php
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}       

	/**
	 * Export workers array
	 * with cost and ID	
	 * @return mixed      --- Workers [array] | false [boolean]
	 */
	public function exportWorkers()
	{	
		if(!$this->meta['session_workers']) return false;

		$res = array();
		foreach ((array)$this->meta['session_workers'] as $val) 
		{		
			$u = get_user_by('id', $val['user']);
			
			if($u)
			{
				$res[$u->data->ID] = sprintf('%s (<b>%s $</b>)', $u->data->display_name, $val['cost']);
			}
		}
		return $res;
	}    

	/**
	 * Get Project edit form
	 * @param  array $args --- from options
	 * @return string      --- HTML code
	 */
	public function editForm($args = array())
	{
		if(is_wp_error($this->base_object))
		{
			$defaults = array(
				'title'       => 'New project',
				'msg'         => $this->msg,
				'msg_class'   => $this->msg_class,
				'name'        => '',
				'description' => ''
			);	
		}
		else
		{
			$defaults = array(
				'title'       => 'Edit project',
				'msg'         => $this->msg,
				'msg_class'   => $this->msg_class,
				'name'        => $this->name,
				'description' => $this->description
			);	
		}
		

		$args = array_merge($defaults, $args);
		extract($args);
		ob_start();
		get_header('form');		
		?>
		<div class="form form-small">
			<form accept-charset="utf-8" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
				<h1><?php _E('New project'); ?></h1>
				<div class="bs-callout bs-callout-danger <?php echo $msg_class; ?>">
					<h4>Error</h4>
					<p>
						<?php echo $msg; ?>
					</p>
				</div>		
				<input type="text" name="title" required placeholder="Title" value="<?php echo $name; ?>">
				<div class="ctrl-group">			
					<textarea name="description" id="description" cols="30" rows="10" placeholder="Project description" required><?php echo $description; ?></textarea>	
				</div>	
				<div class="ctrl-group">
					<?php echo $this->getRemoveWorkersPanel(); ?>
					<div class="clear"></div>
				</div>			
				<div class="ctrl-group">			
					<button class="btn btn-blue" type="submit" name="done"><?php _E('Done'); ?></button>			
					<a href="/" class="btn btn-default"><?php _E('Cancel'); ?></a>			
				</div>
			</form>	
		</div>		
		<a href="http://www.gurievcreative.com" class="logo" ><img src="<?php echo TDU; ?>/images/logo.png" alt="Guriev Creative" ></a>
		<div id="modal-remove-worker" style="display: none;">
		    <div class="content modal-danger" style="width: 500px">		    	
		    	<form action="#">
			    	<header>
			    		<h1><?php _E('Remove worker from project'); ?></h1>	
			    	</header>
			    	<section>
			    		<?php _E('You realy want remove this worker?'); ?>
			    	</section>
			    	<footer>
			    		<button class="btn btn-danger modal-allow-join-ok" type="submit" onclick="removeWorker(event);"><?php _E('Remove'); ?></button>
			    		<button class="btn btn-default button-close-modal" onclick="closeBoxer(event);"><?php _E('Cancel'); ?></button>		    		
			    	</footer>	
		    	</form>	    	
		    </div>
		</div>
		<?php 
		get_footer();		
		$var = ob_get_contents();
		ob_end_clean();
    	return $var;
	}

	/**
	 * Get remove workers panel
	 * User icon | Display name | Remove button
	 * @return string --- HTML code
	 */
	public function getRemoveWorkersPanel()
	{
		ob_start();
		$workers = $this->meta[self::FIELD_WORKERS];
		if($workers AND is_array($workers))
		{			
			foreach ($workers as $worker) 
			{
				$user = get_user_by('id', $worker['user']);
				$cost = floatval($worker['cost']);
				$url  = add_query_arg(
					array(
						'action' => 'removeWorker',
						'project_id' => $this->term_id,
						'user_id' => $user->data->ID
					), 
					TimeKeeper::getPageLink(TimeKeeper::PAGE_ACTIONS)
				);				
				?>
				<div class="worker-field">
					<i class="fa fa-user"></i> <?php echo $user->data->display_name; ?> (<b><?php echo $cost; ?></b> $)
					<a href="#modal-remove-worker" class="boxer" data-href="<?php echo $url; ?>">×</a>
				</div>
				<?php
			}
		}
		$var = ob_get_contents();
		ob_end_clean();
    	return $var;
	}

	/**
	 * Save editted project
	 */
	public function save()
	{
		if(isset($_POST['done']))
		{					
			// =========================================================
			// CREATE NEW
			// =========================================================
			if(is_wp_error($this->base_object))
			{
				$res = wp_insert_term(
					$_POST['title'], 
					'project', 
					array('description' => $_POST['description'])
				);

				if(is_wp_error($res)) 
				{
					$this->msg       = $res->get_error_message();
					$this->msg_class = '';
				}	
				else
				{		
					update_option(sprintf('tax_%s_%s', $res['term_id'], self::OWNER_OPTION), get_current_user_id());
					update_option(sprintf('tax_%s_%s', $res['term_id'], self::CREATE_DATE_OPTION), date('Y-m-d H:i:s'));
					wp_redirect('/');
				}
			}
			// =========================================================
			// UPDATE OLD
			// =========================================================
			else
			{				
				if($this->isOwner())
				{					
					$this->name        = $_POST['title'];
					$this->description = $_POST['description'];				
					
					$res = wp_update_term($this->term_id, $this->taxonomy, array(
					  'name'        => $this->name,
					  'description' => $this->description
					));


					if(is_wp_error($res)) 
					{
						$this->msg       = $res->get_error_message();
						$this->msg_class = '';
					}	
					else
					{	
						$this->saveMeta();
						wp_redirect('/');
					}
				}
				else
				{
					$this->msg 	     = 'You are not owner the project and this can not edit it!';
					$this->msg_class = '';
				}
			}
		}
	}

	public function saveMeta()
	{
		if(is_array($this->meta))
		{
			foreach ($this->meta as $key => $value) 
			{
				update_option(sprintf('tax_%s_%s', $this->term_id, $key), $value);
			}
		}
	}

	/**
	 * Remove project
	 */
	public function remove()
	{
		if($this->isOwner())
		{
			wp_delete_term($this->term_id, 'project');				
		} 
		wp_redirect('/');
	}

	/**
	 * Join user to project
	 */
	public function join($cost = 0)
	{		
		if(!$this->isJoined() AND !$this->isAllowWorker())
		{
			$joins                         = $this->meta[self::FIELD_JOINS];
			$joins[]                       = array('user_id' => get_current_user_id(), 'cost' => $cost);
			$this->meta[self::FIELD_JOINS] = $joins;
			update_option(sprintf('tax_%s_%s', $this->term_id, self::FIELD_JOINS), $joins);
		}
		wp_redirect('/');
	}

	/**
	 * Allow to join 
	 * @param  integer $user_id --- user to join
	 * @return boolean --- true | false
	 */
	public function allowToJoin($user_id)
	{
		if(!$this->isOwner()) return false;
		$user_id = intval($user_id);
		if($this->isJoined($user_id))
		{
			$joins   = $this->meta[self::FIELD_JOINS];
			$workers = $this->meta[self::FIELD_WORKERS];

			foreach ($joins as $key => $join) 
			{
				if(intval($join['user_id']) == $user_id) 
				{					
					$cost = intval($joins[$key]['cost']);
					unset($joins[$key]);					
				}
			}
			$this->meta[self::FIELD_JOINS] = $joins;
			$workers[] = array('user' => $user_id, 'cost' => $cost);
			$this->meta[self::FIELD_WORKERS] = $workers;
			update_option(sprintf('tax_%s_%s', $this->term_id, self::FIELD_JOINS), $joins);
			update_option(sprintf('tax_%s_%s', $this->term_id, self::FIELD_WORKERS), $workers);
			return true;
		}
		return false;
	}

	/**
	 * Remove worker from the project
	 * @param  integer $id --- user id
	 * @return mixed     
	 */
	public function removeWorker($id)
	{		
		if(!$this->isOwner()) return false;
		$workers = $this->meta[self::FIELD_WORKERS];
		if($workers AND is_array($workers))
		{			
			foreach ($workers as $key => $worker) 
			{
				if($worker['user'] == $id) unset($workers[$key]);
			}
		}
		return update_option(sprintf('tax_%s_%s', $this->term_id, self::FIELD_WORKERS), $workers);
	}

	/**
	 * This user is join to current project or no
	 * @return boolean --- joined or no
	 */
	public function isJoined($user_id = 0)
	{
		$user_id = $user_id ? $user_id : get_current_user_id();
		$joined  = $this->getJoinedUsers();
		if($joined) return in_array($user_id, $joined);
		return false;
	}

	/**
	 * Get all joined users to project
	 * @return array --- joined users
	 */
	public function getJoinedUsers()
	{
		$joins = $this->meta[self::FIELD_JOINS];
		if($joins)
		{
			foreach ($joins as $join) 
			{
				$arr[] = intval($join['user_id']);
			}
			return $arr;
		}
		return false;
	}

	/**
	 * Get request to join users
	 * @return string --- html code
	 */
	public function getJoinUsers($url)
	{		
		$str 	= array();
		$joined = $this->getJoinedUsers();
		if($joined)
		{	
			foreach ($joined as $user_id) 
			{
				$user  = get_user_by('id', $user_id);
				if($this->isOwner())
				{
					$str[] = sprintf(
						'<a href="#modal-allow-join" data-href="%s" class="boxer button-allow-to-join">%s</a>', 
						add_query_arg(
							array(
								'action'  => 'allowJoin',
								'id'      => $this->term_id, 
								'user_id' => $user_id
							), 
							$url
						), 
						$user->data->display_name);	
				}
				else
				{
					$str[] = $user->data->display_name;	
				}

				
			}
			return implode(', ', $str);
		}
		return '';
	}
}