<?php
/**
 * li3_ensureindex: ensuring mongodb indexes
 *
 * @copyright     Copyright 2012, Knodes (http://knod.es)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_ensureindex\tests\mocks\data\model;


class MockNoIndexModel extends \lithium\data\Model {

    protected $_schema = array(
        '_id'  => array('type' => 'id'),
        'timestamp' => array('type' => 'datetime'),
        'field1' => array('type' => 'string'),
        'field2' => array('type' => 'integer'),
    );



}