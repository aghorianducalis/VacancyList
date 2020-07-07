<?php

namespace App\Message;

final class ParseVacancyMessage
{
     private $url;

     public function __construct(string $url)
     {
         $this->url = $url;
     }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
