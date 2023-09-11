<?php

use App\Timeline\Timestamp;
use App\Timeline\TimestampCollector;
use PHPUnit\Framework\TestCase;


class TimestampCollectorTest extends TestCase
{
    private ?TimestampCollector $collector;
    static $mockTimestampsPath =  __DIR__ . '/../../mocks/timestamps/';

    public static function setUpBeforeClass(): void
    {
        self::createTimestampMockFiles();
    }

    public static function tearDownAfterClass(): void
    {
        self::deleteMockedTimestampFiles();
    }

    private static function createTimestampMockFiles(): void
    {
        require_once self::$mockTimestampsPath . 'MockedTimestamps.php';
        array_walk(
            $mockedTimestamps,
            fn ($file) => file_put_contents(self::$mockTimestampsPath . $file['name'], $file['contents'])
        );
    }

    private static function deleteMockedTimestampFiles(): void
    {
        $mockedTimestamps = scandir(self::$mockTimestampsPath);
        array_walk(
            $mockedTimestamps,
            fn ($file) => !in_array($file, ['.', '..', 'MockedTimestamps.php']) ? unlink(self::$mockTimestampsPath . $file) : []
        );
    }

    protected function setUp(): void
    {
        $this->collector = new TimestampCollector(self::$mockTimestampsPath);
    }

    protected function tearDown(): void
    {
        $this->collector = null;
    }

    public function testCollectedTimestampsMatchesFilesEntries(): void
    {
        $timestampsInDir = sizeof(array_filter(
            scandir(self::$mockTimestampsPath),
            fn ($filename) => preg_match(TimestampCollector::TIMESTAMPS_FILE_MATCH_REGEX, $filename)
        ));
        $this->assertIsArray($this->collector->getTimestamps());
        $this->assertEquals(sizeof($this->collector->getTimestamps()), $timestampsInDir);
    }

    public function testTimestampFilenameDateIsNotValidTimestampIsNotIncludedInTimeline(): void
    {
        $this->assertIsArray($this->collector->getTimestamps());
        $this->assertNotContains('9764-01-22_this-should-not-be-included.md', $this->collector->getTimestamps());
    }

    public function testCollectedTimestampsAreTimestampsObjects(): void
    {
        array_map(
            fn ($timeline) => $this->assertInstanceOf(Timestamp::class, $timeline),
            $this->collector->getTimestamps()
        );
    }

    public function testFilenameIsProperlySplittedInDateAndTitle(): void
    {
        $areDateAndTitleProperlySplit = function (Timestamp $timestamp) {
            $timestampFilename = $timestamp->getDate()->format('Y-m-d') . '_' . $timestamp->getTitle() . '.md';
            print_r($timestamp->getTitle());
            $this->assertTrue(in_array($timestampFilename, scandir(self::$mockTimestampsPath)));
        };
        array_walk($this->collector->getTimestamps(), $areDateAndTitleProperlySplit);
    }
}
