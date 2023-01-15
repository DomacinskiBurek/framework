<?php

namespace DomacinskiBurek\System\Filesystem;

use BadMethodCallException;
use DomacinskiBurek\System\Error\Handlers\StreamException;
use SplFileObject;

/**
 * @method fread($getSize)
 * @method getSize()
 */
class File
{
    const FORBIDDEN_EXTENSIONS = ['php', 'xml'];
    private SplFileObject $object;
    /**
     * @throws StreamException
     */
    public function __construct(string $filename, string $mode = 'r', bool $useIncludePath = false, $context = null)
    {
        if ($this->forbiddenFileCheck($filename) === true) throw new StreamException('Forbidden file extension!');

        $this->object = new SplFileObject($filename, $mode, $useIncludePath, $context);
    }

    public function __call(string $method, array $args)
    {
        if (method_exists($this->object, $method) === false) throw new BadMethodCallException('Requested method does not exist!');

        return call_user_func_array([$this->object, $method], $args);
    }

    private function forbiddenFileCheck (string $filename): bool
    {
        $name      = substr(" $filename", strpos(" $filename", "."));
        $extension = substr(" $filename", (strlen(" $filename") - strlen($name)) + 1);

        return in_array($extension, self::FORBIDDEN_EXTENSIONS, true);
    }
}