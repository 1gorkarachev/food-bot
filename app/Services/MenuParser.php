<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextBreak;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

class MenuParser
{
    protected $menu;
    protected $document;

    protected const DAYS = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница'];

    public function __construct()
    {
        $this->menu = collect();
        $file = storage_path('app/menu.docx');
        $this->document = IOFactory::load($file);
    }

    public function getParseMenu()
    {
        $this->parseDocument($this->document);
        $this->formatMenu();

        return $this->menu;
    }

    protected function formatMenu()
    {
        $menu = [];
        $key = '';

        foreach ($this->menu as $item) {
            if (is_string($item)) {
                $key = $item;
            }
            if (is_array($item)) {
                if (array_key_exists($key, $menu)) {
                    $menu[$key] = array_merge($menu[$key], array_values($item));
                } else {
                    $menu[$key] = array_values($item);
                }
            }
        }

        $this->menu = $menu;
    }

    protected function parseDocument($document)
    {
        $pages = $document->getSections();

        foreach ($pages as $page) {

            $elements = $page->getElements();

            $this->parseElements($elements);
        }
    }

    protected function parseElements($elements)
    {
        foreach ($elements as $element) {

            switch (get_class($element)) {
                case TextRun::class:
                    $this->menu->push($this->parseText($element));
                    break;

                case Table::class:
                    $this->menu->push($this->parseTable($element));
                    break;

                case TextBreak::class:
                    break;
            }
        }
    }

    protected function parseText(TextRun $text)
    {
        $elements = $text->getElements();
        $string = '';

        foreach ($elements as $element) {
            if ($element instanceof Image) continue;
            if (in_array($element->getText(), self::DAYS)) continue;
            $string .= $element->getText();
        }

        return trim($string, ': ');
    }

    protected function parseTable(Table $table)
    {
        $rows = $table->getRows();
        $table = [];

        foreach ($rows as $row) {
            $cells = $row->getCells();
            $row_text = [];

            foreach ($cells as $cell) {
                $elements = $cell->getElements();
                $string = '';

                foreach ($elements as $element) {
                    if ($element instanceof TextBreak) continue;
                    $text = $element->getElements();

                    foreach ($text as $item) {

                        if ($item instanceof Image) continue;
                        $string .= $item->getText();
                    }
                }
                $row_text[] = $string;
            }
            $table[] = $row_text;
        }

        return $table;
    }
}