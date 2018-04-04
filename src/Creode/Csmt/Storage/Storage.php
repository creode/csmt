<?php

namespace Creode\Csmt\Storage;

interface Storage
{
    public function push($source, $dest, array $storageDetails);

    public function pull($source, $dest, array $storageDetails);

    public function info($source, array $storageDetails);

    public function downloadLink($source, $validFor, array $storageDetails);
}
