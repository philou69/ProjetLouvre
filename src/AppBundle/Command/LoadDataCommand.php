<?php


namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:load-datas')

            // the short description shown while running "php bin/console list"
            ->setDescription('Load datas fixtures.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to load all fixtures in the db...')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'coucou';
    }

}