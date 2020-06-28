<?php


namespace App\Service;


interface ParserInterface
{
    public function parseItemList(string $url): array;

    public function parseItem(string $url): array;
}