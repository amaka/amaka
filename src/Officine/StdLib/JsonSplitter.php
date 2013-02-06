<?php

namespace Officine\StdLib;

use Zend\Json\Json;

class JsonSplitter
{
    /**
     *
     *
     */
    public static function split($serial, $decodeType = Json::TYPE_OBJECT)
    {
        $json = self::fixMalformedArray($serial);
        $decoded = Json::decode($json, $decodeType);

        if (! is_array($decoded)) {
            return array($decoded);
        }
        return $decoded;
    }

    /**
     * Given a *malformed* json input consisting of non-separated objects
     * it returns a correct json representation as array of objects, e.g.
     * {type: "message"}{type: "another message"} > [{}, {type: "message"}, {type: "another message"}]
     * which can then be given as input to a json decoder.
     *
     * @param string $json
     * @return string The JSON array of objects as string
     */
    public static function fixMalformedArray($json)
    {
        if (false === strpos($json, '}{')) {
            return $json;
        }
        return '[' . str_replace('}{', '}, {', $json) . ']';
    }
}
