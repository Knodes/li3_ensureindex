# li3_ensureindex

`li3_ensureindex` allows you to define MongoDB indexes within your models, and ensure them on the server via a console command. 

## Installation

Get the library code:

    $ cd /path/to/app/libraries
    $ git clone https://github.com/Knodes/li3_ensureindex.git

Make sure it's added on `app/config/bootstrap/libraries.php`:

    
    Libraries::add('li3_ensureindex');
    

## Index Definition

To define your indexes inside your model, just add a static and public _indexes property, such as:

    
    static public $_indexes = array(
        'indexname' =>  array(
            'keys' => array('somefield' => -1)
        )
    );
    

That's it. The 'indexname' key will be used as the index name in Mongo.
Other options detault to these:

    'unique' => false,
    'dropDups' => false,
    'background' => true,
    'safe' => true,
    'timeout' => 10000

And can be overwritten easily:

    
    static public $_indexes = array(
        'indexname' =>  array(
            'keys' => array('somefield' => -1),
            'background' => false,
        ),

        'anotherindex' =>  array(
            'keys' => array('anotherfield' => -1, 'athirdfield' => 1),
            'unique' => true,
            'dropDups' => true,
            'safe' => false
        ),

    );
    

Please note that MongoCollection::ensureIndex restrictions apply of course, as described in the [docs](http://www.php.net/manual/en/mongocollection.ensureindex.php).   

## Ensuring Indexes

This library comes with a console command to ensure the defined indexes.
Ensuring all indexes defined for all models is as simple as:

    li3 ensure-indexes

And will result in a detailed report of which indexes were created.
Please note that existing indexes are not deleted from the collections.

It's also possible to limit the index ensure to a specific model by supplying it as a parameters:

    li3 ensure-indexes --model=MyModel





