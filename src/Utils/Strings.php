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
}
