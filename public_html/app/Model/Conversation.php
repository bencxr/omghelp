<?php
App::uses('AppModel', 'Model');
/**
 * Conversation Model
 *
 * @property Helper $Helper
 * @property Category $Category
 */
class Conversation extends AppModel {


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Helper' => array(
			'className' => 'User',
			'foreignKey' => 'helper_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Category' => array(
			'className' => 'Category',
			'foreignKey' => 'category_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
