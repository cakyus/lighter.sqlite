<?php

namespace Lighter;

/**
 * Static methods for loading classes and files.
 **/

class Loader {
	
	/**
	 * Register autoload callback
	 **/
	
	public static function register() {
		spl_autoload_register(array(__CLASS__, 'load'));
	}
	
	/**
	 * Autoload callback
	 **/
	
	public static function load($class) {
		
		if (class_exists($class)) {
			return false;
		}
		
		$file = self::getClassPath($class);
		
		if (!is_readable($file)) {
			throw new \Exception('Unable to read file. '.$file);
		}
		
		require_once($file);
	}
	
	/**
	 * Resolve file path of a class
	 **/
	
	public static function getClassPath($class) {
		
		if (is_object($class)) {
			$class = get_class($class);
		}
		
		$namespaces = explode('\\', $class);
		
		if ($namespaces[0] == __NAMESPACE__) {
			unset($namespaces[0]);
			return __DIR__.'/'.implode('/', $namespaces).'.php';
		} else {
			return dirname(__DIR__).'/'.implode('/', $namespaces).'.php';
		}
	}	
}
