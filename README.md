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

## Finders

Migration classes are "discovered" using objects that implement `FinderInterface` interface. All that we expect from finders is that they return an array of where key is migration class name, and value is path where we can expect to find the class definition. This makes migration library independent on directory and file structure that you decide to use to organize your migrations.

### Migrations in a Changeset Finder

What we found to work really well for [Active Collab](https://www.activecollab.com/index.html) project are migrations that are grouped in changesets. A changeset is a directory that has one or more related migrations. Valid format of the changeset directory name is `YYYY-MM-DD-what-this-is-all-about`. Here's a couple of valid changeset names:

* `2016-01-02-add-invoicing-module`
* `2016-03-12-remove-is-trashed-project-field`
* `2016-12-09-fix-users-table-indexes`

Timestamp part of the changeset name is used for sorting, and details part is used to make it clear what the changeset is all about.