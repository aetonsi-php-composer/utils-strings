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
        $length = 64,
        $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) {
        if ($length < 1) {
            throw new \RangeException('Length must be a positive integer');
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
     * @param bool $noNamedEntities if true, all named entities are converted to numberic entities
     * @param bool $forceNumericEntities if true, ALL the characters are converted to numeric entities
     * @return string
     */
    public static function htmlEntitiesFull($str, $noNamedEntities = false, $forceNumericEntities = false)
    {
        $str = \mb_convert_encoding($str, 'UTF-32', 'UTF-8');
        $codepoints = \unpack('N*', $str);
        $result = \array_map(function ($codepoint) use ($noNamedEntities, $forceNumericEntities) {
            if ($forceNumericEntities) {
                // convert ALL characters to html numeric entities
                $result =  "&#$codepoint;";
            } else {
                $character = \mb_convert_encoding("&#$codepoint;", 'UTF-8', 'HTML-ENTITIES');
                $characterHtmlNamedEntity = \htmlentities($character, \ENT_QUOTES);
                if (!$noNamedEntities && $characterHtmlNamedEntity !== $character) {
                    // html named entity exists! using it
                    $result = $characterHtmlNamedEntity;
                } elseif (\mb_check_encoding($character, 'ASCII')) { // OR $codepoint < 128
                    // is ascii? just use the character
                    $result = $character;
                } else {
                    // unicode, not ascii, without html named entity; use the html numeric entity
                    $result = "&#$codepoint;";
                }
            }

            return $result;
        }, $codepoints);

        return \implode('', $result);
    }


    /**
     * Un-does unicode escape sequences (eg. "\uXXXX\uYYYY") in $text, using \json_decode. Equivalent of PHP7+ "\u{XXXX}\u{YYYY}".
     *
     * @see https://wiki.php.net/rfc/unicode_escape
     * @see https://stackoverflow.com/questions/2934563/how-to-decode-unicode-escape-sequences-like-u00ed-to-proper-utf-8-encoded-cha#comment92828689_7981441
     *
     * @param string $text
     * @return string
     */
    public static function unicodeUnescape($text)
    {
        return \json_decode('"' . \str_replace('"', '\\"', $text) . '"');
    }
}
