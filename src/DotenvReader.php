<?php

namespace Mifesta\DotenvEditor;

use Mifesta\DotenvEditor\Contracts\DotenvFormatter as DotenvFormatterContract;
use Mifesta\DotenvEditor\Contracts\DotenvReader as DotenvReaderContract;
use Mifesta\DotenvEditor\Exceptions\UnableReadFileException;

/**
 * The DotenvReader class.
 *
 * @package Mifesta\DotenvEditor
 */
class DotenvReader implements DotenvReaderContract
{
    /**
     * The file path
     *
     * @var string
     */
    protected $filePath;

    /**
     * Instance of Mifesta\DotenvEditor\DotenvFormatter
     *
     * @var object
     */
    protected $formatter;

    /**
     * Create a new reader instance
     *
     * @param \Mifesta\DotenvEditor\Contracts\DotenvFormatter $formatter
     */
    public function __construct(DotenvFormatterContract $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Load file
     *
     * @param string $filePath
     * @return DotenvReader
     */
    public function load($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Ensures the given file is readable.
     *
     * @throws \Mifesta\DotenvEditor\Exceptions\UnableReadFileException
     */
    protected function ensureFileIsReadable()
    {
        if (! is_readable($this->filePath) || ! is_file($this->filePath)) {
            throw new UnableReadFileException(sprintf('Unable to read the file at %s.', $this->filePath));
        }
    }

    /**
     * Get content of file
     *
     * @return string
	 * @throws \Mifesta\DotenvEditor\Exceptions\UnableReadFileException
	 */
    public function content()
    {
        $this->ensureFileIsReadable();

        return file_get_contents($this->filePath);
    }

    /**
     * Get information of all lines from file content
     *
     * @return array
	 * @throws \Mifesta\DotenvEditor\Exceptions\UnableReadFileException
	 */
    public function lines()
    {
        $content = [];
        $lines = $this->readLinesFromFile();

        foreach ($lines as $row => $line) {
            $data = [
                'line' => $row + 1,
                'raw_data' => $line,
                'parsed_data' => $this->formatter->parseLine($line),
            ];

            $content[] = $data;
        }

        return $content;
    }

    /**
     * Get information of all keys from file content
     *
     * @return array
	 * @throws \Mifesta\DotenvEditor\Exceptions\UnableReadFileException
	 */
    public function keys()
    {
        $content = [];
        $lines = $this->readLinesFromFile();

        foreach ($lines as $row => $line) {
            $data = $this->formatter->parseLine($line);
            if ($data['type'] == 'setter') {
                $content[$data['key']] = [
                    'line' => $row + 1,
                    'export' => $data['export'],
                    'value' => $data['value'],
                    'comment' => $data['comment'],
                ];
            }
        }

        return $content;
    }

    /**
     * Read content into an array of lines with auto-detected line endings
     *
     * @return array
	 * @throws \Mifesta\DotenvEditor\Exceptions\UnableReadFileException
	 */
    protected function readLinesFromFile()
    {
        $this->ensureFileIsReadable();

        $autodetect = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', '1');
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES);
        ini_set('auto_detect_line_endings', $autodetect);

        return $lines;
    }
}
