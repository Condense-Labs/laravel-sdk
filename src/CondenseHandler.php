<?php

namespace Condense;

use Illuminate\Support\Arr;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;

class CondenseHandler extends AbstractProcessingHandler
{
    public function __construct(protected CondenseClient $client)
    {
        parent::__construct();
    }

    protected function write(array $record): void
    {
        $jsonRecord = json_decode($record['formatted'], JSON_OBJECT_AS_ARRAY);

        if ($jsonRecord['context']['exception'] ?? null) {
            $jsonRecord['extra']['condense']['exception'] = $jsonRecord['context']['exception'];
            $errorSource = $jsonRecord['extra']['condense']['exception']['file'];
            [$filename, $line] = explode(':', $errorSource, 2);
            $line = (int) $line;
            $jsonRecord['extra']['condense']['exception']['filename'] = $filename;
            $jsonRecord['extra']['condense']['exception']['line'] = $line;

            $jsonRecord['extra']['condense']['exception']['source_code'] = $this->getCode($filename, $line);

            $jsonRecord['extra']['condense']['exception']['trace'] = array_map(function ($sourceFile) {
                [$filename, $line] = explode(':', $sourceFile, 2);
                $line = (int) $line;

                return [
                    'file' => $filename,
                    'line' => $line,
                    'code' => $this->getCode($filename, $line),
                ];
            }, $jsonRecord['extra']['condense']['exception']['trace']);
        }

        $this->client->record($jsonRecord);
    }

    public function getFormatter(): FormatterInterface
    {
        return $this->getDefaultFormatter();
    }

    protected function getDefaultFormatter(): FormatterInterface
    {
        $formatter = new JsonFormatter();
        $formatter->includeStacktraces();

        return $formatter;
    }

    protected function getCode(string $filename, int $line, $surroundingLines = 6)
    {
        $line -= 1;
        $lines = [];
        $file = new \SplFileObject($filename);
        $startLine = $line - $surroundingLines < 0 ? 0 : $line - $surroundingLines;
        $maxEndLine = $line + $surroundingLines;
        $file->seek($startLine);

        for ($i = $startLine; $i <= $maxEndLine; $i++) {
            $code = $file->getCurrentLine();
            $lines[] = [
                'content' => $code,
                'line' => $i + 1,
            ];

            if ($file->eof()) {
                break;
            }

            $file->next();
        }

        return $lines;
    }
}
