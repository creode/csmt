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

        if (!file_exists($this->getLocalStorageDir())) {
            mkdir($this->getLocalStorageDir(), 0755, true);
        }

        if (count($filesystem)) {
            foreach($filesystem as $label => $details) {
                $zipFilename = strtolower($details['zip_dir']) . '.zip';
                $localFile = $this->getLocalStorageDir() . $zipFilename;

                if (!is_writeable(dirname($localFile)) || (file_exists($localFile) && !is_writeable($localFile))) {
                    throw new \Exception('Cannot write file ' . $localFile);
                }

                if (!file_exists($details['parent_dir'])) {
                    throw new \Exception('parent_dir does not exist, expected ' . $details['parent_dir']);
                }

                if (!file_exists($details['parent_dir'] . DIRECTORY_SEPARATOR . $details['zip_dir'])) {
                    throw new \Exception('zip_dir does not exist, expected ' . $details['parent_dir'] . DIRECTORY_SEPARATOR . $details['zip_dir']);
                }

                $excludes = isset($details['exclude']) && count($details['exclude'])
                    ? sprintf(' -x \*%s\*', implode('\* -x \*', $details['exclude']))
                    : '';

                // TODO: This shouldn't always be zip
                exec('cd ' . $details['parent_dir'] . ' && zip -r ' . $localFile . ' ' . $details['zip_dir'] . $excludes);

                $this->pushToStorage($localFile, $details['remote_dir'], $zipFilename, $details['storage']['general']);
            }
        }

        $this->sendSuccessResponse('Took file snapshot and stored safely');
    }
}
