<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotDownload;

class SnapshotDownloadCommand extends SnapshotDownload
{
    protected function configure()
    {
        $this->setName('snapshot:database:download');
        $this->setDescription('Downloads latest DB snapshot');

        parent::configure();
    }

    public function downloadSnapshot($duration)
    {
        $databases = $this->_config->get('databases');

        $links = [];

        if (is_array($databases) && count($databases)) {
            foreach($databases as $filename => $databaseDetails) {
                $links[$filename] = $this->_storage->downloadLink($databaseDetails['destination'], $duration, $databaseDetails['storage']);
            }
        }

        if (!count($links)) {
            $this->sendErrorResponse('There were no databases to download');
        }

        $this->sendSuccessResponse([
            'message' => 'Created download links from remote storage',
            'links' => $links
        ]);
    }
}
