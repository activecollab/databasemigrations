<?php

/*
 * This file is part of the ActiveCollab DatabaseMigration project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\DatabaseMigrations\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait Up
{
    use Base;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->getMigrations()->getMigrations();

        if ($migrations_count = count($migrations)) {
            $output->writeln('');

            if ($migrations_count === 1) {
                $output->writeln('<info>One migration</info> found. Executing...');
            } else {
                $output->writeln("<info>{$migrations_count} migrations</info> found. Executing...");
            }
            $output->writeln('');

            $this->getMigrations()->up(function ($message) use ($output) {
                $output->writeln("    <comment>*</comment> $message");
            });

            $output->writeln('');
            $output->writeln('Done. Your database is up to date.');
            $output->writeln('');
        } else {
            $output->writeln('No migrations found');
        }

        return 0;
    }
}
