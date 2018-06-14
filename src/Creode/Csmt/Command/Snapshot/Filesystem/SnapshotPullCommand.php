<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\SnapshotPull;

class SnapshotPullCommand extends SnapshotPull
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem:pull');
        $this->setDescription('Pull latest filesystem snapshot');
    }

    public function pullSnapshot()
    {
        $filesystem = $this->_config->get('filesystem');

        if (is_array($filesystem) && count($filesystem)) {
            foreach($filesystem as $label => $details) {
                $zipFilename = strtolower($details['zip_dir']) . '.zip';
                $localFilePath = $this->getLocalStorageDir() . $details['remote_dir'] . DIRECTORY_SEPARATOR . $zipFilename;

                // support for old versions of csmt.yml where `destination` was a full file path
                $destination = isset($details['remote_dir'])
                    ? $details['remote_dir'] . '/' . $zipFilename
                    : $details['destination'];

                $storage = $this->getStorageDetails($details['storage']['general']);

                $this->_storage->pull($destination, $localFilePath, $storage);
            }
        }

        $this->sendSuccessResponse('Pulled filesystem snapshots from remote storage');
    }
}
