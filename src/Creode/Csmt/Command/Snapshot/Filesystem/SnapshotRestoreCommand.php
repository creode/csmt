<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotRestore;

class SnapshotRestoreCommand extends SnapshotRestore
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem:restore');
        $this->setDescription('Restores all filesystem snapshots');
    }

    public function restoreSnapshots()
    {
        $filesystem = $this->_config->get('filesystem');

        if (!$filesystem) {
            $this->sendErrorResponse('No files defined in config');
        } elseif (count($filesystem) == 0) {
            $this->sendErrorResponse('No file snapshots to restore');
        }

        if (!$this->systemCommandExists('unzip')) {
            $this->sendErrorResponse('unzip - command not found');
        }

        try {
            $dir = $this->getLocalStorageDir();

            foreach($filesystem as $label => $details) {
                // TODO: This shouldn't always be unzip
                $infile = $this->getLocalStorageDir() . $details['filename'];
                exec('cd ' . $details['parentdir'] . ' && unzip ' . $infile);
            }
        } catch (\Exception $e) {
            $this->sendErrorResponse('There was a problem restoring ' . $filename);
        }

        $this->sendSuccessResponse('Restored filesystem snapshots');
    }
}
