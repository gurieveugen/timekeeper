<?php

class TableGenerator{
	//                                       __  _          
	//     ____  _________  ____  ___  _____/ /_(_)__  _____
	//    / __ \/ ___/ __ \/ __ \/ _ \/ ___/ __/ / _ \/ ___/
	//   / /_/ / /  / /_/ / /_/ /  __/ /  / /_/ /  __(__  ) 
	//  / .___/_/   \____/ .___/\___/_/   \__/_/\___/____/  
	// /_/              /_/                                 
	public $args;
	private $head;
	private $body;
	private $group;

	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  
	public function __construct($head = null, $body = null, $group = null, $args = array())
	{
		$defaults   = array('class' => 'table w100p');
		$this->args = array_merge($defaults, $args);

		$this->setHead($head);
		$this->setBody($body);
		$this->setColGroup($group);
	}

	/**
	 * Get table heml
	 * @return string --- html code
	 */
	public function getTable()
	{
		return sprintf(
			'<table %1$s>%2$s %3$s %4$s</table>', 
			$this->parseTagARGS($this->args), 
			$this->getColGroup(), 
			$this->getHead(), 
			$this->getBody()
		);		
	}

	/**
	 * Set table thead
	 * @param array $head --- fields array with row settings
	 */
	public function setHead($head)
	{
		$this->head = $head;
	}

	/**
	 * Get html thead
	 * @return string --- html code
	 */
	public function getHead()
	{
		return $this->head ? sprintf('<thead>%s</thead>', $this->generateTableRows($this->head, array('col_container' => 'th'))) : '';
	}

	/**
	 * Set table tbody
	 * @param array $body --- fields array with row settings
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}

	/**
	 * Get html tbody
	 * @return string --- html code
	 */
	public function getBody()
	{
		return $this->body ? sprintf('<tbody>%s</tbody>', $this->generateTableRows($this->body)) : '';
	}

	/**
	 * Set col group
	 * @param array $group --- columns properties
	 */
	public function setColGroup($group)
	{
		$this->group = $group;
	}

	/**
	 * Get columns 
	 * @return [type] [description]
	 */
	public function getColGroup()
	{
		if(!is_array($this->group) || !count($this->group)) return '';
		$cols = '';
		foreach ($this->group as $col) 
		{
			$cols.= sprintf('<col %s>', $this->parseTagARGS($col));
		}
		return sprintf('<colgroup>%s</colgroup>', $cols);
	}

	/**
	 * Generate table rows from array
	 * @param  array $arr  --- row items
	 * @param  array $args --- wrap options 
	 * @return mixed       --- string | boolean
	 */
	public function generateTableRows($arr, $args = array())
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
			$properties = $row;
			unset($properties['fields']);
			
			$out.= sprintf('<%1$s %2$s>', $row_container, $this->parseTagARGS($properties));	
				
			foreach ($row['fields'] as $col) 
			{
				$out.= sprintf('<%1$s>%2$s</%1$s>', $col_container, $col);
			}
			$out.= sprintf('</%s>', $row_container);
		}
		return $out;
	}

	/**
	 * Parse tag arguments
	 * From array('key1' => 'value1', 'key2' => 'value2')
	 * To key1="value1" key2="value2"
	 * @param  array $args --- arguments
	 * @return string      --- parsed arguments
	 */
	public function parseTagARGS($args)
	{
		$str = '';
		foreach ($args as $key => $value) 
		{
			$str.= " $key=\"$value\"";
		}
		return $str;
	}
}