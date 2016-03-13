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
        new Migrations($this->connection, new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path), $this->log, '');
    }

    /**
     * Test if executed migrations table is created when requested.
     */
    public function testTableDoesNotExistUntilRequested()
    {
        $migrations = new Migrations($this->connection, new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path), $this->log);

        $this->assertNotContains('executed_database_migrations', $this->connection->getTableNames());
        $this->assertEquals('executed_database_migrations', $migrations->getTableName());
        $this->assertContains('executed_database_migrations', $this->connection->getTableNames());
    }

    /**
     * Test if table name can be cusgomized.
     */
    public function testTableNameCanBeCustomized()
    {
        $migrations = new Migrations($this->connection, new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path), $this->log, 'awesomeness');

        $this->assertNotContains('executed_database_migrations', $this->connection->getTableNames());
        $this->assertNotContains('awesomeness', $this->connection->getTableNames());
        $this->assertEquals('awesomeness', $migrations->getTableName());
        $this->assertNotContains('executed_database_migrations', $this->connection->getTableNames());
        $this->assertContains('awesomeness', $this->connection->getTableNames());
    }
}
