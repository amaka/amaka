<?php

namespace Officine\Amaka\ErrorReporting\Formatter;

use Officine\Amaka\ErrorReporting\ErrorInterface;
use Officine\Amaka\ErrorReporting\Failure;

class ErrorFormatter
{
    public static function format(ErrorInterface $error)
    {
        $buffer = [];
        $buffer[] = self::formatColumn(self::formatErrorClass($error), $error->getMessage());

        if ($error->getLongMessage()) {
            $buffer[] = self::formatColumn('Message', $error->getLongMessage());
        }

        if ($error instanceof Failure && $error->getLine()) {
            $buffer[] = self::formatColumn('Location', self::formatErrorLocation($error));
        }

        if ($error->getResolutions()) {
            $buffer[] = self::formatSectionHeader('Troubleshooting');

            foreach ($error->getResolutions() as $resolution) {
                if ($resolution->getDescription()) {
                    $buffer[] = sprintf("- %s\n%s", $resolution->getName(), $resolution->getDescription());
                } else {
                    $buffer[] = sprintf("- %s", $resolution->getName());
                }
            }
        }

        return implode(PHP_EOL, $buffer);
    }

    public static function formatErrorLocation(\Exception $e)
    {
        return sprintf("%s:%d", $e->getFile(), $e->getLine());
    }

    public static function formatErrorClass(ErrorInterface $error)
    {
        if ($error instanceof Failure) {
            return 'Failure';
        }
        return 'Error';
    }

    public static function formatColumn($title, $content)
    {
        return sprintf("%-8s: %s", $title, $content);
    }

    public static function formatSectionHeader($title)
    {
        return sprintf("\n%s:", $title);
    }
}
