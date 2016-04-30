<?php

/*
 * This file is part of the overtrue/validation.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\Validation;

use ArrayAccess;
use Closure;

if (! function_exists('array_get')) {
    /**
     * Return array specific item.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (!array_accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (array_exists($array, $key)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (array_accessible($array) && array_exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }
}

if (! function_exists('array_accessible')) {
    /**
     * Check input is array accessable.
     *
     * @param mixed $value
     *
     * @return bool
     */
    function array_accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
}

if (! function_exists('array_exists')) {
    /**
     * Check array key exists.
     *
     * @param array  $array
     * @param string $key
     *
     * @return bool
     */
    function array_exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}

if (! function_exists('snake_case')) {
    /**
     * Convert a string to snake case.
     *
     * @param string $string
     * @param string $delimiter
     *
     * @return string
     */
    function snake_case($string, $delimiter = '_')
    {
        $replace = '$1'.$delimiter.'$2';

        return ctype_lower($string) ? $string : strtolower(preg_replace('/(.)([A-Z])/', $replace, $string));
    }
}

if (! function_exists('studly_case')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param string $string
     *
     * @return string
     */
    function studly_case($string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', $string));

        return str_replace(' ', '', $string);
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}