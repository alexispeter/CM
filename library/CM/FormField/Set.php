<?php

class CM_FormField_Set extends CM_FormField_Abstract {

	private $_valuesSet = array();
	private $_columnSize;
	private $_labelsInValues;
	private $_labelPrefix;

	/**
	 * @param string $name
	 * @param array $valuesSet OPTIONAL possible values
	 * @param string $labelPrefix OPTIONAL
	 * @param bool $labelsInValues OPTIONAL
	 */
	public function __construct($name, array $valuesSet = array(), $labelPrefix = null, $labelsInValues = false) {
		$this->_valuesSet = $valuesSet;
		$this->_labelPrefix = (string) $labelPrefix;
		$this->_labelsInValues = (bool) $labelsInValues;
		parent::__construct($name);
	}

	public function setColumnSize($cssSize) {
		$this->_columnSize = $cssSize;
	}

	public function validate($userInput) {
		foreach ($userInput as $key => $value) {
			if (!in_array($value, $this->_getValuesSet())) {
				unset($userInput[$key]);
			}
		}
		return $userInput;
	}

	public function prepare(array $params) {
		$this->setTplParam('class', isset($params['class']) ? $params['class'] : null);
		$labelsection = isset($params['labelsection']) ? $params['labelsection'] : '%forms._fields.' . $this->getName() . '.values';
		$this->setTplParam('labelsForValuesSet', $this->_getLabelsForValuesSet($labelsection));
		$this->setTplParam('colSize', isset($params['col_size']) ? $params['col_size'] : $this->_columnSize);
	}

	private function _getValuesSet() {
		if ($this->_labelsInValues) {
			return array_keys($this->_valuesSet);
		} else {
			return $this->_valuesSet;
		}
	}

	private function _getLabelsForValuesSet($labelsection) {
		if ($this->_labelsInValues) {
			$valuesSet = $this->_valuesSet;
		} else {
			$valuesSet = array();
			foreach ($this->_valuesSet as $item) {
				$lang_key = $this->_labelPrefix ? $this->_labelPrefix . '_' . $item : $item;
				$valuesSet[$item] = CM_Language::text($labelsection . '.' . $lang_key);
			}
		}
		return $valuesSet;
	}

}
