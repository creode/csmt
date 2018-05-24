<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotPull;

class SnapshotPullCommand extends SnapshotPull
{
    protected function configure()
    {
        $this->setName('snapshot:database:pull');
        $this->setDescription('Pull latest DB snapshot');
    }

    public function pullSnapshot()
    {
        $databases = $this->_config->get('databases');

        if (is_array($databases) && count($databases)) {
            foreach($databases as $filename => $databaseDetails) {
                $outfilename = SnapshotCommand::STRUCTURE_FILE_PREFIX . $databaseDetails['filename'];

                $this->pullFromStorage($outfilename, $databaseDetails);

                $outfilename = SnapshotCommand::DATA_FILE_PREFIX . $databaseDetails['filename'];

                $this->pullFromStorage($outfilename, $databaseDetails);
            }
        }

        $this->sendSuccessResponse('Pulled DB snapshots from remote storage');
    }

    private function pullFromStorage($fileName, $databaseDetails) {
        $localFilePath = $this->getLocalStorageDir() . $fileName;

        // support for old versions of csmt.yml where `destination` was a full file path
        $destination = isset($databaseDetails['remote_dir'])
                ? $databaseDetails['remote_dir'] . '/' . $fileName
                : $databaseDetails['destination'];

        $this->_storage->pull($destination, $localFilePath, $databaseDetails['storage']);
    }   
}
