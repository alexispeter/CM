<?php

class CM_Usertext_Filter_MaxLengthTest extends CMTest_TestCase {

	public static function tearDownAfterClass() {
		CMTest_TH::clearEnv();
	}

	public function testProcess() {
		$filter = new CM_Usertext_Filter_MaxLength(10);
		$this->assertEquals('Hello…',$filter->transform('Hello World'));

		$filter = new CM_Usertext_Filter_MaxLength(11);
		$this->assertEquals('Hello World', $filter->transform('Hello World'));

		$filter = new CM_Usertext_Filter_MaxLength(12);
		$this->assertEquals('Hello World', $filter->transform('Hello World'));
	}

}
