<?php

namespace League\Flysystem;

use LogicException;

class Util
{
    /**
     * Get normalized pathinfo
     *
     * @param   string  $path
     * @return  array   pathinfo
     */
    public static function pathinfo($path)
    {
        $pathinfo = pathinfo($path) + compact('path');
        $pathinfo['dirname'] = static::normalizeDirname($pathinfo['dirname']);

        return $pathinfo;
    }

    /**
     * Normalize a dirname return value
     *
     * @param   string  $dirname
     * @return  string  normalized dirname
     */
    public static function normalizeDirname($dirname)
    {
        if ($dirname === '.') {
            return '';
        }

        return $dirname;
    }

    /**
     * Get a normalized dirname from a path
     *
     * @param   string  $path
     * @return  string  dirname
     */
    public static function dirname($path)
    {
        return static::normalizeDirname(dirname($path));
    }

    /**
     * Map result arrays
     *
     * @param   array  $object
     * @param   array  $map
     * @return  array  mapped result
     */
    public static function map(array $object, array $map)
    {
        $result = array();

        foreach ($map as $from => $to) {
            if ( ! isset($object[$from]))
                continue;

            $result[$to] = $object[$from];
        }

        return $result;
    }

    /**
     * Normalize path
     *
     * @param   string  $path
     * @param   string  $separator
     * @return  string  normalized path
     */
    public static function normalizePath($path, $separator = '\\/')
    {
        // Remove any kind of funky unicode whitespace
        $normalized = preg_replace('#\p{C}+|^\./#u', '', $path);

        // Path remove self referring paths ("/./").
        $normalized = preg_replace('#/\.(?=/)|^\./|\./$#', '', $normalized);

        // Regex for resolving relative paths
        $regex = '#/*[^/\.]+/\.\.#Uu';

        while (preg_match($regex, $normalized)) {
            $normalized = preg_replace($regex, '', $normalized);
        }

        if (preg_match('#/\.{2}|\.{2}/#', $normalized)) {
            throw new LogicException('Path is outside of the defined root, path: [' . $path . '], resolved: [' . $normalized . ']');
        }

        return trim($normalized, $separator);
    }

    /**
     * Normalize prefix
     *
     * @param   string  $prefix
     * @param   string  $separator
     * @return  string  normalized path
     */
    public static function normalizePrefix($prefix, $separator)
    {
        return rtrim($prefix, $separator).$separator;
    }

    /**
     * Get content size
     *
     * @param   string  $contents
     * @return  int     content size
     */
    public static function contentSize($contents)
    {
        return mb_strlen($contents, '8bit');
    }

    /**
     * Guess MIME Type based on the path of the file and it's content
     *
     * @param  string $path
     * @param  string $content
     * @return string|null     MIME Type or NULL if no extension detected
     */
    public static function guessMimeType($path, $content)
    {
        $mimeType = Util\MimeType::detectByContent($content);

        if (empty($mimeType) || $mimeType === 'text/plain') {
            $extension = pathinfo($path, PATHINFO_EXTENSION);

            if ($extension) {
                $mimeType = Util\MimeType::detectByFileExtension($extension) ?: $mimeType;
            }
        }

        return $mimeType;
    }

    /**
     * Emulate directories
     *
     * @param   array  $listing
     * @return  array  listing with emulated directories
     */
    public static function emulateDirectories(array $listing)
    {
        $directories = array();

        foreach ($listing as $object) {
            if (empty($object['dirname'])) {
                continue;
            }

            $parent = $object['dirname'];

            while( ! empty($parent) && ! in_array($parent, $directories)) {
                $directories[] = $parent;

                $parent = static::dirname($parent);
            }
        }

        $directories = array_unique($directories);

        foreach ($directories as $directory) {
            $listing[] = static::pathinfo($directory) + array('type' => 'dir');
        }

        return $listing;
    }

    /**
     * Ensure a Config instance
     *
     * @param  string|array|Config  $config
     * @return Config  config instance
     * @throw  LogicException
     */
    public static function ensureConfig($config)
    {
        if ($config === null) {
            return new Config;
        }

        if ($config instanceof Config) {
            return $config;
        }

        // Backwards compatibility
        if (is_string($config)) {
            $config = array('visibility' => $config);
        }

        if (is_array($config)) {
            return new Config($config);
        }

        throw new LogicException('A config should either be an array or a Flysystem\Config object.');
    }
}
