<?php

namespace Creode\Csmt\Command\Snapshot\Filesystem;

use Creode\Csmt\Command\Snapshot\SnapshotDetails;

class SnapshotDetailsCommand extends SnapshotDetails
{
    protected function configure()
    {
        $this->setName('snapshot:filesystem:info');
        $this->setDescription('Returns info on the latest filesystem snapshot');
    }

    public function getSnapshotInfo()
    {
        $filesystem = $this->_config->get('filesystem');

        if (count($filesystem)) {
            foreach($filesystem as $label => $details) {
                $info = $this->_storage->info($details['destination'], $details['storage']);

                $file = new \Creode\Csmt\System\File($info['Key']);
                $file->date($info['LastModified'])
                    ->size($info['Size']);

                $this->addFileToResponse($file);
            }
        }

        $this->snapshotInfoSuccess();
    }
}
