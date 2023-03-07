<?php

namespace App\Observers;

use App\Http\Controllers\ScrapingHelper;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;


class CustomCrawlerObserver extends CrawlObserver
{

    private array $content;
    private ?string $url = "";
    private ?string $searchTerm;
    private ?string $searchString = "";
    private ?string $responseReasonPhrase = "";
    private ?string $responseStatusCode = "";
    private ?string $message = "";
    private bool $foundSearchTerm = FALSE;
    private bool $foundImpressum = FALSE;

    public function __construct($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }


    /**
     * Called when the crawler will crawl the url.
     *
     * @param UriInterface $url
     */
    public function willCrawl(UriInterface $url): void
    {

        $this->url = $url->getScheme() . '://' . $url->getHost();

    }

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param UriInterface $url
     * @param ResponseInterface $response
     * @param UriInterface|null $foundOnUrl
     */
    #[NoReturn] public function crawled(
        UriInterface      $url,
        ResponseInterface $response,
        ?UriInterface     $foundOnUrl = null
    ): void
    {

        $this->responseReasonPhrase = $response->getReasonPhrase();

        if ($response->getStatusCode() === 200) {


            if ($this->pageIsImpressum($url->getPath())) {
                $this->foundImpressum = TRUE;
                $this->message = "impressum found";
            }


            $websiteText = ScrapingHelper::receiveWrappedWebsiteContent($response->getBody(), $url);

            $this->content[$url->getHost()][$url->getPath()] = $websiteText;


            if ($this->searchString = ScrapingHelper::searchForSearchTerm($websiteText, $this->searchTerm)) {
                $this->foundSearchTerm = TRUE;
            }

        }

        $this->responseStatusCode = $this->foundImpressum ? 200 : $response->getStatusCode();

    }


    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param UriInterface $url
     * @param RequestException $requestException
     * @param UriInterface|null $foundOnUrl
     */
    public function crawlFailed(
        UriInterface     $url,
        RequestException $requestException,
        ?UriInterface    $foundOnUrl = null
    ): void
    {

        Log::error('crawlFailed', ['url' => $url, 'error' => $requestException->getMessage()]);

        $this->responseStatusCode = $requestException->getCode();

    }

    /**
     * Called when the crawl has ended.
     * @throws Exception
     */
    public function finishedCrawling(): void
    {
        ScrapingHelper::storeResultInSession(
            $this->url,
            $this->foundSearchTerm,
            $this->searchString,
            $this->message,
            $this->responseStatusCode
        );
    }


    private function isLike($str, $searchTerm): bool
    {
        $searchTerm = strtolower($searchTerm);
        $str = strtolower($str);
        $pos = strpos($str, $searchTerm);

        return $pos !== false;
    }


    private function pageIsImpressum(string $path): bool
    {

        return $this->isLike($path, "pages/impressum")
            || $this->isLike($path, "imprint")
            || $this->isLike($path, "impressum")
            || $this->isLike($path, "pages/imprint");
    }


}

