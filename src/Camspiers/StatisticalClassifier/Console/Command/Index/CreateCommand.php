<?php

namespace Camspiers\StatisticalClassifier\Console\Command\Index;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input;
use Symfony\Component\Console\Output;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('index:create')
            ->setDescription('')
            ->addArgument(
                'name',
                Input\InputArgument::REQUIRED,
                'Name of index'
            );
    }

    protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
    {
    }
}