<?php

namespace Officine\Amaka\ErrorReporting;

class Trigger
{
    const AMAKA_ERROR = 'error';
    const AMAKA_FAILURE = 'failure';

    use ResolutionTrait;

    private $message;
    private $longMessage;
    private $fileName;
    private $fileLine;

    private $triggeringError;
    private $triggeringErrorType;

    public function __construct($errorType = self::AMAKA_ERROR)
    {
        $this->triggeringErrorType = $errorType;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function setFileLine($fileLine)
    {
        $this->fileLine = $fileLine;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setLongMessage($longMessage)
    {
        $this->longMessage = $longMessage;
        return $this;
    }

    public function trigger()
    {
        throw $this->build();
    }

    public function build()
    {
        if ($this->triggeringError) {
            return $this->triggeringError;
        }

        if (self::AMAKA_ERROR == $this->triggeringErrorType) {
            $this->triggeringError = new Error(
                $this->message,
                $this->longMessage,
                $this->getResolutions());
        }

        if (self::AMAKA_FAILURE == $this->triggeringErrorType) {
            $this->triggeringError = new Failure(
                $this->message,
                $this->longMessage,
                $this->getResolutions(),
                $this->fileName,
                $this->fileLine
            );
        }

        return $this->triggeringError;
    }

    public static function fromException(\Exception $e)
    {
        return self::error()
            ->setMessage($e->getMessage())
            ->setFileName($e->getFile())
            ->setFileLine($e->getLine());
    }

    public static function error($message = "", $longMessage = "", $resolutions = [])
    {
        $error = new self(self::AMAKA_ERROR);
        return $error->setMessage($message)
                     ->setLongMessage($longMessage)
                     ->addResolutions($resolutions);
    }

    public static function failure($message = "", $longMessage = "", $resolutions = [])
    {
        $failure = new self(self::AMAKA_FAILURE);
        return $failure->setMessage($message)
                       ->setLongMessage($longMessage)
                       ->addResolutions($resolutions);
    }
}
