<?php
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
    public $name = 'User';
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'helper', 'user')),
                'message' => 'Please enter a valid role',
                'allowEmpty' => false
            )
        )
    );
    var $displayField = 'fullname';
    var $hasAndBelongsToMany = array(
        'HelpsCategories' =>
            array(
                'className'              => 'Category',
                'joinTable'              => 'users_categories',
                'foreignKey'             => 'user_id',
                'associationForeignKey'  => 'category_id',
                'unique'                 => true,
                'conditions'             => '',
                'fields'                 => '',
                'order'                  => '',
                'limit'                  => '',
                'offset'                 => '',
                'finderQuery'            => '',
                'deleteQuery'            => '',
                'insertQuery'            => ''
            )
    );

}

?>
