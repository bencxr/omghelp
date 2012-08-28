<?php
App::uses('Conversation', 'Model');

/**
 * Conversation Test Case
 *
 */
class ConversationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.conversation'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Conversation = ClassRegistry::init('Conversation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Conversation);

		parent::tearDown();
	}

}
