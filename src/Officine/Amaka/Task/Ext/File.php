<?php

namespace Officine\Amaka\Task\Ext;

use Officine\Amaka\Task\Task;

class File extends Task
{
    public function getFilename()
    {
        return $this->getName();
    }
}
