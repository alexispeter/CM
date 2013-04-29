<?php

class CM_Model_Splittest_RequestClient extends CM_Model_Splittest {

	const TYPE = 26;

	/**
	 * @param CM_Request_Abstract $request
	 * @param string              $variationName
	 * @return bool
	 */
	public function isVariationFixture(CM_Request_Abstract $request, $variationName) {
		if ($request->isBotCrawler()) {
			return false;
		}
		return $this->_isVariationFixture($request->getClientId(), $variationName);
	}

	/**
	 * @param CM_Request_Abstract $request
	 * @param float|null          $weight
	 */
	public function setConversion(CM_Request_Abstract $request, $weight = null) {
		if ($request->isBotCrawler()) {
			return;
		}
		$this->_setConversion($request->getClientId(), $weight);
	}
}
