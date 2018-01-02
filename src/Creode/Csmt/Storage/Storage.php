<?php

namespace Creode\Csmt\Storage;

interface Storage
{
    public function transfer($source, $dest, array $storageDetails);

    public function info($source, array $storageDetails);
}
