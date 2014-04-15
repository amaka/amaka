<?php

namespace Officine\Amaka\ErrorReporting;

class Error extends \Exception implements ErrorInterface
{
    private $resolutions;
    private $longMessage;

    public function __construct($message = "", $longMessage = "", array $resolutions = [])
    {
        $this->resolutions = $resolutions;
        $this->longMessage = $longMessage;

        parent::__construct($message);
    }

    public function getLongMessage()
    {
        return $this->longMessage;
    }

    public function getResolutions()
    {
        return $this->resolutions;
    }
}
