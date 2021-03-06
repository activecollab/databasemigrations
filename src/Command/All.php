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

trait All
{
    use Base;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->getMigrations()->getMigrations();

        if ($migrations_count = count($migrations)) {
            $output->writeln('');

            if ($migrations_count === 1) {
                $output->writeln('<info>One migration</info> found:');
            } else {
                $output->writeln("<info>{$migrations_count} migrations</info> found:");
            }
            $output->writeln('');

            foreach ($migrations as $migration) {
                $execution_status = $this->getMigrations()->isExecuted($migration) ? '<info>Executed</info>' : '<comment>Not executed</comment>';
                $output->writeln('    <comment>*</comment> ' . get_class($migration) . " ($execution_status)");
            }

            $output->writeln('');
        } else {
            $output->writeln('No migrations found');
        }

        return 0;
    }
}
