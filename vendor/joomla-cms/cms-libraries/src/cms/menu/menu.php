<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Menu
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JMenu class
 *
 * @package     Joomla.Libraries
 * @subpackage  Menu
 * @since       1.5
 */
class JMenu
{
	/**
	 * @var    array  JMenu instances container.
	 * @since  1.7
	 */
	protected static $instances = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options  An array of configuration options.
	 *
	 * @since   1.5
	 */
	public function __construct($options = array())
	{
		// Load the menu items
		$this->load();

		foreach ($this->_items as $item)
		{
			if ($item->home)
			{
				$this->_default[trim($item->language)] = $item->id;
			}

			// Decode the item params
			$result = new JRegistry;
			$result->loadString($item->params);
			$item->params = $result;
		}
	}

	/**
	 * Returns a JMenu object
	 *
	 * @param   string  $client   The name of the client
	 * @param   array   $options  An associative array of options
	 *
	 * @return  JMenu  A menu object.
	 *
	 * @since   1.5
	 * @throws  Exception
	 */
	public static function getInstance($client, $options = array())
	{
		if (empty(self::$instances[$client]))
		{
			// Create a JMenu object
			$classname = 'JMenu' . ucfirst($client);

			if (class_exists($classname))
			{
				self::$instances[$client] = new $classname($options);
			}
			else
			{
				throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_MENU_LOAD', $client), 500);
			}
		}

		return self::$instances[$client];
	}

	/**
	 * Get menu item by id
	 *
	 * @param   integer  $id  The item id
	 *
	 * @return  mixed    The item object, or null if not found
	 *
	 * @since   1.5
	 */
	public function getItem($id)
	{
		$result = null;

		if (isset($this->_items[$id]))
		{
			$result = &$this->_items[$id];
		}

		return $result;
	}

	/**
	 * Set the default item by id and language code.
	 *
	 * @param   integer  $id        The menu item id.
	 * @param   string   $language  The language cod (since 1.6).
	 *
	 * @return  boolean  True, if successful
	 *
	 * @since   1.5
	 */
	public function setDefault($id, $language = '')
	{
		if (isset($this->_items[$id]))
		{
			$this->_default[$language] = $id;

			return true;
		}

		return false;
	}

	/**
	 * Get the default item by language code.
	 *
	 * @param   string  $language  The language code, default value of * means all.
	 *
	 * @return  object  The item object
	 *
	 * @since   1.5
	 */
	public function getDefault($language = '*')
	{
		if (array_key_exists($language, $this->_default))
		{
			return $this->_items[$this->_default[$language]];
		}
		elseif (array_key_exists('*', $this->_default))
		{
			return $this->_items[$this->_default['*']];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Set the default item by id
	 *
	 * @param   integer  $id  The item id
	 *
	 * @return  mixed  If successful the active item, otherwise null
	 *
	 * @since   1.5
	 */
	public function setActive($id)
	{
		if (isset($this->_items[$id]))
		{
			$this->_active = $id;
			$result = &$this->_items[$id];

			return $result;
		}

		return null;
	}

	/**
	 * Get menu item by id.
	 *
	 * @return  object  The item object.
	 *
	 * @since   1.5
	 */
	public function getActive()
	{
		if ($this->_active)
		{
			$item = &$this->_items[$this->_active];

			return $item;
		}

		return null;
	}

	/**
	 * Gets menu items by attribute
	 *
	 * @param   mixed    $attributes  The field name(s).
	 * @param   mixed    $values      The value(s) of the field. If an array, need to match field names
	 *                                each attribute may have multiple values to lookup for.
	 * @param   boolean  $firstonly   If true, only returns the first item found
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$items = array();
		$attributes = (array) $attributes;
		$values = (array) $values;

		foreach ($this->_items as $item)
		{
			if (!is_object($item))
			{
				continue;
			}

			$test = true;

			for ($i = 0, $count = count($attributes); $i < $count; $i++)
			{
				if (is_array($values[$i]))
				{
					if (!in_array($item->$attributes[$i], $values[$i]))
					{
						$test = false;
						break;
					}
				}
				else
				{
					if ($item->$attributes[$i] != $values[$i])
					{
						$test = false;
						break;
					}
				}
			}

			if ($test)
			{
				if ($firstonly)
				{
					return $item;
				}

				$items[] = $item;
			}
		}

		return $items;
	}

	/**
	 * Gets the parameter object for a certain menu item
	 *
	 * @param   integer  $id  The item id
	 *
	 * @return  JRegistry  A JRegistry object
	 *
	 * @since   1.5
	 */
	public function getParams($id)
	{
		if ($menu = $this->getItem($id))
		{
			return $menu->params;
		}
		else
		{
			return new JRegistry;
		}
	}

	/**
	 * Getter for the menu array
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function getMenu()
	{
		return $this->_items;
	}

	/**
	 * Method to check JMenu object authorization against an access control
	 * object and optionally an access extension object
	 *
	 * @param   integer  $id  The menu id
	 *
	 * @return  boolean  True if authorised
	 *
	 * @since   1.5
	 */
	public function authorise($id)
	{
		$menu = $this->getItem($id);
		$user = JFactory::getUser();

		if ($menu)
		{
			return in_array((int) $menu->access, $user->getAuthorisedViewLevels());
		}
		else
		{
			return true;
		}
	}

	/**
	 * Loads the menu items
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public function load()
	{
		return array();
	}
}
