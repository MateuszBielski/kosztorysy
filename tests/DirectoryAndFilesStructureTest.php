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
        Functions::CopyFileStructure($sourcePathDirectory,$destPathDirectory,
        'App\Service\Functions::ReplaceCharsAccordingUtf8');
        $copiedFile = 'tests/CopyDirLev/SourceDirLev2_2/0-19R9.OP';
        $readText = fread(fopen($copiedFile,'rb'),filesize($copiedFile));
        Functions::RemoveDirRecursive($destPathDirectory);
        $this->assertEquals('Demontaż i montaż wahadłowe ścianki',$readText);
    }
    public function testCopyOneFileWithCharReplacement()
    {
        $sourceFilePath = 'resources/Norma3/Kat/2-02/2-02R1.OPR';
        $destDirPath = 'resources/Norma3/Kat/2-02/utf8/';
        $expectedFile = $destDirPath.'2-02R1.OPR';
        Functions::CopyOneFileWithTranslate($sourceFilePath,$destDirPath,'App\Service\Functions::ReplaceCharsAccordingUtf8');
        $this->assertTrue(file_exists($expectedFile));
        //wymaga usucięcia, lecz test był tworzony na potrzebę konwersji jednego pliku
        //Functions::RemoveDirRecursive($destDirPath);
    }
    
}
