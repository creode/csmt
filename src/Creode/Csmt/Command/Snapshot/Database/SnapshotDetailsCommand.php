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

        if (is_array($databases) && count($databases)) {
            if ($this->isLiveEnvironment()) {
                array_walk($databases, array($this, 'getLiveSnapshotInfo'));
            } else {
                array_walk($databases, array($this, 'getTestSnapshotInfo'));
            }
        }

        $this->snapshotInfoSuccess();
    }
}
