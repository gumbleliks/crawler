<?php

namespace App\Http\Controllers;

use App\Observers\CustomCrawlerObserver;
use Exception;
use GuzzleHttp\RequestOptions;
use Http;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class CrawlerController extends Controller
{

    private ?string $responseStatusCode = "";
    private ?string $message = "";
    private bool $foundSearchTerm = FALSE;


    public function __construct()
    {
    }

    /**
     * Crawl the website content.
     * @return true
     * @throws Exception
     */
    public function fetchContent($url, $searchTerm, $crawlOnlyImpressum): ?bool
    {

        /** Crawl only Imprint Pages, or crawl the whole Website  */
        return ($crawlOnlyImpressum)
            ? $this->impressumCrawler($url, $searchTerm)
            : $this->allSitesCrawler($url, $searchTerm);


    }

    /**
     * @throws Exception
     */
    private function impressumCrawler($url, $searchTerm)
    {

        $url = rtrim($url, "/");

        $impressumUrls = [
            $url . "/impressum",
            $url . "/pages/impressum",
            $url . "/imprint",
            $url . "/pages/imprint"
        ];


        foreach ($impressumUrls as $impressumUrl) {


            try {
                $response = Http::connectTimeout(1)
                    ->withoutVerifying()
                    ->get($impressumUrl);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $this->message = "Server Error";
                $this->responseStatusCode = "500";
                break;
            }


            if ($response->successful()) {

                $this->message = "impressum found";
                $this->responseStatusCode = $response->status();

                $content = ScrapingHelper::receiveWrappedWebsiteContent($response->body(), $url);

                if ($searchTerm = ScrapingHelper::searchForSearchTerm($content, $searchTerm)) {
                    $this->foundSearchTerm = TRUE;
                }

                break;

            }
            $searchTerm = "";
            $this->responseStatusCode = (!$this->responseStatusCode) ? $response->status() : $this->responseStatusCode;

        }


        ScrapingHelper::storeResultInSession(
            $url,
            $this->foundSearchTerm,
            $searchTerm,
            $this->message,
            $this->responseStatusCode
        );

    }

    /**
     * @param $url
     * @param $searchTerm
     * @return true
     */
    private function allSitesCrawler($url, $searchTerm): bool
    {
        ini_set('max_execution_time', 0); // 0 = Unlimited


        Crawler::create([RequestOptions::ALLOW_REDIRECTS => true, RequestOptions::TIMEOUT => 600])
            ->acceptNofollowLinks()
//            ->setBrowsershot($browserShot)
//            ->executeJavaScript()
//            ->setCrawlQueue($queue)
            ->ignoreRobots()
            ->setMaximumDepth(7)
            ->setParseableMimeTypes(['text/html', 'text/plain'])
            ->setCrawlObserver(new CustomCrawlerObserver($searchTerm))
//            ->setCrawlProfile($profile)
            ->setCrawlProfile(new CrawlInternalUrls($url))
            ->setMaximumResponseSize(1024 * 1024 * 2) // 2 MB maximum
            ->setTotalCrawlLimit(70) // limit defines the maximal count of URLs to crawl
//            ->setConcurrency(5) // all urls will be crawled one by one --> 10 by default
            ->setDelayBetweenRequests(10)
            ->startCrawling($url);

        return true;
    }

}
