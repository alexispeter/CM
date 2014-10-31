<?php

class CM_I18n_SetupScript extends CM_Provision_Script_Abstract {

    public function load(CM_Service_Manager $manager) {
        /** @var CM_Model_Language $language */
        foreach (new CM_Paging_Language_All() as $language) {
            $path = 'translations/' . $language->getAbbreviation() . '.php';
            foreach (CM_Util::getResourceFiles($path) as $translationsFile) {
                $translationsSetter = require $translationsFile->getPath();
                if (!$translationsSetter instanceof Closure) {
                    throw new CM_Exception_Invalid('Invalid translation file. `' . $translationsFile->getPath() . '` must return callable');
                }
                $translationsSetter($language);
            }
        }
    }
}
