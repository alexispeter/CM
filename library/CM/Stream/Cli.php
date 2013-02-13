<?php

class CM_Stream_Cli extends CM_Cli_Runnable_Abstract {

	/**
	 * @synchronized
	 */
	public function startSynchronization() {
		CM_Stream_Message::getInstance()->startSynchronization();
	}

	public static function getPackageName() {
		return 'stream';
	}

}