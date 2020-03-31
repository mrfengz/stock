<?php
function download($url, $filename)
{
    if (!$url) return false;
echo file_get_contents($url);die;
    return file_put_contents($filename, file_get_contents($url));
}