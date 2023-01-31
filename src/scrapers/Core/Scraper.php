<?php 
namespace Plexcorp\Mltoolkit\Core;

use Symfony\Component\Console\Output\OutputInterface;
use voku\helper\HtmlDomParser;

/**
 * Scraper - Provides a simple base for all other scrapers.
 */
abstract class Scraper {

    protected OutputInterface $output;
    protected string $feedPath;
    protected string $baseUrl;

    /**
     * contructor
     *
     * @param OutputInterface $output - you can use this to write any output to the console.
     */
    public function __construct(OutputInterface &$output, string $baseUrl) {
        $this->output = $output;
        $this->baseUrl = $baseUrl;
        $this->feedPath = "./feeds/" . time() . ".json";
    }

    abstract protected function render(int $lineNumber, array $item);
    abstract protected function run(string $feedPath) : bool;

    /**
     * Undocumented function
     *
     * @param string $url
     * @param boolean $returnDOMObject
     * @return string | HtmlDomParser
     */
    public function crawl(string $url, bool $returnDOMObject = true) 
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36');
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        $response = curl_exec($ch); 
        curl_close($ch);

        if (empty($response)) {
            throw new \Exception("No response returned. Curl error: " . curl_error($ch));
        }

        if ($returnDOMObject) {
            // Convert the raw HTML to a DOM Object so we can manipulate in php similar to jquery.
            $htmlDomParser = HtmlDomParser::str_get_html($response);
            return $htmlDomParser;
        }

        return $response;
    }
}