<?php
/**
 * ConversationFixture
 *
 */
class ConversationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'),
		'sessionID' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'helperID' => array('type' => 'integer', 'null' => true, 'default' => null),
		'categoryID' => array('type' => 'integer', 'null' => true, 'default' => null),
		'imageEnabled' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'imagePhotoURL' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'imageOverlayData' => array('type' => 'binary', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'sessionID' => 'Lorem ipsum dolor sit amet',
			'helperID' => 1,
			'categoryID' => 1,
			'imageEnabled' => 1,
			'imagePhotoURL' => 'Lorem ipsum dolor sit amet',
			'imageOverlayData' => 'Lorem ipsum dolor sit amet',
			'created' => '2012-08-11 19:46:42',
			'modified' => '2012-08-11 19:46:42'
		),
	);

}
