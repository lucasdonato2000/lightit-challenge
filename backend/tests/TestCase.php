<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $initialFiles = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $documentsPath = __DIR__ . '/../storage/app/documents';
        if (is_dir($documentsPath)) {
            self::$initialFiles = array_map('basename', glob($documentsPath . '/*.encrypted'));
        }
    }

    protected function tearDown(): void
    {
        $this->cleanupTestFiles();
        parent::tearDown();
    }

    protected function cleanupTestFiles(): void
    {
        $documentsPath = __DIR__ . '/../storage/app/documents';

        if (is_dir($documentsPath)) {
            $currentFiles = glob($documentsPath . '/*.encrypted');

            foreach ($currentFiles as $file) {
                $basename = basename($file);
                if (!in_array($basename, self::$initialFiles)) {
                    @unlink($file);
                }
            }
        }
    }
}
