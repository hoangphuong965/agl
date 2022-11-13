<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use App\Models\Keyword;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Support\Facades\Session;
use App\Repositories\Keyword\KeywordRepositoryInterface;
use App\Rules\Extension;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class CheckUrlController extends Controller
{
    protected $keywordRepo;

    public function __construct(KeywordRepositoryInterface $keywordRepo)
    {
        $this->keywordRepo = $keywordRepo;
    }

    public static function cleanURL($str)
    {
        $url = substr($str, 29);
        $url = str_replace('&sa=', '_', $url);
        $url = strtok($url, '_');
        return $url;
    }


    public function index()
    {
        return view('checkUrl');
    }

    public function store()
    {
        $output = $this->readCSV();
        return $output;
    }

    public function exportCSV($output, $columns)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=output.csv');
        header('Pragma: no-cache');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        foreach ($output as $key => $value) {
            $row['No'] = $value['No'];
            $row['keyword'] = $value['keyword'];
            $row['title'] = $value['title'];
            $row['url']  = $value['url'];

            fputcsv($file, array($row['No'], $row['keyword'], $row['title'], $row['url']));
        }

        fclose($file); 
    }

    public function saveDB($output) 
    {
        forEach ($output as $key => $value) {
            $this->keywordRepo->create([
                "No" => $value['No'],
                "key_word" => $value['keyword'],
                "title" => $value['title'],
                "url" => $value['url'],
            ]);
        }
    }

    public function readCSV()
    {
        $output = [];
        $file = request()->file()['csv_file'];

        if($file->getMimeType() === "text/csv") {
            $keys = [];

            if (($open = fopen($file->getRealPath(), "r")) !== FALSE) {
                while (($data = fgetcsv($open, 1000, ",")) !== FALSE) {
                    $keys[] = $data;
                }
                fclose($open);
            }
            
            forEach($keys as $key => $value) {
                if($key == '0') continue;
    
                if(str_contains($value[1], 'youtube')) {
                    return redirect()->route('import')->with('error', 'Youtube keyword is not allowed in row of csv file');
                }
                
                $data = [
                    'No' => $value[0],
                    'keyword' => $value[1]
                ];
    
                $crawler = $this->crawler($value[1]);
                $output[] = array_merge($data, $crawler);
                sleep(1);
            }

            $columns  = ["No", "keyword", "title", "url"];
            // $this->saveDB($output);
            $this->exportCSV($output, $columns);

        } else {
            return redirect()->route('import')->with('csv', "Please select the file with the extension .csv");
        }

    }

    public function crawler(string $keyWord)
    {
        $client = new Client();
        $URL = "https://www.google.com/search?q={$keyWord}";
        $crawler = $client->request('GET', $URL);
        $currentNode = null;
        $title = '';
        $url = '';

        $nodes = $crawler->filterXPath("//div[contains(@class, 'egMi0') and contains(@class, 'kCrYT')]/a");

        if ($nodes->count()) {
            $nodes->each(function($node) use (&$currentNode) {
                $cleanUrl = self::cleanURL(( $node->link()->getUri() ));

                // check youtube
                $url = str_replace('watch', '_', $cleanUrl);
                $url = strtok($url, '_');
                if (stripos($url, "https://youtube.com/") !== 0) {
                    $currentNode = $node;
                }
            });
            if(!$currentNode) {
                $currentNode = $nodes->first();
            }

            $title = $currentNode->innerText();
            $url = self::cleanURL($currentNode->link()->getUri());
        }

        return [
            'title' => $title,
            'url' => $url,
        ];
    }

}
