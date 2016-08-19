<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;
use ActiveCollab\DatabaseMigrations\Migrations;
use ActiveCollab\DatabaseMigrations\MigrationsInterface;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
class MigrationExecutedFlagTest extends TestCase
{
    /**
     * @var MigrationsInterface
     */
    private $migrations;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $finder = new MigrationsInChangesetsFinder($this->log, 'ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations', $this->migrations_path);
        $this->migrations = new Migrations($this->connection, $finder, $this->log);
    }

    /**
     * Test if migrations are not executed by default.
     */
    public function testMigrationsAreNotExecutedByDefault()
    {
        foreach ($this->migrations->getMigrations() as $migration) {
            $this->assertFalse($this->migrations->isExecuted($migration));
        }
    }

    /**
     * Test if migration is marked as executed once executed.
     */
    public function testMigrationIsMarkedAsExecutedWhenExecuted()
    {
        $migration = $this->migrations->getMigrations()[0];

        $this->assertFalse($this->migrations->isExecuted($migration));
        $this->migrations->execute($migration);
        $this->assertTrue($this->migrations->isExecuted($migration));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Migration ActiveCollab\DatabaseMigrations\Test\NamepsacedMigrations\AddUsersTable is already executed
     */
    public function testMigrationsCantBeExecutedTwice()
    {
        $migration = $this->migrations->getMigrations()[0];

        $this->migrations->execute($migration);
        $this->migrations->execute($migration);
    }

    /**
     * Test if all migrations can be marked as executed.
     */
    public function testAllMigrationsCanBeSetAsExecuted()
    {
        $migrations = $this->migrations->getMigrations();

        foreach ($migrations as $migration) {
            $this->assertFalse($this->migrations->isExecuted($migration));
        }

        $this->migrations->setAllAsExecuted();

        foreach ($migrations as $migration) {
            $this->assertTrue($this->migrations->isExecuted($migration));
        }
    }
}
