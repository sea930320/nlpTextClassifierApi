<?php

namespace App\Services;

class HelperService
{
    /**
     * @param string $prefix
     *
     * @return string
     */
    public function uniquePath($prefix) : string
    {
        while (1) {
            $rootPath = uniqid($prefix);
            if (!file_exists(public_path() . '/' . $rootPath)) {
                break;
            }
        }
        return $rootPath;
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function uniqueFileName($prefix) : string
    {
        while (1) {
            $rootPath = uniqid($prefix);
            if (!file_exists(public_path() . '/' . $rootPath)) {
                break;
            }
        }
        return str_replace($prefix, "", $rootPath);
    }
}
