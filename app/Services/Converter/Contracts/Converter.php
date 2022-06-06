<?php

namespace App\Services\Converter\Contracts;

interface Converter
{
    public function convert($path);
}