<?php
/**
 * li3_ensureindex: ensuring mongodb indexes
 *
 * @copyright     Copyright 2012, Knodes (http://knod.es)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_ensureindex\tests\cases\extensions\command;

use Exception;
use lithium\console\Request;
use lithium\core\Libraries;
use li3_ensureindex\extensions\command\EnsureIndexes;
use li3_ensureindex\tests\mocks\data\model\MockModel;

class EnsureIndexesTest extends \lithium\test\Unit {

    public $classes = array();
    public $_backup = array();
    public $collection = null;

    public function setUp() {
        Libraries::paths(
            array(
                'models' => '{:library}\tests\mocks\data\model\{:name}Model'
            )
        );

        $this->classes = array('response' => 'lithium\tests\mocks\console\MockResponse');
        $this->_backup['cwd'] = getcwd();
        $this->_backup['_SERVER'] = $_SERVER;
        $_SERVER['argv'] = array();

        $this->collection = $this->getCollection( 'li3_ensureindex\tests\mocks\data\model\MockModel' );
        $this->collection->deleteIndexes();
    }
    
    public function tearDown() {
        $_SERVER = $this->_backup['_SERVER'];
        chdir($this->_backup['cwd']);
    }

    protected function getCollection( $model ) {
        $db = $model::connection();
        if ( !is_a( $db, 'lithium\data\source\MongoDb' ) ) {
            throw new Exception( "This command works only with MongoDB, sorry" );
        }

        $conn = $db->server->{$db->_config['database']};
        return $conn->{$model::meta('source')};
    }
    

    public function testSimple() {
        $this->request = new Request( array('input' => fopen('php://temp', 'w+')) );
        $this->request->argv = array(
            "ensure-indexes"
        );
        
        $command = new EnsureIndexes( array( 'request' => $this->request, 'classes' => $this->classes ) );
        $command->run();
        $result = $command->response;
        $expected = 'lithium\console\Response';
        $this->assertTrue( is_a( $result, $expected ) );

        $this->assertNull( $command->response->error );
        
        $indexes = $this->collection->getIndexInfo();
        $this->assertEqual( 3, count( $indexes ) ); // +1 for _id index

        $this->assertEqual( 'timestamp', $indexes[1]['name'] );
        $this->assertFalse( $indexes[1]['dropDups'] );
        $this->assertTrue( $indexes[1]['background'] );

        $this->assertEqual( 'field1_field2', $indexes[2]['name'] );
        $this->assertTrue( $indexes[2]['dropDups'] );
        $this->assertFalse( $indexes[2]['background'] );
    }


}

?>