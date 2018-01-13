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
            if ($this->isLiveEnvironment()) {
                array_walk($filesystem, array($this, 'getLiveSnapshotInfo'));
            } else {
                array_walk($filesystem, array($this, 'getTestSnapshotInfo'));
            }
        }

        $this->snapshotInfoSuccess();
    }
}
