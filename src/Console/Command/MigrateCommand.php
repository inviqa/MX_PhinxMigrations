<?php

namespace MX\PhinxMigrations\Console\Command;

use Magento\Framework\Console\Cli;
use Magento\Setup\Console\Command\DbStatusCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @package MX\PhinxMigrations
 * @author  James Halsall <james.halsall@inviqa.com>
 */
class MigrateCommand extends Command
{
    private const MIGRATE_TIMEOUT = 900;
    private const PHINX_BIN = 'vendor/robmorgan/phinx/bin/phinx';

    protected function configure()
    {
        $this
            ->setName('mx:db:migrate')
            ->setDescription('Migrates the database so it is ready for use with the current application code.');

        $this->addOption(
            'keep-generated',
            null,
            InputOption::VALUE_NONE,
            'Prevents generated files from being deleted during migration.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbStatusCommand = $this->getApplication()->get('setup:db:status');

        $output->writeln('<info>Checking if setup:upgrade is required...</info>');
        $exit = $dbStatusCommand->run(new ArrayInput([]), new NullOutput());

        if ($exit === DbStatusCommand::EXIT_CODE_UPGRADE_REQUIRED) {
            $output->writeln('<info>A setup:upgrade is required, running...</info>');
            $setupUpgradeCommand = $this->getApplication()->get('setup:upgrade');

            $setupUpgradeArgs = $input->getOption('keep-generated') ? ['--keep-generated' => true] : [];
            $setupUpgradeCommand->run(new ArrayInput($setupUpgradeArgs), $output);
        } else {
            $output->writeln('<info>No setup:upgrade required.</info>');
        }

        return $this->runPhinxMigrate();
    }

    protected function runPhinxMigrate(): int
    {
        $phinx = new Process(sprintf('%s migrate', self::PHINX_BIN));

        $phinx->setTimeout(self::MIGRATE_TIMEOUT);
        $phinx->run(function ($type, $buffer) {
            echo $buffer;
        });

        return $phinx->isSuccessful() ? Cli::RETURN_SUCCESS : Cli::RETURN_FAILURE;
    }
}
