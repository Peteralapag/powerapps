<?php
$directory = $_SERVER['DOCUMENT_ROOT'].'/tmp';
$files = scandir($directory);
// echo $files ;
foreach ($files as $file) {
    $filepath = $directory . '/' . $file;
    if (is_file($filepath) && filectime($filepath) < time() - 1800) {
        unlink($filepath);
    }
}
?>