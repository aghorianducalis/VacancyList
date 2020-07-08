<?php


namespace App\Service;


interface ParserInterface
{
    public function parseVacancyList(string $url): array;

    public function parseVacancy(string $url): array;

    public function getVacancyListUrl(): string;
}