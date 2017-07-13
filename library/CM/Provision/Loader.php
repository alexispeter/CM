<?php

class CM_Provision_Loader {

    /** @var CM_Provision_Script_Abstract[] */
    private $_scriptList;

    public function __construct() {
        $this->_scriptList = [];
    }

    /**
     * @param CM_Provision_Script_Abstract $script
     */
    public function registerScript(CM_Provision_Script_Abstract $script) {
        $this->_scriptList[] = $script;
    }

    /**
     * @param string[]           $scriptClassNames
     * @param CM_Service_Manager $serviceManager
     */
    public function registerScriptFromClassNames(array $scriptClassNames, CM_Service_Manager $serviceManager) {
        foreach ($scriptClassNames as $scriptClassName) {
            $this->registerScript(new $scriptClassName($serviceManager));
        }
    }

    public function load(CM_OutputStream_Interface $output) {
        $loadingTimes = [];
        $scriptList = $this->_getScriptList();
        foreach ($scriptList as $setupScript) {
            $start = microtime(true);
            if ($setupScript->shouldBeLoaded()) {
                $output->writeln('  Loading ' . $setupScript->getName() . '…');
                $setupScript->load($output);
            }
            $end = microtime(true);
            $time = round($end - $start, 5);
            $loadingTimes[$setupScript->getName()] = $time;
        }
        asort($loadingTimes);
        echo "Load times\n";
        foreach ($loadingTimes as $scriptName => $time) {
            echo str_pad("{$time}", 10, ' ') . "{$scriptName}\n";
        }
    }

    public function unload(CM_OutputStream_Interface $output) {
        $loadingTimes = [];
        $scriptList = array_reverse($this->_getScriptList());
        foreach ($scriptList as $setupScript) {
            $start = microtime(true);
            if ($setupScript instanceof CM_Provision_Script_UnloadableInterface && $setupScript->shouldBeUnloaded()) {
                /** @var $setupScript CM_Provision_Script_Abstract|CM_Provision_Script_UnloadableInterface */
                $output->writeln('  Unloading ' . $setupScript->getName() . '…');
                $setupScript->unload($output);
            }
            $end = microtime(true);
            $time = round($end - $start, 5);
            $loadingTimes[$setupScript->getName()] = $time;
        }
        asort($loadingTimes);
        echo "Unload times\n";
        foreach ($loadingTimes as $scriptName => $time) {
            echo str_pad("{$time}", 10, ' ') . "{$scriptName}\n";
        }
    }

    public function reload(CM_OutputStream_Interface $output) {
        $loadingTimes = [];
        $scriptList = $this->_getScriptList();
        foreach ($scriptList as $setupScript) {
            $start = microtime(true);
            if ($setupScript->shouldBeLoaded()) {
                $output->writeln('  Loading ' . $setupScript->getName() . '…');
                $setupScript->load($output);
            } elseif ($setupScript instanceof CM_Provision_Script_UnloadableInterface) {
                /** @var $setupScript CM_Provision_Script_Abstract */
                $output->writeln('  Reloading ' . $setupScript->getName() . '…');
                $setupScript->reload($output);
            }
            $end = microtime(true);
            $time = round($end - $start, 5);
            $loadingTimes[$setupScript->getName()] = $time;
        }
        asort($loadingTimes);
        echo "Reload times\n";
        foreach ($loadingTimes as $scriptName => $time) {
            echo str_pad("{$time}", 10, ' ') . "{$scriptName}\n";
        }
    }

    /**
     * @return CM_Provision_Script_Abstract[]
     */
    protected function _getScriptList() {
        $scriptList = $this->_scriptList;
        $runLevelList = \Functional\invoke($scriptList, 'getRunLevel');
        array_multisort($runLevelList, array_keys($scriptList), $scriptList);
        return $scriptList;
    }
}
