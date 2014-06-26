<?php
namespace Factory\Controls;

class Table extends Control{	
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($title, $args = array())
	{
		$defaults = array('columns' => null);
		$args     = array_merge($defaults, $args);
		parent::__construct($title, $args);		
		// =========================================================
		// HOOKS
		// =========================================================					
		add_action('admin_enqueue_scripts', array(&$this, 'adminScriptsAndStyles'));  
	}

	/**
     * Add scripts and styles to admin panel
     */
    public function adminScriptsAndStyles()
    {        
        wp_enqueue_script('table', getCurrentUrl(dirname(dirname(__FILE__))).'/js/table.js', array('jquery'));
    }


	/**
	 * Get html code
	 * @param  string $value --- value
	 * @return string        --- HTML code
	 */
	public function getHtml($value = '')
	{
		if(!$this->columns) return '';

		$thead     = '';
		$tbody     = ''; 
		$rows      = is_array($value) ? $value : unserialize($value);	
		$row_html  = '';	
		$col_count = $this->columns->getCount();
		$controls  = $this->columns->getControls();		
		//var_dump($rows);
		// =========================================================
		// THEAD SECTION
		// =========================================================
		foreach ($controls as $col) 
		{
			$thead.= sprintf('<th data-name="%1$s">%2$s</th>', $col->name, $col->title);	
		}
		$thead.= '<th data-name="remove-button" style="width: 50px;">Remove</th>';	

		// =========================================================
		// TBODY SECTION
		// =========================================================
		if($rows)
		{
			foreach ($rows as $row_id => $row) 
		    { 			    		
		    	foreach ($controls as $ctrl)
		    	{	
					$tmp_ctrl       = clone $ctrl;	    		
					$value          = $row[$tmp_ctrl->name];
					$tmp_ctrl->name = sprintf('%1$s[%2$s][%3$s]', $this->name, $row_id, $tmp_ctrl->name);
					$row_html      .= sprintf('<td>%s</td>', $tmp_ctrl->getHtml($value));
		    	}		    	
		    	$row_id     = sprintf('%s-row-%s', $this->name, $row_id);
		        $remove_btn = sprintf('<button type="button" class="button remove-btn" data-row-id="%s" onclick="removeRow(this, event);"><i class="fa fa-times"></i></button>', $row_id);
		        $tbody     .= sprintf('<tr id="%1$s">%2$s<td>%3$s</td></tr>',  $row_id, $row_html, $remove_btn);
		        $row_html   = '';		       	        
		    }
		}
		$button = sprintf('<td><button type="button" class="button add-table-item">%s</button></td>', __('Add'));
        $thead  = sprintf('<thead><tr>%s</tr></thead>', $thead);
        $tbody .= sprintf('<tr class="footer">%s %s</tr>', $button, str_repeat('<td></td>', $col_count-1)); 
        $tbody  = sprintf('<tbody>%s</tbody>', $tbody);

        // =========================================================
        // NEW ROW SECTION
        // =========================================================
        foreach ($controls as $ctrl) 
        {
        	$ctrl->name = sprintf('%1$s[__i__][%2$s]', $this->name, $ctrl->name);
        	$new_row[] = sprintf('<td>%1$s</td>', $ctrl->getHtml());
        }
        $new_row[] = sprintf('<td><button type="button" class="button remove-btn" data-row-id="%s-row-__i__" onclick="removeRow(this, event);"><i class="fa fa-times"></i></button></td>', $this->name);
        $last_id = $rows ? end(array_keys($rows)) : -1;
        return sprintf('<table id="%1$s" class="widefat" data-last-id="%4$d">%2$s %3$s</table>%5$s', $this->name, $thead, $tbody, $last_id, $this->getNewRowData($new_row));
	}

	/**
	 * Add template new row
	 * @param  array  $new_row --- new row elements
	 * @return string          --- javascript variable
	 */
	public function getNewRowData($new_row = array())
	{
		foreach ( (array) $new_row as $key => $value ) 
		{
			if(!is_scalar($value)) continue;
			$new_row[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
		}

		$script = "var new_row = " . json_encode($new_row) . ';';
		return sprintf('<script type=\'text/javascript\'>/* <![CDATA[ */ %s /* ]]> */</script>', $script);
	}
}