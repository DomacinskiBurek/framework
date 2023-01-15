<?php

namespace DomacinskiBurek\System\Filesystem;

use DirectoryIterator;
use Exception;
use DomacinskiBurek\System\Error\Handlers\DirectoryFailure;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use SplObjectStorage;
use stdClass;

class Storage
{
    // Constants

    const FORBIDDEN_EXTENSIONS = ['php', 'xml'];
    const ALLOWED_FOLDERS      = ['Cache', 'Language', 'Template', 'Application'];

    // Properties
    private SplObjectStorage $obj;
    private string $separator = '/';
    private static ?storage $instance = null;

    protected function __construct() { $this->obj = new SplObjectStorage(); }
    protected function __clone() { }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    private function set (stdClass $object): void
    {
        $this->obj->attach($object);
    }

    public function get (): SplObjectStorage
    {
        return $this->obj;
    }

    /**
     * @throws DirectoryFailure
     */
    private function directoryIterator (string $directory): SplObjectStorage
    {
        if ($this->forbiddenPathCheck($directory)) throw new DirectoryFailure('You cannot map directory which is forbidden!');

        $directory = new DirectoryIterator($directory);

        foreach ($directory as $item) {
            if ($item->isFile()) {
                if ($this->forbiddenFileCheck($item->getExtension())) continue;

                $this->set($this->createFileObject($item));
            }
        }

        return $this->get();
    }

    /**
     * @throws DirectoryFailure
     */
    private function directoryIteratorMap (string $directory, string $filename): object
    {
        if ($this->forbiddenFileCheck($filename)) throw new DirectoryFailure('You can\'t access forbidden file.');

        $recursion = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($recursion as $object) {
            if ($object->isExecutable() || $object->isLink() || $this->isDot($object->getFilename())) continue;
            if ($this->forbiddenPathCheck($object->getPath())) continue;
            if ($object->getFilename() === $filename) return $this->createFileObject($object);
        }

        throw new DirectoryFailure('Unable to find file you are searching for!');
    }

    private function forbiddenFileCheck (string $extension): bool
    {
        return in_array($extension, self::FORBIDDEN_EXTENSIONS, true);
    }

    private function forbiddenPathCheck (string $directory): bool
    {
        if (empty(self::ALLOWED_FOLDERS)) return false;

        $clear = $this->pathSeparatorConvert($directory);

        foreach (self::ALLOWED_FOLDERS as $folder) if (str_contains($clear, $folder)) return false;

        return true;
    }

    private function pathSeparatorConvert (string $directory): string
    {
        if (strpos(" $directory}", "\\")) {
            return (string) str_replace('\\', $this->separator, $directory);
        }

        return $directory;
    }

    private function createFileObject (SplFileInfo $object): object
    {
        return (object) [
            'fileName'      => $object->getFilename(),
            'filePath'      => $object->getPath(),
            'fileExtension' => $object->getExtension(),
            'fileReadable'  => $object->isReadable()
        ];

    }

    private function isDot (string $item): bool
    {
        return in_array($item, ['.', '..']);
    }

    /**
     * @throws DirectoryFailure
     */
    private function directoryGenerate (string $directory)
    {
        if ($this->forbiddenPathCheck($directory)) throw new DirectoryFailure('You cannot map directory which is forbidden!');

        if (is_dir($directory)) {
            return true;
        }

        $parts = explode($this->separator, $this->pathSeparatorConvert($directory));

        $path   = '';

        if (strtoupper(substr(PHP_OS,0,3)) === 'WIN') {
            $path .= $parts[0] . '/';
            unset($parts[0]);

            foreach ($parts as $dir) {
                $path .= "$dir/";
                if (!is_dir($path)) {
                    mkdir($path);
                }

                if (!is_dir($path)) {
                    return false;
                }
            }
        } else {
            foreach ($parts as $dir) {
                $path .= "/$dir";
                if (!is_dir($path)) {
                    mkdir($path);
                }

                if (!is_dir($path)) {
                    return false;
                }
            }
        }

        return true;
    }
    /**
     * @throws DirectoryFailure
     */
    public static function locateFile (string $filename, ?string $directory = null): object
    {
        if (strlen($filename) < 1) throw new DirectoryFailure('Please enter the filename you are searching for');

        $subclass = static::class;

        if (self::$instance !== $subclass) self::$instance = new static();


        $instance = self::$instance;

        return $instance->directoryIteratorMap((is_null($directory)) ? dirname(__DIR__) : $directory, $filename);
    }

    /**
     * @throws DirectoryFailure
     */
    public static function directoryMap (string $directory): SplObjectStorage
    {
        $subclass = static::class;

        if (self::$instance !== $subclass) self::$instance = new static();

        $instance = self::$instance;

        return $instance->directoryIterator($directory);
    }

    /**
     * @throws DirectoryFailure
     */
    public static function directoryGenerator ($directory)
    {
        $subclass = static::class;

        if (self::$instance !== $subclass) self::$instance = new static();

        $instance = self::$instance;

        return $instance->directoryGenerate($directory);
    }
}