<?php
namespace App\Models;

interface FromDataFile
{
    /**
     * @param array $record
     * @return mixed
     */
    public static function fromDataFile(array $record);
}