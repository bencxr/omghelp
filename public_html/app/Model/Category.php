<?php
App::uses('AppModel', 'Model');
/**
 * category Model
 *
 * @property category $Parentcategory
 * @property category $Childcategory
 * @property Conversation $Conversation
 */
class Category extends AppModel {
    var $actsAs = array('Tree'); 
}
