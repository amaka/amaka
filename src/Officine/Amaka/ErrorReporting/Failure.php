<?php

namespace Officine\Amaka\ErrorReporting;

class Failure extends \ErrorException implements ErrorInterface
{
    private $resolutions;
    private $longMessage;

    public function __construct($message = "", $longMessage = "", array $resolutions = [], $fileName = null, $fileLine = null, $previous = null)
    {
        $this->resolutions = $resolutions;
        $this->longMessage = $longMessage;

        // 0 is the error code
        // 1 is the severity
        parent::__construct($message, 0, 1, $fileName, $fileLine, $previous);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getLongMessage()
    {
        return $this->longMessage;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getResolutions()
    {
        return $this->resolutions;
    }
}
