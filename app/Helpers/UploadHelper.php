<?php

if (!function_exists('upload_file')) {
    function upload_file($file, $folder)
    {
        return $file ? $file->store($folder, 'public') : null;
    }
}