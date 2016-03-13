# Database Migrations

[![Build Status](https://travis-ci.org/activecollab/databasemigrations.svg?branch=master)](https://travis-ci.org/activecollab/databasemigrations)

## Migrations

To write a migration, create a class that is discoverable by Finder that you are using, and extend `Migration` class:

```php
<?php

namespace Acme\App\Migrations;

use ActiveCollab\DatabaseMigrations\Migration\Migration;

class AddUserRolesTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
    }
}
```

If you don't like how `Migration` class is structured, you can write your migrations any way you want, as long as you implement `MigrationInterface`:

```php
<?php

namespace Acme\App\Migrations;

use ActiveCollab\DatabaseMigrations\Migration\MigrationInterface;

class AddUserRolesTable implements MigrationInterface
{
    …
}
```

Migrations that extend `Migration` class get two important properties injected via constructor:

1. `connection` - a `ActiveCollab\DatabaseConnection\ConnectionInterface` instance with valid connection to the database, and
2. `log` - a PSR-3 `LoggerInterface` instance.

```php
<?php

namespace Acme\App\Migrations;

use ActiveCollab\DatabaseMigrations\Migration\Migration;

class AddUserRolesTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        if (!in_array('user_roles', $this->connection->getTableNames()) {
            $this->log->debug('{table} not found in the database', ['table' => 'user_roles']);
            $thos->connection->execute('CREATE TABLE STATEMENT');
            $this->log->debug('{table} created', ['table' => 'user_roles']);
        }
    }
}
```

## Finders

Migration classes are "discovered" using objects that implement `FinderInterface` interface. All that we expect from finders is that they return an array where key is migration class name, and value is path where we can expect to find the class definition. This makes migration library independent on directory and file structure that you decide to use to organize your migrations.

This library currently implements only one Finder - Migrations in Changesets.

### Migrations in a Changeset Finder

What we found to work really well for [Active Collab](https://www.activecollab.com/index.html) project are migrations that are grouped in changesets. A changeset is a directory that has one or more related migrations. Valid format of the changeset directory name is `YYYY-MM-DD-what-this-is-all-about`. Here's a couple of valid changeset names:

* `2016-01-02-add-invoicing-module`
* `2016-03-12-remove-is-trashed-project-field`
* `2016-12-09-fix-users-table-indexes`

Timestamp part of the changeset name is used for sorting, and details part is used to make it clear what the changeset is all about.

## Command Line

Database Migrations package includes a couple of traits that make implementation of commands that work with migrations easy. These commands are:

* List all migrations and their status (All)
* Run all non-executed migrations (Up)
* Create a new migration file (Create)

In order to use these traits, you need to include them in a regular Symfony Console class, and provide implementation of `getMigrations()` method. This method needs to return a valid, configured `MigrationsInterface` instance.

Create helper has extra requirements and extension points that lets you configure how migration stubs are created. Please check protected and abstract methods under `Override` comment in \src\Command\Create.php file for details.
