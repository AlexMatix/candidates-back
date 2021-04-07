<?php

namespace App\Util;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class ImportExcel
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function readExcel($header_position): array
    {
        $reader = ReaderEntityFactory::createReaderFromFile($this->path);
        $reader->setShouldPreserveEmptyRows(true);
        $reader->open($this->path);

        $data = [];
        $header = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $position => $row) {
                if ($position < $header_position) {
                    continue;
                } elseif ($position == $header_position) {
                    $header = $row->toArray();
                } else {
                    if (!empty($row->toArray())) {
                        $data[] = array_combine($header, $row->toArray());
                    } else {
                        $data[] = array_fill_keys($header, '');
                    }
                }
            }
        }

        return $data;
    }
}
