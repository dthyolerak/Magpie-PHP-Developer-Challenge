<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeHelper
{     private array $hello = [];
    public static function fetchDocument(string $url): Crawler
    {
        $client = new Client();
        $response = $client->get($url);
        return new Crawler($response->getBody()->getContents(), $url);
    }

}
