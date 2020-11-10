<?php

namespace marklester\phpmvc;

class Storage
{
    static public function uploadFile($file, $location)
    {
        $supportedFormats = ['image/png', 'image/jpg', 'image/gif', 'image/jpeg'];

        if (is_array($file)) {
            if (in_array($file['type'], $supportedFormats)) {
                move_uploaded_file($file['tmp_name'], Application::$ROOT_DIR . '\public' . $location . $file['name']);
                return true;
            } else {
                return false;
            }
        }
    }
}