<?php

namespace Creode\Csmt\Command\Snapshot\Database;

use Creode\Csmt\Command\Snapshot\SnapshotDetails;

class SnapshotDetailsCommand extends SnapshotDetails
{
    protected function configure()
    {
        $this->setName('snapshot:database:info');
        $this->setDescription('Returns info on the latest DB snapshot');
    }

    public function getSnapshotInfo()
    {
        $databases = $this->_config->get('databases');

        if (count($databases)) {
            foreach($databases as $filename => $databaseDetails) {
                $info = $this->_storage->info($databaseDetails['destination'], $databaseDetails['storage']);

                $file = new \Creode\Csmt\System\File($info['Key']);
                $file->date($info['LastModified'])
                    ->size($info['Size']);

                $this->addFileToResponse($file);
            }
        }

        $this->snapshotInfoSuccess();
    }
}
