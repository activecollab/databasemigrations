<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;
use ActiveCollab\DateValue\DateTimeValue;
use InvalidArgumentException;

/**
 * @package ActiveCollab\DatabaseMigrations\Test
 */
class MigrationsInChangesetsPrepareMigrationPathTest extends TestCase
{
    /**
     * @var MigrationsInChangesetsFinder
     */
    private $finder;

    /**
     * @var string
     */
    private $timestamp;

    public function setUp(): void
    {
        parent::setUp();

        $this->finder = new MigrationsInChangesetsFinder($this->log, '', $this->migrations_path);
        $this->timestamp = (new DateTimeValue())->format('Y-m-d');
    }

    /**
     * Happy path test.
     */
    public function testPathBasedOnMigrationClassName()
    {
        $this->assertEquals("{$this->migrations_path}/{$this->timestamp}-do-something-awesome/DoSomethingAwesome.php", $this->finder->prepareMigrationPath('DoSomethingAwesome'));
    }

    public function testMigrationsDirMustBeAlreadySetInFinder()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Directory 'some other dir' is not managed by this finder");

        $this->finder->prepareMigrationPath('DoSomethingAwesome', 'some other dir');
    }

    public function testMigrationsDirCantBeEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Migrations dir is optional, but it can't be empty when specified");

        $this->finder->prepareMigrationPath('DoSomethingAwesome', '');
    }

    /**
     * Test if migrations dir can be specified.
     */
    public function testMigrationsDirCanBeSpecified()
    {
        $this->assertEquals("{$this->migrations_path}/{$this->timestamp}-do-something-awesome/DoSomethingAwesome.php", $this->finder->prepareMigrationPath('DoSomethingAwesome', $this->migrations_path));
    }

    public function testChangesetCantBeEmptyWhenSpecified()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Changeset argument is optional, but it can't be empty when specified");

        $this->finder->prepareMigrationPath('DoSomethingAwesome', $this->migrations_path, '');
    }

    public function testChangesetCanBeSpcifiedWithoutTimestamp()
    {
        $this->assertEquals("{$this->migrations_path}/{$this->timestamp}-my-custom-changeset-name/DoSomethingAwesome.php", $this->finder->prepareMigrationPath('DoSomethingAwesome', $this->migrations_path, 'my custom changeset name'));
    }

    public function testChangesetCanBeSpcifiedWithTimestamp()
    {
        $this->assertEquals("{$this->migrations_path}/2013-10-02-my-custom-changeset-name/DoSomethingAwesome.php", $this->finder->prepareMigrationPath('DoSomethingAwesome', $this->migrations_path, '2013-10-02-my custom changeset name'));
    }
}
