<?php

/*{{{*/
/**
 * ----------------------------------------------------------------------------
 * Mumsys_Registry
 * for MUMSYS Library for Multi User Management System (MUMSYS)
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * ----------------------------------------------------------------------------
 * @copyright Copyright (c) 2014 by Florian Blasel for FloWorks Company
 * ----------------------------------------------------------------------------
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Registry
 * @version     1.0.0
 * Created: 2014-01-07
 * @filesource
 */
/*}}}*/


/**
 * Mumsys registry class.
 *
 * @uses Singleton pattern
 *
 * @category    Mumsys
 * @package     Mumsys_Library
 * @subpackage  Mumsys_Registry
 */
abstract class Mumsys_Registry extends Mumsys_Abstract
{
    /**
     * Version ID information
     */
    const VERSION = '1.0.0';

    /**
     * List of properties to register
     * @var array
     */
    private static $_registry = array();


    /**
     * Replaces/ sets the value to the registry by given key.
     *
     * @param sting $key Key to be set
     * @param mixed $value Value to be set
     * @throws Mumsys_Registry_Exception Throws exception if key is not a string
     */
    public static function replace( $key, $value )
    {
        self::_checkKey($key);
        self::$_registry[$key] = $value;
    }


    /**
     * Registers the value to the registry by given key.
     *
     * @param sting $key Key to register
     * @param mixed $value Value to be set

     * @throws Mumsys_Registry_Exception Throws exception if key already exists
     */
    public static function register( $key, $value )
    {
        self::_checkKey($key);

        if (array_key_exists($key, self::$_registry)) {
            $message = sprintf('Registry key "%1$s" exists', $key);
            throw new Mumsys_Registry_Exception($message);
        }

        self::$_registry[$key] = $value;
    }


    /**
     * Sets value to the registry by given key and value.
     *
     * @todo To be removed in the future.
     *
     * @throws Mumsys_Registry_Exception Throws exception
     */
    public static function set( $key, $value )
    {
        $message = 'Unknown meaning for set(). Use register() or replace() methodes';
        throw new Mumsys_Registry_Exception($message);
    }


    /**
     * Returns the value by given key.
     *
     * @param string $key Key which was set
     * @return mixed Returns the value which was set
     *
     * @throws Mumsys_Registry_Exception Throws exception if key not exists
     */
    public static function get( $key )
    {
        if (isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        }

        $message = sprintf('Registry key "%1$s" not found', $key);
        throw new Mumsys_Registry_Exception($message);
    }

    /**
     * Removes registered entry.
     *
     * @param string $key Key which was set
     *
     * @throws Mumsys_Registry_Exception Throws exception if key not exists
     */
    public static function remove( $key )
    {
        if (isset(self::$_registry[$key])) {
            unset(self::$_registry[$key]);
            return true;
        }

        return false;
    }

    /**
     * Check given key to be a valid type.
     *
     * @param string $key Key to register
     * @throws Mumsys_Registry_Exception Throws exception if key is not a string
     */
    private function _checkKey( $key )
    {
        if (!is_string($key)) {
            $message = 'Invalid registry key. It\'s not a string';
            throw new Mumsys_Registry_Exception($message);
        }
    }

}
