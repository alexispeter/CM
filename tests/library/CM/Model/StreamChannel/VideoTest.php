<?php
require_once __DIR__ . '/../../../../TestCase.php';

class CM_Model_StreamChannel_VideoTest extends TestCase {

	public function setup() {
		TH::clearEnv();
	}

	public function tearDown() {
		TH::clearEnv();
	}

	public function testCreate() {
		/** @var CM_Model_StreamChannel_Video $channel */
		$channel = CM_Model_StreamChannel_Video::create(array('key' => 'foo', 'width' => 100, 'height' => 200, 'wowzaIp' => ip2long('127.0.0.1')));
		$this->assertInstanceOf('CM_Model_StreamChannel_Video', $channel);
		$this->assertSame(100, $channel->getWidth());
		$this->assertSame(200, $channel->getHeight());
		$this->assertSame('127.0.0.1', long2ip($channel->getWowzaIp()));
		$this->assertSame('foo', $channel->getKey());
	}

	public function testOnDelete() {
		$streamChannel = TH::createStreamChannel();
		$streamChannel->delete();
		try {
			new CM_Model_StreamChannel_Video($streamChannel->getId());
		} catch (CM_Exception_Nonexistent $ex) {
			$this->assertTrue(true);
		}
		$this->assertNotRow(TBL_CM_STREAMCHANNEL_VIDEO, array('id' => $streamChannel->getId()));
	}

	public function testOnBeforeDelete() {
		$streamChannel = TH::createStreamChannel();
		TH::createStreamPublish(null, $streamChannel);
		try {
			new CM_Model_StreamChannelArchive_Video($streamChannel->getId());
			$this->fail('Archive exists before StreamChannel deleted.');
		} catch (CM_Exception_Nonexistent $ex) {
			$this->assertTrue(true);
		}
		$streamChannel->delete();
		try {
			new CM_Model_StreamChannelArchive_Video($streamChannel->getId());
			$this->assertTrue(true);
		} catch (CM_Exception_Nonexistent $ex) {
			$this->fail('Archive was not created.');
		}

		//without streamPublish
		$streamChannel = TH::createStreamChannel();
		$streamChannel->delete();
		try {
			new CM_Model_StreamChannelArchive_Video($streamChannel->getId());
			$this->fail('Archive created despite missing streamPublish.');
		} catch (CM_Exception_Nonexistent $ex) {
			$this->assertTrue(true);
		}
	}
}

