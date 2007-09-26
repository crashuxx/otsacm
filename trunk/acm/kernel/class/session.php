<?php

/**
 * Session API
 *
 * PHP versions 5
 *
 * Copyright (c) 2006-2007 Lukasz Pajak
 *
 * @author 		Lukasz Pajak <droopsik@gmail.com>
 * @copyright 	2006-2007 Lukasz Pajak
 * @package		class
 * @subpackage	session
 * @version 	v 0.3  2007/05/15
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