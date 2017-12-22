<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\Snapshot;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SnapshotCommand extends Snapshot
{
    protected function configure()
    {
        $this->setName('snapshot:database');
        $this->setDescription('Takes a DB snapshot');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->takeSnapshot();
    }

    public function takeSnapshot()
    {
        $databases = $this->_config->get('databases');

        if (count($databases)) {
            foreach($databases as $filename => $databaseDetails) {
                // TODO: This shouldn't always be mysql
                $outfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $databaseDetails['filename'];
                exec('mysqldump -h ' . $databaseDetails['host'] . ' -u ' . $databaseDetails['user'] . ' -p"' . $databaseDetails['pass'] . '" ' . $databaseDetails['name'] . ' > ' . $outfile);

                $this->_storage->transfer($outfile, $databaseDetails['destination'], $databaseDetails['storage']);
            }
        }

        $this->sendSuccessResponse('Took DB snapshot and stored safely');
    }
}
