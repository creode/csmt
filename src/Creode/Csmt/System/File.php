<?php

namespace Creode\Csmt\System;

class File
{
    /**
     * Constructor
     * @param string $name 
     * @return \Creode\Csmt\System\File
     */
    public function __construct(
        $name
    ) {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the file date
     * @param \DateTime $date 
     * @return \Creode\Csmt\System\File
     */
    public function date(\DateTime $date) {
        $this->date = $date;

        return $this;
    }

    /**
     * Sets the file size
     * @param int $size 
     * @return \Creode\Csmt\System\File
     */
    public function size($size) {
        $this->size = $size;

        return $this;
    }


}
