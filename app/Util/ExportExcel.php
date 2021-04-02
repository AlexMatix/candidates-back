<?php


use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class ExportExcel
{
    private $path;
    public function __construct($path)
    {
        $this->path = $path;
    }


    public function createExcel($data, $header)
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($this->path);

        $border = (new BorderBuilder())
            ->setBorderBottom(Border::WIDTH_THIN)
            ->setBorderLeft(Border::WIDTH_THIN)
            ->setBorderRight(Border::WIDTH_THIN)
            ->setBorderTop(Border::WIDTH_THIN)
            ->build();

        $header_style = (new StyleBuilder())
            ->setBorder($border)
            ->setCellAlignment(CellAlignment::CENTER)
            ->setBackgroundColor(Color::rgb(105, 28, 50))
            ->setFontColor(Color::WHITE)
            ->build();

        $data_style = (new StyleBuilder())
            ->setCellAlignment(CellAlignment::CENTER)
            ->build();


        $row = WriterEntityFactory::createRowFromArray($header, $header_style);
        $writer->addRow($row);

        foreach ($data as $value) {
            $row = WriterEntityFactory::createRowFromArray($value, $data_style);
            $writer->addRow($row);
        }

        $writer->close();
    }
}
