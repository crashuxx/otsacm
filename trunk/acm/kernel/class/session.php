<?php

/**
 * [ACM]Account Manager
 * 
 * Account Manager for OpenTibia Server
 * 
 * PHP versions 5
 *
 * Copyright (c) 2006-2007 Lukasz Pajak
 * 
 * LICENSE:
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * @author 		Lukasz Pajak <droopsik@gmail.com>
 * @copyright 		2006-2007 Lukasz Pajak
 * @license		GPL 
 * @package		acm
 * @subpackage 	kernel
 * @version 		3
 */


/**
 * Session class
 */
class Session {
	protected $prefix;

	/**
	 * Konstruktor
	 *
	 * @param string $prefix
	 */
	public function __construct($prefix = 'default_prefix_')
	{
		$this->prefix = $prefix;
		session_start();
	}

	/**
	 * Usowa zmienne sesji
	 * 
	 * @param string ..
	 */
	public function unRegister()
	{
		$names = func_get_args();
		foreach($names as $name)
		{
			unset($_SESSION[$this->prefix . $name]);
		}
	}

	/**
	 * Pobiera ID sesi
	 *
	 * @return string
	 */
	public function getid()
	{
		return session_id();
	}

	/**
	 * Zwraca wartosc zmiennej zapiasanej w $_SESSION[..]
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get($name)
	{
		return $_SESSION[$this->prefix . $name];
	}
	
	public function __get($name)
	{
		return $_SESSION[$this->prefix . $name];
	}

	/**
	 * Ustawia wartos zmiennej
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function set($name, $value)
	{
		$_SESSION[$this->prefix . $name] = $value;
	}
	
	public function __set($name, $value)
	{
		$_SESSION[$this->prefix . $name] = $value;
	}
}

?>