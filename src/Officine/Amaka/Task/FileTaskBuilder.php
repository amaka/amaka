<?php

namespace Officine\Amaka\Task;

use Officine\Amaka\Task\Ext\File;

class FileTaskBuilder extends DefaultTaskBuilder
{
    public function invoke()
    {
        $task = $this->build();
        $rebuild = ! file_exists($this->name);

        if (! $rebuild) {
            $time = filemtime($this->name);
            foreach ($this->getAdjacencyList() as $file) {
                if (file_exists($file) && filemtime($file) > $time) {
                    $rebuild = true;
                    break;
                }
            }
        }

        if ($rebuild) {
            if (is_callable($this->getInvocationCallback())) {
                $ic = $this->getInvocationCallback();
                call_user_func_array($ic, array($task));
            }
            touch($this->name);
        }

        // invoke the callback
        $task->invoke();
    }

    public function build()
    {
        return new File($this->getName());
    }
}
