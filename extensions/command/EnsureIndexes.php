<?php
/**
 * li3_ensureindex: ensuring mongodb indexes
 *
 * @copyright     Copyright 2012, Knodes (http://knod.es)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_ensureindex\extensions\command;

use Exception;
use lithium\core\Environment;
use lithium\core\Libraries;
use lithium\analysis\Inspector;

class EnsureIndexes extends \lithium\console\Command {

    public $model = null;

    protected $defaults = array(
        'unique' => false,
        'dropDups' => false,
        'background' => true,
        'safe' => true,
        'timeout' => 10000
    );

    public function run() {
        $models = Libraries::locate('models', $this->model);

        if( empty( $models ) ) {
            throw new Exception( "could not locate model " . $this->model );
        }

        if( !is_array( $models ) ) {
            $models = array( $models );
        }

        $counters = array(
            'models' => 0,
            'indexes' => 0
        );

        foreach( $models as $model ) {
            $model = str_replace("\\\\", "\\", $model);

            if( property_exists( $model, '_indexes' ) ) {
                $db = $model::connection();

                if ( !is_a( $db, 'lithium\data\source\MongoDb' ) ) {
                    throw new Exception( "This command works only with MongoDB, sorry" );
                }

                $conn = $db->server->{$db->_config['database']};
                $collection = $conn->{$model::meta('source')};
               
                $indexes = $model::$_indexes;
                if( empty( $indexes ) ) {
                    continue;
                }
                $counters['models']++;
                $this->out( "{:heading}ensuring indexes for model $model{:end}" );

                foreach( $indexes as $name => $index ) {
                    if( empty( $index['keys'] ) ) {
                        $this->out( "skipping index $name as it didn't define any keys" );
                        continue;
                    }

                    $keys = $index['keys'];
                    unset( $index['keys'] );
                    if( !isset( $index['name'] ) ) {
                        $index['name'] = $name;
                    }

                    $options = $index + $this->defaults;

                    try {
                       $collection->ensureIndex( $keys, $options ); 
                    } catch (Exception $e) {
                        $this->error( "{:command}$name{:end} failed with: " . $e->getMessage() );
                    }
                    $counters['indexes']++;
                    $this->out( "{:command}$name{:end} ensured" );
                   
                }
                
            }
        }

        $this->header( "{:heading}done. ensured a total of {$counters['indexes']} indexes on {$counters['models']} models{:end}" );
    }

}
