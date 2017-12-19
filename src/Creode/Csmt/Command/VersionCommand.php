<?php
namespace Creode\Csmt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class VersionCommand extends Command
{    
    const MAJOR = 1;
    const MINOR = 2;
    const PATCH = 3;

    protected function configure()
    {
        $this->setName('version');
        $this->setDescription('Returns version details');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        echo 'this is a silly idea. Just get it by running --version on the phar file' . PHP_EOL;
    }
}
