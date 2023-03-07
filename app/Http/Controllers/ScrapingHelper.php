<?php

namespace App\Http\Controllers;

use App\Enums\SearchTermEnum;
use DOMDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use voku\helper\HtmlDomParser;

class ScrapingHelper
{

    public static function receiveWrappedWebsiteContent($responseBody, $url): string
    {

        $dom = "";
        $doc = new DOMDocument();
        @$doc->loadHTML('<?xml encoding="UTF-8">' . $responseBody);
//        @$doc->loadHTML($responseBody);

        // receive DOM/HTML and extract only content inner body without tags, css, etc..
        try {
            $dom = HtmlDomParser::str_get_html($doc->saveHTML());
        } catch (Exception $e) {
            Log::error('crawlFailed', ['url' => $url, 'HtmlDomParser error' => $e->getMessage()]);
        }

        $websiteText = $dom->findOne('body')->text;

        //# convert encoding
        $content1 = mb_convert_encoding($websiteText, 'UTF-8', mb_detect_encoding($websiteText, 'UTF-8, ISO-8859-1', true));
        //# strip all javascript
        $content2 = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content1);
        //# strip all style
        $content3 = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content2);
        //# strip tags
        $content4 = str_replace('<', ' <', $content3);
        $content5 = strip_tags($content4);
        $content6 = str_replace('  ', ' ', $content5);
        //# strip white spaces and line breaks
        $content7 = preg_replace('/\s+/S', " ", $content6);
        //# html entity decode - ö was shown as &ouml;

        return html_entity_decode($content7);

    }


    public static function searchForSearchTerm(string $websiteText, $searchTerm): string
    {

        $regEx = match ($searchTerm) {
            SearchTermEnum::UST_ID_NR->value => '/(ATU[0-9]{8}|BE0[0-9]{9}|BG[0-9]{9,10}|CY[0-9]{8}L|CZ[0-9]{8,10}|DE?\s?[0-9]{9}|DK[0-9]{8}|EE[0-9]{9}|(EL|GR)[0-9]{9}|ES[0-9A-Z][0-9]{7}[0-9A-Z]|FI[0-9]{8}|FR[0-9A-Z]{2}[0-9]{9}|GB([0-9]{9}([0-9]{3})?|[A-Z]{2}[0-9]{3})|HU[0-9]{8}|IE[0-9]S[0-9]{5}L|IT[0-9]{11}|LT([0-9]{9}|[0-9]{12})|LU[0-9]{8}|LV[0-9]{11}|MT[0-9]{8}|NL[0-9]{9}B[0-9]{2}|PL[0-9]{10}|PT[0-9]{9}|RO[0-9]{2,10}|SE[0-9]{12}|SI[0-9]{8}|SK[0-9]{10})/i',
            SearchTermEnum::HOTLINE->value => '/0800/i',
            SearchTermEnum::KLEINUNTERNEHMEN->value => '/§19/i',
            SearchTermEnum::RECHTSFORM->value => '"/e\.V|GmbH|Ltd/"i',
            SearchTermEnum::STEUER_NR->value => '/\b(?:\d{2,3}(?:\s?\/\s?|\s)\d{3}(?:\s?\/\s?|\s)\d{5}|\d{3}(?:\s?\/\s?|\s)\d{4}(?:\s?\/\s?|\s)\d{4}|\d{5}(?:\s?\/\s?|\s)\d{5}|\d{10,11})\b/m',
            SearchTermEnum::HANDELS_REG->value => '/HRB[,:]?(?:[- ](?:Nr|Nummer)[.:]*)?\s?(\d+(?: \d+)*)(?: B)?/',
            default => '/' . $searchTerm . '/i',
        };


        if (preg_match($regEx, $websiteText, $matches)) {
            return $matches[0];
        }

        return "";
    }


    /**
     * @param string $url
     * @param bool $foundSearchTerm
     * @param mixed $searchTerm
     * @param string $message
     * @param mixed $statusCode
     * @return void
     * @throws Exception
     */
    public static function storeResultInSession(string $url, bool $foundSearchTerm, mixed $searchTerm, string $message, mixed $statusCode): void
    {
        Session::put(
            md5($url),
            [
                'url' => $url,
                'found' => $foundSearchTerm,
                'filter' => $searchTerm,
                "message" => $message,
                'http_code' => $statusCode
            ]);
    }


}
