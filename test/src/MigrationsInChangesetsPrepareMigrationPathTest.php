<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Test;

use ActiveCollab\DatabaseMigrations\Finder\MigrationsInChangesetsFinder;
use ActiveCollab\DateValue\DateTimeValue;

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

    /**
     * {@inheritdoc}
     */
    public function setUp()
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Directory 'some other dir' is not managed by this finder
     */
    public function testMigrationsDirMustBeAlreadySetInFinder()
    {
        $this->finder->prepareMigrationPath('DoSomethingAwesome', 'some other dir');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Migrations dir is optional, but it can't be empty when specified
     */
    public function testMigrationsDirCantBeEmpty()
    {
        $this->finder->prepareMigrationPath('DoSomethingAwesome', '');
    }

    /**
     * Test if migrations dir can be specified.
     */
    public function testMigrationsDirCanBeSpecified()
    {
        $this->assertEquals("{$this->migrations_path}/{$this->timestamp}-do-something-awesome/DoSomethingAwesome.php", $this->finder->prepareMigrationPath('DoSomethingAwesome', $this->migrations_path));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Changeset argument is optional, but it can't be empty when specified
     */
    public function testChangesetCantBeEmptyWhenSpecified()
    {
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
