<?php

namespace App\Tests;

use App\Service\Functions;
use PHPUnit\Framework\TestCase;

class DirectoryAndFilesStructureTest extends TestCase
{
    public function testCreatigCopyOfFilesInFolder()
    {
        $sourcePathDirectory = 'tests/SourceDirLev1';
        $this->assertTrue(file_exists($sourcePathDirectory) && is_dir($sourcePathDirectory));
        $destPathDirectory = 'tests/CopyDirLev';
        Functions::CopyFileStructure($sourcePathDirectory,$destPathDirectory);
        $this->assertTrue(file_exists($destPathDirectory) && is_dir($destPathDirectory));
        $destPathFile1 = 'tests/CopyDirLev/SourceDirLev2_2/SourceFileLev3_2.bcd';
        $this->assertTrue(file_exists($destPathFile1) && is_file($destPathFile1));
        Functions::RemoveDirRecursive($destPathDirectory);
        $this->assertTrue(!file_exists($destPathDirectory));
    }
    //kopiowanie ze zmianą liter
    public function testCopyFilesRecursiveWithCharReplacement()
    {
        $sourcePathDirectory = 'tests/SourceDirLev1';
        $destPathDirectory = 'tests/CopyDirLev';
        // Functions::CopyFileStructure($sourcePathDirectory,$destPathDirectory,'src/Service/Functions::ReplaceCharsAccordingUtf8');//Functions::
        Functions::CopyFileStructure($sourcePathDirectory,$destPathDirectory,'Functions::ReplaceCharsAccordingUtf8');
        $copiedFile = 'tests/CopyDirLev/SourceDirLev2_2/0-19R9.OP';
        $readText = fread(fopen($copiedFile,'rb'),filesize($copiedFile));
        $this->assertEquals('Demontaż i montaż wahadłowe ścianki\r\n',$readText);
        Functions::RemoveDirRecursive($destPathDirectory);
    }
}
