<?php

namespace App\Http\Crawlprofile;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfiles\CrawlProfile;

class CrawlImpressumUrls extends CrawlProfile
{
    protected mixed $baseUrl;

    public function __construct($baseUrl)
    {
        if (!$baseUrl instanceof UriInterface) {
            $baseUrl = new Uri($baseUrl);
        }

        $this->baseUrl = $baseUrl;
    }

    public function shouldCrawl(UriInterface $url): bool
    {

        return $this->baseUrl->getHost() === $url->getHost()
            && $this->pageIsImpressum($url->getPath());

    }

    private function pageIsImpressum(string $path): bool
    {

        return $this->isLike($path, "pages/impressum")
            || $this->isLike($path, "imprint")
            || $this->isLike($path, "impressum")
            || $this->isLike($path, "pages/imprint")
            || $path === "/";
    }

    private function isLike($str, $searchTerm): bool
    {
        $searchTerm = strtolower($searchTerm);
        $str = strtolower($str);
        $pos = strpos($str, $searchTerm);

        return $pos !== false;
    }
}
