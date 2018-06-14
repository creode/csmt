<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\SnapshotDownload;

class SnapshotDownloadCommand extends SnapshotDownload
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem:download');
        $this->setDescription('Downloads latest filesystem snapshot');

        parent::configure();
    }

    public function downloadSnapshot($duration)
    {
        $filesystem = $this->_config->get('filesystem');

        $links = [];

        if (is_array($filesystem) && count($filesystem)) {
            foreach($filesystem as $label => $details) {
                $zipFilename = strtolower($details['zip_dir']) . '.zip';
                $this->addFileToDownload($links, $zipFilename, $duration, $details);
            }
        }

        if (!count($links)) {
            $this->sendErrorResponse('There were no files to download');
        }

        $this->sendSuccessResponse([
            'message' => 'Created download links from remote storage',
            'links' => $links
        ]);
    }
}
