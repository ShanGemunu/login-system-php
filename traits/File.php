<?php

namespace app\traits;

trait File
{
    public function getFirstLine(string $filePath)
    {
        $file = fopen($filePath, 'r');
        $firstLine = fgets($file);
        fclose($file);

        return $firstLine;
    }

    public function fileMove(string $from, string $to): bool
    {

        return move_uploaded_file($from, $to);
    }

    public function fileCopy(string $from, string $to)
    {

        return copy($from, $to);
    }
}