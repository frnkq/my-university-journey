<?php

namespace App\Timeline;

use DateTime;

class TimestampCollector
{
    public const TIMESTAMPS_PATH = __DIR__ . '/../../timestamps/';
    public const TIMESTAMPS_FILE_MATCH_REGEX = '/(20[2,3][0-9])\-([0,1]{1}[0-9])\-([0,1,2]{1}[0-9]{1})\_[a-z\-]+\.md/'; //20yy-mm-dddd_title-of-timestamp.md
    private string $timestampsPath;
    private mixed $timestamps = [];

    public function __construct(string $timestampsPath = self::TIMESTAMPS_PATH)
    {
        $this->timestampsPath = $timestampsPath;
        $this->timestamps = $this->collectTimestamps();
    }

    public function getTimestamps(): mixed
    {
        return $this->timestamps;
    }

    private function collectTimestamps(): mixed
    {
        $timestampFiles = $this->getTimestampFilenames();
        return array_map([$this, 'createTimestampFromFileName'], $timestampFiles);
    }

    private function getTimestampFilenames(): mixed
    {
        return array_filter(
            scandir($this->timestampsPath),
            fn ($filename) => preg_match(self::TIMESTAMPS_FILE_MATCH_REGEX, $filename)
        );
    }

    private function createTimestampFromFileName(string $filename): Timestamp
    {
        $date = new DateTime(explode('_', $filename)[0]);
        $title = substr(explode('_', $filename)[1], 0, -3);
        $content = file_get_contents($this->timestampsPath . $filename);
        return new Timestamp($date, $title, $content);
    }
}
