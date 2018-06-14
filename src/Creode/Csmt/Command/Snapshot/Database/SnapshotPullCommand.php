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
                $this->pullFromStorage(SnapshotCommand::STRUCTURE_FILENAME, $databaseDetails);
                $this->pullFromStorage(SnapshotCommand::DATA_FILENAME, $databaseDetails);
                $this->pullFromStorage(SnapshotCommand::OBFUSCATED_DATA_FILENAME, $databaseDetails);
            }
        }

        $this->sendSuccessResponse('Pulled DB snapshots from remote storage');
    }

    private function pullFromStorage($fileName, $databaseDetails) {
        $localFilePath = $this->getLocalStorageDir() . $databaseDetails['remote_dir'] . DIRECTORY_SEPARATOR . $fileName;

        // support for old versions of csmt.yml where `destination` was a full file path
        $destination = isset($databaseDetails['remote_dir'])
            ? $databaseDetails['remote_dir'] . '/' . $fileName
            : $databaseDetails['destination'];

        $storage = $this->getStorageDetails($databaseDetails['storage']['general']);

        $this->_storage->pull($destination, $localFilePath, $storage);
    }   
}
