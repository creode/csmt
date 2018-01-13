<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\SnapshotTaker;

class SnapshotCommand extends SnapshotTaker
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem');
        $this->setDescription('Takes a filesystem snapshot');
    }

    public function takeSnapshot()
    {
        $filesystem = $this->_config->get('filesystem');

        if (!$this->systemCommandExists('zip')) {
            $this->sendErrorResponse('zip - command not found');
        }

        mkdir($this->getLocalStorageDir(), 0755, true);

        if (count($filesystem)) {
            foreach($filesystem as $label => $details) {
                // TODO: This shouldn't always be zip
                $outfile = $this->getLocalStorageDir() . $details['filename'];
                // echo 'cd ' . $details['parentdir'] . ' && zip -r ' . $outfile . ' ' . $details['dir']; exit;
                exec('cd ' . $details['parentdir'] . ' && zip -r ' . $outfile . ' ' . $details['dir']);

                $this->_storage->push($outfile, $details['destination'], $details['storage']);
            }
        }

        $this->sendSuccessResponse('Took file snapshot and stored safely');
    }
}
