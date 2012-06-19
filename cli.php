<?php

use cyclone\dbdeploy;

return array(
    'description' => 'Tool for database version controlling.',
    'commands' => array(
        'apply' => array(
            'description' => 'Applies the changes not yet applied to the database.',
            'arguments' => array(
                '--revision' => array(
                    'alias' => '-r',
                    'descr' => 'the target revision',
                    'required' => FALSE
                ),
                '--delta-set' => array(
                    'alias' => '-s',
                    'descr' => 'the delta set to be applied up to the target revision',
                    'required' => FALSE
                ),
                '--changelog-table' => array(
                    'alias' => '-c',
                    'descr' => 'the nam eof the changelog table in the database which stores the applies revisions',
                    'required' => FALSE
                ),
                '--connection' => array(
                    'alias' => '-n',
                    'descr' => 'the name of the DB config (connection) to be used',
                    'required' => FALSE,
                ),
                '--src-dir' => array(
                    'alias' => '-d',
                    'descr' => 'the root directory of database revision files',
                    'required' => FALSE,
                    'default' => NULL
                ),
                '--exec' => array(
                    'alias' => '-e',
                    'required' => FALSE,
                    'descr' => 'executes the created SQL script, not only writes to standard output'
                ),
                '--quiet' => array(
                    'alias' => '-q',
                    'required' => FALSE,
                    'desrc' => 'does not write the generated SQL script to the standard output'
                )
            ),
            'callback' => array(dbdeploy\CommandProcessor::factory('apply'), 'execute')
        ),
        'revert' => array(
            'description' => 'Reverts the database to a given revision.',
            'arguments' => array(
                '--revision' => array(
                    'alias' => '-r',
                    'descr' => 'the target revision number',
                    'required' => TRUE
                ),
                '--delta-set' => array(
                    'alias' => '-s',
                    'descr' => 'the delta set to be reverted to the target revision'
                ),
                '--changelog-table' => array(
                    'alias' => '-c',
                    'descr' => 'the name of the changelog table in the database which stores the applies revisions',
                    'required' => FALSE,
                ),
                '--connection' => array(
                    'alias' => '-n',
                    'descr' => 'the name of the DB config (connection) to be used',
                    'required' => FALSE
                ),
                '--src-dir' => array(
                    'alias' => '-d',
                    'descr' => 'the root directory of database revision files',
                    'required' => FALSE,
                ),
                '--exec' => array(
                    'alias' => '-e',
                    'required' => FALSE,
                    'descr' => 'executes the created SQL script, not only writes to standard output'
                ),
                '--quiet' => array(
                    'alias' => '-q',
                    'required' => FALSE,
                    'desrc' => 'does not write the generated SQL script to the standard output'
                )
            ),
            'callback' => array(dbdeploy\CommandProcessor::factory('revert'), 'execute')
        ),
        'diff' => array(
            'description' => 'Shows diff between two revisions',
            'arguments' => array(
                '--revision' => array(
                    'alias' => '-r',
                    'descr' => 'the number of the two revisions to be diffed. These must be separated by 2 dots, eg: 5..6 or 15..7',
                    'required' => TRUE
                ),
                '--delta-set' => array(
                    'alias' => '-s',
                    'descr' => 'the delta set to be reverted to the target revision',
                    'required' => FALSE
                ),
                '--changelog-table' => array(
                    'alias' => '-c',
                    'descr' => 'the name of the changelog table in the database which stores the applies revisions',
                    'required' => FALSE,
                    'default' => NULL
                ),
                '--connection' => array(
                    'alias' => '-n',
                    'descr' => 'the name of the DB config (connection) to be used',
                    'required' => FALSE,
                    'default' => 'default'
                ),
                '--src-dir' => array(
                    'alias' => '-d',
                    'descr' => 'the root directory of database revision files',
                    'required' => FALSE,
                    'default' => NULL
                ),
                '--exec' => array(
                    'alias' => '-e',
                    'required' => FALSE,
                    'default' => FALSE,
                    'descr' => 'executes the created SQL script, not only writes to standard output'
                ),
                '--quiet' => array(
                    'alias' => '-q',
                    'required' => FALSE,
                    'default' => FALSE,
                    'desrc' => 'does not write the generated SQL script to the standard output'
                )
            ),
            'callback' => array(dbdeploy\CommandProcessor::factory('diff'), 'execute')
        )
    )
);