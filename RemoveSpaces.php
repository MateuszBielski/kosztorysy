<?php
$fileBaseName = 'loadAll1';
$fileName = __DIR__.'/'.$fileBaseName.'.sql';
$f = fopen($fileName,'r');
$str = fread($f,filesize($fileName));
fclose($f);

$strReplaced = preg_replace('/[ ]{2,}/',' ',$str);
$fileReplacedName = __DIR__.'/'.$fileBaseName.'Repl.sql';
$fileReplaced = fopen($fileReplacedName,'w');
fwrite($fileReplaced,$strReplaced);
fclose($fileReplaced);