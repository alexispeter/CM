<?php
/**
 * @param array                    $params
 * @param Smarty_Internal_Template $template
 * @return bool
 */
function smarty_function_actionAllowed(array $params, Smarty_Internal_Template $template) {

	if (!isset($params['actionType'])) {
		trigger_error('Param `actionType` missing.');
	}
	if (!isset($params['actionVerb'])) {
		trigger_error('Param `actionVerb` missing.');
	}
	if (!empty($params['forceAllow'])) {
		return true;
	}

	/** @var CM_Model_User $viewer */
	$viewer = $template->smarty->getTemplateVars('viewer');
	if (!isset($viewer)) {
		return false;
	}

	$action = CM_Action_Abstract::factory($viewer, (int) $params['actionVerb'], (int) $params['actionType']);

	/** @var $action SK_Action_Abstract */
	return ($action->getActionLimit() === null);
}
