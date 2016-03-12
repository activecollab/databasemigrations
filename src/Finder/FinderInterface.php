<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
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
}
