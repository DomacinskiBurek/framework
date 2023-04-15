<?php

namespace DomacinskiBurek\System\Error;

use Exception;
use DomacinskiBurek\System\FileSystem;
use DomacinskiBurek\System\Error\Handlers\DirectoryFailure;
use DomacinskiBurek\System\Error\Handlers\FileNotFound;
use DomacinskiBurek\System\Error\Handlers\FileNotWrittable;
use DomacinskiBurek\System\Error\Handlers\NotFound;
use DomacinskiBurek\System\Filesystem\File;
use DomacinskiBurek\System\System;
use DomacinskiBurek\System\View;
use SplFileObject;

class Error extends Exception
{
    final public function __toString() : string
    {
        $this->__LogError();

        try {
            error_reporting(0);
            exit(
                View::render('Error.500',
                    [
                        'errorMessage' => $this->getMessage(),
                        'errorTitle'   => 'System Error'
                    ],
                    500
                )
            );
        } catch (NotFound $error) {
            error_reporting(E_ALL);
            return parent::__toString();
        }
    }

    final public function __LogError ()
    {
        $location  = System::GetDirectory();
        $separator = System::GetSeparator();

        $location .= $separator . "Cache";

        $file = new SplFileObject("$location/error_log.log", 'a');

        //if (!is_dir($location)) $directory->Create($location);

        $logFormat = <<<EOB
        --------------------------------------
        [] - Code {$this->getCode()}
        [] - Line {$this->getLine()}
        [] - Message {$this->getMessage()}
        [] - File {$this->getFile()}
        [] - Trace {$this->getTraceAsString()}
        --------------------------------------
        EOB;
        $logFormat .= PHP_EOL;

        $file->fwrite($logFormat);
    }
}