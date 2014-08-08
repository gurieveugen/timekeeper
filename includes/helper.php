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
		if(is_array($value))
		{
			$options.= sprintf('<option value="%1$s" %2$s>%3$s</option>', $value[0], selected($value[0], $current, false), $value[1]);
		}
		else
		{
			$options.= sprintf('<option value="%1$s" %2$s>%1$s</option>', $value, selected($value, $current, false));	
		}
		
	}

	return sprintf('<select name="%s" autocomplete="off" id="%s" class="%s">%s</select>', $name, $id, $class, $options);
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
function autoloader($class) 
{			
	$path = sprintf('%s%s.php', plugin_dir_path(__FILE__), $class);	
	$path = str_replace('\\', '/', $path);	
	
	if (file_exists($path))
	{
		require_once $path;
    }	
}
spl_autoload_register('autoloader');

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

/**
 * Fill array
 * @param  array $fields --- fields array
 * @param  array $arr    --- array with values
 * @return mixed         --- filled [array] | false [boolean]
 */
function arrayFill($fields, $arr)
{
	if(!$fields) return false;
	foreach ($fields as $field) 
	{
		$new_arr[$field] = isset($arr[$field]) ? $arr[$field] : '';
	}
	return $new_arr;
}


function isValidDateTime($dateTime)
{
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches)) {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    }

    return false;
}
