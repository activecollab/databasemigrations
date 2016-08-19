<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;
use ActiveCollab\DatabaseMigrations\Migrations;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
class TableCreationTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Table name is required
     */
    public function testTableNameIsRequired()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);
        new Migrations($this->connection, $finder, $this->log, '');
    }

    /**
     * Test if executed migrations table is created when requested.
     */
    public function testTableDoesNotExistUntilRequested()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);
        $migrations = new Migrations($this->connection, $finder, $this->log);

        $this->assertNotContains('executed_database_migrations', $this->connection->getTableNames());
        $this->assertEquals('executed_database_migrations', $migrations->getTableName());
        $this->assertContains('executed_database_migrations', $this->connection->getTableNames());
    }

    /**
     * Test if table name can be cusgomized.
     */
    public function testTableNameCanBeCustomized()
    {
        $finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);
        $migrations = new Migrations($this->connection, $finder, $this->log, 'awesomeness');

        $this->assertNotContains('executed_database_migrations', $this->connection->getTableNames());
        $this->assertNotContains('awesomeness', $this->connection->getTableNames());
        $this->assertEquals('awesomeness', $migrations->getTableName());
        $this->assertNotContains('executed_database_migrations', $this->connection->getTableNames());
        $this->assertContains('awesomeness', $this->connection->getTableNames());
    }
}
