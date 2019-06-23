<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Finder;

/**
 * @package ActiveCollab\DatabaseMigrations\Finder
 */
interface FinderInterface
{
    /**
     * Return migration class name -> class file path map.
     *
     * @return array
     */
    public function getMigrationClassFilePathMap();

    /**
     * Prepare migration file path based on classified migration name (DoSomethingAwesome) and optional extra arguments.
     *
     * @param  string      $classified_name
     * @param  string|null $migrations_dir
     * @param  array       $extra_arguments
     * @return string
     */
    public function prepareMigrationPath($classified_name, $migrations_dir = null, ...$extra_arguments);
}
