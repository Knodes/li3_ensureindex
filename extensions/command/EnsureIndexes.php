<?php
/**
 * li3_ensureindex: ensuring mongodb indexes
 *
 * @copyright     Copyright 2012, Knodes (http://knod.es)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_ensureindex\extensions\command;

use Exception;
use lithium\core\Libraries;

class EnsureIndexes extends \lithium\console\Command
{
    public $model = null;

    protected $defaults = array(
        'unique'     => false,
        'dropDups'   => false,
        'background' => true,
        'safe'       => true,
        'timeout'    => 10000,
    );

    public function run()
    {
        $models = Libraries::locate('models', $this->model);

        if (empty($models)) {
            throw new Exception('Could not locate model: ' . $this->model);
        }

        if (!is_array($models)) {
            $models = array($models);
        }

        $counters = array(
            'models'  => 0,
            'indexes' => 0,
        );

        foreach ($models as $model) {
            $model = str_replace("\\\\", "\\", $model);
            if (property_exists($model, '_indexes')) {
                $indexes = $model::$_indexes;
                if (empty($indexes)) {
                    continue;
                }

                $db = $model::connection();
                if (!is_a($db, 'lithium\data\source\MongoDb')) {
                    throw new Exception('This command works only with MongoDB');
                }

                $db->connect();
                $collection = $db->connection->{$model::meta('source')};

                $counters['models']++;
                $this->out('{:heading}Ensuring indexes for model: ' . $model . '{:end}');

                foreach ($indexes as $name => $index) {
                    if (empty($index['keys'])) {
                        $this->error(' * No keys defined for index: ' . $name);
                        continue;
                    }

                    $keys = $index['keys'];
                    unset($index['keys']);

                    if (!isset($index['name'])) {
                        $index['name'] = $name;
                    }

                    $options = $index + $this->defaults;

                    try {
                        $collection->ensureIndex($keys, $options);
                    } catch (Exception $e) {
                        $this->error(' * Failed: {:command}' . $name . '{:end} with: ' . $e->getMessage());
                        continue;
                    }

                    $counters['indexes']++;
                    $this->out(' * Ensured: {:command}' . $name . '{:end}');
                }
            }
        }

        $this->out('');
        $this->header('{:heading}Done! Ensured a total of ' . $counters['indexes'] . ' indexes on ' . $counters['models'] . ' models{:end}');
    }
}
