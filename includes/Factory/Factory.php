<?php
namespace Factory;

abstract class Factory{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	protected $post_type;    

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  

    /**
     * Get Factory folder url
     * @return string --- url
     */
    protected function getFolderURL()
    {
        $url = getCurrentUrl(__FILE__);  
        return dirname($url);
    }

    /**
     * Helper method, that attaches a passed function to the 'init' WP action
     * @param function $cb Passed callback function.
     */
    protected function init($cb)
    {
        add_action("init", $cb);
    }

    /**
     * Helper method, that attaches a passed function to the 'admin_init' WP action
     * @param function $cb Passed callback function.
     */
    protected function adminInit($cb)
    {
        add_action("admin_init", $cb);
    }

    /**
     * Get post type
     * @return string --- post type
     */
    public function getPostType()
    {
    	return $this->post_type;
    }

    /**
     * Format name.
     * From: Some name
     * To:   some_name
     * @param  string $name --- entry name
     * @return string       --- exit name
     */
    public function formatName($name)
    {
        return strtolower(str_replace(' ', '_', $name));
    }

    /**
     * Format name to web control
     * @param  string $name
     * @return string      
     */
    public function formatControlName($name)
    {
        return $this->post_type.'_'.$this->formatName($name); 
    }

    /**
     * Helper for checkbox control in admin table
     * @param  string $val --- value
     * @return string      --- css class
     */
    public function circleCheckbox($val)
    {
        if(is_array($val)) return $val;
        return $val == '' ? 'fa-circle-thin' : 'fa-circle';
    }
    
    /**
     * Get all meta data from post
     * @param  integer $post_id --- post id
     * @param  array $fields    --- fields array to export
     * @return mixed            --- meta values [array] | false [boolean]
     */
    public function getMeta($post_id, $fields)
    {
        $arr  = array();
        $meta = get_post_custom($post_id);
        if($fields)
        {
            foreach ($fields as $key => &$value) 
            {
                $name = $this->formatControlName($key);
                if(isset($meta[$name])) $arr[$name] = $meta[$name][0];
            }
            return $arr;
        }
        return false;
    }

    /**
     * Get control by type
     * @param  string $type  --- control type
     * @param  string $name  --- control name
     * @param  string $value --- current value
     * @param  mixed $items  --- array items
     * @return string        --- html code
     */
    protected function getControl($type, $name, $value, $items = null)
    {        

        $types  = array(
            'text'     => '<input type="text" name="%1$s" value="%2$s" class="widefat" />',
            'email'    => '<input type="email" name="%1$s" value="%2$s" class="widefat" />',
            'textarea' => '<textarea name="%1$s" class="widefat" rows="10">%2$s</textarea>',
            'checkbox' => '<input type="checkbox" name="%1$s" value="%1$s" '.checked(empty($value), false, false).' />', 
            'select'   => $this->getSelectControl($name, $items, $value), 
            'table'    => $this->getTableControl($name, $items, $value),
            'file'     => '<input type="file" name="%1$s" id="%1$s" />');
        
        return sprintf($types[$type], $name, $value);
    }

    /**
     * Generate table control
     * @param  string $name   --- control name
     * @param  array $columns --- table columns
     * @param  array $rows    --- rows with values
     * @return string         --- html code
     */
    protected function getTableControl($name, $columns, $rows)
    {
        if(!$columns) return '';

        $thead   = '';
        $tbody   = ''; 
        $rows    = is_array($rows) ? $rows : unserialize($rows);
        $row     = '';
        $last_id = 0;        

        foreach ($columns as &$col) 
        {
            $thead.= sprintf('<th data-name="%1$s">%1$s</th>', ucwords($col));
        }
        $thead.= '<th data-name="remove-button" style="width: 50px;">Remove</th>';
        if($rows)
        {
            foreach ($rows as $row_key => $row_val) 
            {                
                $last_id = $row_key;
                foreach ($row_val as $field_key => $field_val) 
                {
                    $col_name = sprintf('%1$s[%2$s][%3$s]', $name, $last_id, $this->formatName($field_key));
                    $row     .= sprintf('<td><input type="text" class="widefat" name="%s" value="%s"></td>', $col_name, $field_val);
                }
                $row_id     = sprintf('%s-row-%s', $name, $last_id);
                $remove_btn = sprintf('<button type="button" class="button remove-btn" data-row-id="%s"><i class="fa fa-times"></i></button>', $row_id);
                $tbody      .= sprintf('<tr id="%1$s">%2$s<td>%3$s</td></tr>',  $row_id, $row, $remove_btn);
                $row        = '';
            }    
        }
        $button = sprintf('<td><button type="button" class="button add-table-item">%s</button></td>', __('Add'));
        $thead  = sprintf('<thead><tr>%s</tr></thead>', $thead);
        $tbody .= sprintf('<tr class="footer">%s %s</tr>', $button, str_repeat('<td></td>', (count($columns)-1))); 
        $tbody  = sprintf('<tbody>%s</tbody>', $tbody);
        return sprintf('<table id="%1$s" class="widefat" data-columns-count="%4$d" data-last-id="%5$d">%2$s %3$s</table>', $name, $thead, $tbody, count($columns), $last_id);
    }

    /**
     * Generate select control
     * @param  string $name    --- control name
     * @param  array $items    --- options for control
     * @param  string $value   --- value to select
     * @param  string $options --- custom option if u need
     * @return string          --- html code
     */
    protected function getSelectControl($name, $items, $value, $options = '')
    {   

        if(!$items) return '';  
        foreach ($items as $option) 
        {
            $options.= sprintf('<option value="%1$s" %2$s>%1$s</option>', $option, selected($value, $option, false));           
        }       
        return sprintf('<select name="%1$s" class="widefat">%2$s</select>', $name, $options); 
    }
}