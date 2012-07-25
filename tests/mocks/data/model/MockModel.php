<?php

namespace li3_ensureindex\tests\mocks\data\model;


class MockModel extends \lithium\data\Model {

    protected $_schema = array(
        '_id'  => array('type' => 'id'),
        'timestamp' => array('type' => 'datetime'),
        'field1' => array('type' => 'string'),
        'field2' => array('type' => 'integer'),
    );

    static public $_indexes = array(
        'timestamp' =>  array(
            'keys' => array('timestamp' => -1),
            'unique' => false
        ),

        'field1_field2' =>  array(
            'keys' => array('field1' => -1, 'field2' => 1),
            'unique' => true,
            'dropDups' => true,
            'background' => false
        )
    );

}