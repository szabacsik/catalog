<?php

namespace Szabacsik\Catalog;

use Exception;
use Generator;
use RuntimeException;
use SplFileObject;

class CatalogFile implements CatalogInterface
{
    private const FILENAME = 'catalog.dat';
    private const EOL = "\n";
    /**
     * @var string
     */
    private $workingDirectory;
    /**
     * @var string
     */
    private $dataFile;

    /**
     * @var resource|false|null
     * @psalm-var resource|closed-resource|false|null
     */
    private $handle;

    /**
     * @throws \Exception
     */
    public function __construct(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
        if (!is_dir($this->workingDirectory)) {
            throw new Exception('Working directory `' . $this->workingDirectory . '` doesnt`t exist.');
        }
        if (!is_writable($this->workingDirectory)) {
            throw new Exception('Working directory `' . $this->workingDirectory . '` is not writable.');
        }
        $this->dataFile = $this->workingDirectory . DIRECTORY_SEPARATOR . self::FILENAME;
        if (!file_exists($this->dataFile)) {
            touch($this->dataFile);
        }
        if (!is_writable($this->dataFile)) {
            throw new Exception('Datafile `' . $this->dataFile . '` is not writable.');
        }
        $this->handle = null;
    }

    /**
     * @inheritDoc
     * @throws \RuntimeException
     */
    public function add(string $item): bool
    {
        $file = new SplFileObject($this->dataFile, 'a');
        $result = (bool)$file->fwrite($item . self::EOL);
        unset($file);
        if (!$result) {
            throw new RuntimeException('Failed to write `' . $this->dataFile . '` file.');
        }
        return true;
    }

    /**
     * @return \Generator
     */
    private function read(): Generator
    {
        $this->handle = @fopen($this->dataFile, 'r');
        while (($buffer = fgets($this->handle, 256)) !== false) {
            yield $buffer;
        }
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    /**
     * @inheritDoc
     */
    public function findFirst(string $regularExpressionPattern): string
    {
        RegularExpressionValidator::validate($regularExpressionPattern);
        $item = '';
        foreach ($this->read() as $_buffer) {
            if (preg_match($regularExpressionPattern, (string)$_buffer)) {
                $item = trim((string)$_buffer);
                break;
            }
        }
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
        return $item;
    }

    /**
     * @inheritDoc
     * @return array<int, string>
     */
    public function findAll(string $regularExpressionPattern): iterable
    {
        RegularExpressionValidator::validate($regularExpressionPattern);
        $items = [];
        foreach ($this->read() as $_buffer) {
            if (preg_match($regularExpressionPattern, (string)$_buffer)) {
                $items[] = trim((string)$_buffer);
            }
        }
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
        return $items;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $item): int
    {
        return 0;
        // TODO: Implement remove() method.
    }

    public function truncate(): bool
    {
        $handle = fopen($this->dataFile, 'w');
        fclose($handle);
        return !filesize($this->dataFile);
    }
}
