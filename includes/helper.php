<?php

//     __  __________    ____  __________     ________  ___   ______________________  _   _______
//    / / / / ____/ /   / __ \/ ____/ __ \   / ____/ / / / | / / ____/_  __/  _/ __ \/ | / / ___/
//   / /_/ / __/ / /   / /_/ / __/ / /_/ /  / /_  / / / /  |/ / /     / /  / // / / /  |/ /\__ \ 
//  / __  / /___/ /___/ ____/ /___/ _, _/  / __/ / /_/ / /|  / /___  / / _/ // /_/ / /|  /___/ / 
// /_/ /_/_____/_____/_/   /_____/_/ |_|  /_/    \____/_/ |_/\____/ /_/ /___/\____/_/ |_//____/  
                                                                                              

/**
 * Generate select control from array
 * @param  array $args   --- properties
 * @param  array $values --- select options
 * @return string        --- html code
 */
function getSelectCtrl($args, $values)
{
	if(!is_array($args) || !is_array($values)) return '';

	$defaults = array(
		'name'    => '',
		'id'      => '',
		'class'   => 'selecter',
		'current' => '');
	$args    = array_merge($defaults, $args);
	$options = '';

	extract($args);

	foreach ($values as &$value) 
	{
		$options.= sprintf('<option value="%1$s" %2$s>%1$s</option>', $value, selected($value, $current, false));
	}

	return sprintf('<select name="%s" id="%s" class="%s">%s</select>', $name, $id, $class, $options);
}

/**
 * Generate table from array
 * @param  array $body_items
 * @param  array $header_items
 * @param  array  $args
 * @return mixed --- html code [string] | false [boolean]
 */
function generateTable($body_items, $header_items = null, $args = array())
{
	if(!$body_items) return false;
	$defaults = array(
		'id'    => '',
		'class' => 'table w100p'
		);

	$args = array_merge($defaults, $args);
	extract($args);
	
	$middle = $header_items ? sprintf('<thead>%s</thead>', generateTableRows($header_items, array('col_container' => 'th'))) : '';
	$middle.= sprintf('<tbody>%s</tbody>', generateTableRows($body_items));
	$out    = sprintf('<table id="%s" class="%s">%s</table>', $id, $class, $middle);
	
    return $out;
}	

/**
 * Generate table rows from array
 * @param  array $arr  --- row items
 * @param  array $args --- wrap options 
 * @return mixed       --- string | boolean
 */
function generateTableRows($arr, $args = array())
{
	if(!$arr) return false;
	$defaults = array(
		'row_container' => 'tr',
		'col_container' => 'td'
		);
	$args = array_merge($defaults, $args);
	$out  = '';

	extract($args);
	foreach ($arr as $row) 
	{
		$our.= sprintf('<%s>', $row_container);		
		foreach ($row as $col) 
		{
			$out.= sprintf('<%1$s>%2$s</%1$s>', $col_container, $col);
		}
		$out.= sprintf('</%s>', $row_container);
	}
	return $out;
}

/**
 * Start session if his not started
 */
function _session_start()
{
	if(session_id() == '') session_start();
}
_session_start();

/**
 * Auto load all Factory classes
 */
function Autoloader($class) 
{		
	$path = sprintf('%s%s.php', plugin_dir_path(__FILE__), $class);		
	if (file_exists($path))
	{
		require_once $path;
    }	
}
spl_autoload_register('Autoloader');

/**
 * Get url from path
 * @param  string $file --- path
 * @return string       --- url
 */
function getCurrentUrl($file)
{
	// Get correct URL and path to wp-content
	$content_url = untrailingslashit(dirname(dirname(get_stylesheet_directory_uri())));
	$content_dir = untrailingslashit(dirname(dirname(get_stylesheet_directory())));

	// Fix path on Windows
	$file        = str_replace('\\', '/', $file);
	$content_dir = str_replace('\\', '/', $content_dir);

	$url = str_replace($content_dir, $content_url, $file);
	return $url;
}

