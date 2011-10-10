<?php

/**
 * Base action controller for ScyMultilang-enabled sites.
 *
 * Your controllers should inherit from this class instead from
 * Zend_Controller_Action, because it will set the locale for the global
 * Zend_Translate instance to the locale stored in the global Zend_Locale
 * instance. Also, it will set the $this->Zend_Locale view variable.
 */
class ScyMultilang_Controller_Action extends Zend_Controller_Action {

	public function init() {
		// Get the active locale.
		$locale = Zend_Registry::get('Zend_Locale');
		// Set a global translate (if it exists) to that locale.
		if (Zend_Registry::isRegistered('Zend_Translate')) {
			$translate = Zend_Registry::get('Zend_Translate');
			$translate->setLocale($locale);
		}
		// Set a locale variable in the view.
		$this->view->Zend_Locale = $locale;
		return parent::init();
	}

}
