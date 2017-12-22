<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\Snapshot;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SnapshotCommand extends Snapshot
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem');
        $this->setDescription('Takes a filesystem snapshot');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->takeSnapshot();
    }

    public function takeSnapshot()
    {
        $filesystem = $this->_config->get('filesystem');

        if (count($filesystem)) {
            foreach($filesystem as $label => $details) {
                // TODO: This shouldn't always be zip
                $outfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $details['filename'];
                // echo 'cd ' . $details['parentdir'] . ' && zip -r ' . $outfile . ' ' . $details['dir']; exit;
                exec('cd ' . $details['parentdir'] . ' && zip -r ' . $outfile . ' ' . $details['dir']);

                $this->_storage->transfer($outfile, $details['destination'], $details['storage']);
            }
        }

        $this->sendSuccessResponse('Took file snapshot and stored safely');
    }
}
