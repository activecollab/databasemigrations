<?php

/*
 * This file is part of the Active Collab DatabaseMigrations project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

namespace ActiveCollab\DatabaseMigrations\Command;

use ActiveCollab\DatabaseMigrations\MigrationsInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package ActiveCollab\DatabaseMigrations\Command
 */
trait All
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->getMigrations()->getMigrations();

        if ($migrations_count = count($migrations)) {
            if ($migrations_count === 1) {
                $output->writeln('<info>One</info> migration found:');
            } else {
                $output->writeln("<info>{$migrations_count}</info> migrations found:");
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

    /**
     * @return MigrationsInterface
     */
    abstract protected function &getMigrations();
}
