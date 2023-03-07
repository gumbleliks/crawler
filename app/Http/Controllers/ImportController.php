<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportController extends Controller
{

    private array $urls = [];

    public function import(Request $request)
    {


        $request->validate([
            'file' => 'required|file|mimes:.csv,txt',
            'free_search' => 'required_without:fixed_search',
            'fixed_search' => 'required_without:free_search',
        ]);

        $file = new File();

        $original_file_name = $request->file->getClientOriginalName();
        $file->name = $request->file('file')->getClientOriginalName();
        $file->file_path = $request->file('file')->storeAs('uploads', $file->name, 'public');

        $pathToFoundCsv = storage_path('app/public/uploads/' . $original_file_name);

        SimpleExcelReader::create($pathToFoundCsv)
            ->noHeaderRow()
            ->getRows()
            ->each(function (array $rowProperties) {

                $this->urls[] = $rowProperties[0];

            });

        $filteredUrls = $this->urls;
        $crawlOnlyImpressum = ($request->which_page === "impressum") ? 1 : 0;
        $searchTerm = ($request->input("free_search")) ?: $request->input('fixed_search');


        /** Call Spatie WebCrawler oder Simpe Laravel HTTP Client for Impressum Search  */
        /** returns scrapedWebsites Results stored in Session */
        $this->scrapingSearch($filteredUrls, $searchTerm, $crawlOnlyImpressum);


        $pathToFoundCsv = storage_path('app/public/uploads/found_url.csv');
        $outFound = fopen($pathToFoundCsv, 'wb');

        $pathToNotFoundCsv = storage_path('app/public/uploads/not_found_url.csv');
        $outNotFound = fopen($pathToNotFoundCsv, 'wb');

        foreach (Session::all() as $key => $subArray) {

            if ($subArray['found']) {
                fputcsv($outFound, [$subArray['url']]);
            } else {
                fputcsv($outNotFound, [$subArray['url']]);
            }


        }

        fclose($outFound);
        fclose($outNotFound);


        return view('result', Session::all());

    }


    private function scrapingSearch(array $urls, string $searchTerm, $crawlOnlyImpressum): void
    {

        session()->flush();

        foreach ($urls as $url) {

            $crawler = new CrawlerController();
            $crawler->fetchContent($url, $searchTerm, $crawlOnlyImpressum);

        }


    }


}


