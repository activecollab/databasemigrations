<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseConnection\Connection\MysqliConnection;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use mysqli;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var mysqli
     */
    protected $link;

    /**
     * @var MysqliConnection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $migrations_path;

    /**
     * Set up test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->log = new Logger('database_migrations_test', [new NullHandler()]);

        $this->link = new \MySQLi('localhost', 'root', $this->getValidMySqlPassword());

        if ($this->link->connect_error) {
            throw new RuntimeException('Failed to connect to database. MySQL said: ' . $this->link->connect_error);
        }

        if (!$this->link->select_db('activecollab_database_migrations_test')) {
            throw new RuntimeException('Failed to select database');
        }

        $this->connection = new MysqliConnection($this->link);

        if ($triggers = $this->connection->execute('SHOW TRIGGERS')) {
            foreach ($triggers as $trigger) {
                $this->connection->execute('DROP TRIGGER ' . $this->connection->escapeFieldName($trigger['Trigger']));
            }
        }

        $this->connection->execute('SET foreign_key_checks = 0;');
        foreach ($this->connection->getTableNames() as $table_name) {
            $this->connection->dropTable($table_name);
        }
        $this->connection->execute('SET foreign_key_checks = 1;');

        $this->migrations_path = dirname(__DIR__) . '/changesets/migrations_in_changesets';
        $this->assertFileExists($this->migrations_path);
    }

    /**
     * Tear down test environment.
     */
    public function tearDown()
    {
        if ($triggers = $this->connection->execute('SHOW TRIGGERS')) {
            foreach ($triggers as $trigger) {
                $this->connection->execute('DROP TRIGGER ' . $this->connection->escapeFieldName($trigger['Trigger']));
            }
        }

        $this->connection->execute('SET foreign_key_checks = 0;');
        foreach ($this->connection->getTableNames() as $table_name) {
            $this->connection->dropTable($table_name);
        }
        $this->connection->execute('SET foreign_key_checks = 1;');

        $this->connection = null;
        $this->link->close();

        parent::tearDown();
    }

    protected function getValidMySqlPassword(): string
    {
        return (string) getenv('DATABASE_CONNECTION_TEST_PASSWORD');
    }
}
