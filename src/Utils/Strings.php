<?php

namespace Aetonsi\Utils;

class Strings
{
    /**
     * Generates a random string of the given length, composed of the given characters.
     *
     * Adapted from: https://stackoverflow.com/a/31107425
     *
     * @param int $length
     * @param string $keyspace
     * @throws \RangeException on $length < 1
     * @return string
     */
    public static function random_str(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = \mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[\random_int(0, $max)];
        }
        return \implode('', $pieces);
    }


    /**
     * Equivalent of htmlentities, that also converts to html numeric entities to non-ascii characters that have no named entity. It can effectively be used for any unicode text.
     *
     * Adapted from: https://stackoverflow.com/a/3005240
     *
     * @param string $str input string
     * @param bool $forceNumericEntities if true, ALL the characters are converted to html numeric entities
     * @return string
     */
    public static function htmlEntitiesFull($str, $forceNumericEntities = false)
    {
        $str = \mb_convert_encoding($str, 'UTF-32', 'UTF-8');
        $codepoints = \unpack('N*', $str);
        $result = \array_map(function ($codepoint) use ($forceNumericEntities) {
            if ($forceNumericEntities) {
                // convert ALL characters to html numeric entities
                $result =  "&#$codepoint;";
            } else {
                $character = \mb_convert_encoding("&#$codepoint;", 'UTF-8', 'HTML-ENTITIES');
                $characterHtmlNamedEntity = \htmlentities($character, \ENT_QUOTES);
                if ($characterHtmlNamedEntity !== $character) {
                    // html named entity escape exists! using it
                    $result = $characterHtmlNamedEntity;
                } elseif (\mb_check_encoding($character, 'ASCII')) { // OR $codepoint < 128
                    // is ascii? just use the character
                    $result = $character;
                } else {
                    // utf8 entity, not ascii, without html named escape sequence; use the html numeric entity
                    $result = "&#$codepoint;";
                }
            }

            return $result;
        }, $codepoints);

        return \implode('', $result);
    }
}
