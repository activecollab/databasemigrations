# Database Migrations

## Finders

Migration classes are "discovered" using objects that implement `ActiveCollab\DatabaseMigrations\Finder\FinderInterface` interface.

### Migrations in a Changeset Finder

What we found to work really well for [Active Collab](https://www.activecollab.com/index.html) project are migrations that are grouped in changesets. A changeset is a directory that has one or more related migrations. Valid format of the changeset directory name is `YYYY-MM-DD-what-this-is-all-about`. Here's a couple of valid changeset names:

* `2016-01-02-add-invoicing-module`
* `2016-03-12-remove-is-trashed-project-field`
* `2016-12-09-fix-users-table-indexes`

Timestamp part of the changeset name is used for sorting, and details part is used to make it clear what the changeset is all about.