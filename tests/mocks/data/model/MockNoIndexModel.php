<?php

namespace li3_ensureindex\tests\mocks\data\model;


class MockNoIndexModel extends \lithium\data\Model {

    protected $_schema = array(
        '_id'  => array('type' => 'id'),
        'timestamp' => array('type' => 'datetime'),
        'field1' => array('type' => 'string'),
        'field2' => array('type' => 'integer'),
    );



}