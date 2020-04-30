<?php
$fileBaseName = 'loadAll';
$fileName = __DIR__.'/'.$fileBaseName.'.sql';
$f = fopen($fileName,'r');
$str = fread($f,filesize($fileName));
fclose($f);

$strTrimmed = substr($str,0x85ac5);
$fileReplacedName = __DIR__.'/'.$fileBaseName.'Trimmed.sql';
$fileReplaced = fopen($fileReplacedName,'w');
fwrite($fileReplaced,$strTrimmed);
fclose($fileReplaced);
