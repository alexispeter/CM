<?php

abstract class CM_Class_Abstract {

	/**
	 * @return int
	 */
	public function getType() {
		return static::TYPE;
	}

	/**
	 * @return string[] List of class names
	 */
	public function getClassHierarchy() {
		return self::_getClassHierarchy();
	}

	/**
	 * @param int $type
	 * @return string
	 * @throws CM_Exception_Invalid
	 */
	public static function _getClassName($type = null) {
		$type = (int) $type;
		$config = self::_getConfig();
		if (!$type) {
			if (empty($config->class)) {
				return get_called_class();
			}
			return $config->class;
		}
		if (empty($config->types[$type])) {
			throw new CM_Exception_Invalid('Type `' . $type . '` not configured for class `' . get_called_class() . '`.');
		}
		return $config->types[$type];
	}

	/**
	 * @return stdClass
	 * @throws CM_Exception_Invalid
	 */
	protected static function _getConfig() {
		$config = CM_Config::get();
		foreach (self::_getClassHierarchy() as $class) {
			if (isset($config->$class)) {
				return $config->$class;
			}
		}
		throw new CM_Exception_Invalid('Class `' . get_called_class() . '` has no configuration.');
	}

	/**
	 * @return string[]
	 */
	private static function _getClassHierarchy() {
		$classHierarchy = array_values(class_parents(get_called_class()));
		array_unshift($classHierarchy, get_called_class());
		array_pop($classHierarchy);
		return $classHierarchy;
	}
}
