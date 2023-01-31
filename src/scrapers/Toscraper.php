<?php namespace Plexcorp\Mltoolkit;
use Plexcorp\Mltoolkit\Core\Scraper;

// This is an example of a HTML scraper to get you started. Below is the config you should use in config.json
//       {
//         "name": "toscraper",
//         "class": "Toscraper",
//         "feed_path": "./feeds/toscraper.json",
//         "base_url": ""https://books.toscrape.com/",
//         "description": "A sample scrapper that scrapes books.toscrape.com",
//         "classifier": {
//             "feed_path": "../feeds/toscraper.json",
//             "model_path": "../models/toscraper.bin",
//             "dataset_path": "../models/datasets/toscraper.txt",
//             "field": "category",
//             "text": "title",
//             "loss_function": "ova",
//             "epochs": 25,
//             "ngrams": 3,
//             "threads": 30
//         }
//    }
/**
 * The above is an example configuration for a single scraper, the settings are as follows:
 * - name: The name you will use when running the command, e.g. php console.php my_scraper   --- no spaces or special characters including spaces.
 * - class: This must be the class name as it is in the scrapers/ folder. This must be PSR4 compatible: https://www.php-fig.org/psr/psr-4/
 * - feed_path : Where should the scraper write the scraped data to. Full path to the .json output file.
 * - base_url : Path to website base URL to scrape.
 * - classifier: This is the configuration set used by the Python module to generate and use a machine learning model. If you don't need
 *   the machine learning functionality - this is not needed and can be ommited.
 * - classifier - feed_path : The path to the output .json file from the scraped data. Similar to "feed_path" above, except note the ../
 *    since the python code is inside plexcorp_mltoolkit.
 * - classifier - model_path : The path to save the trained model.
 * - classifier - dataset_path : The python code needs to do some pre-processing on the json file to generate a text file that 
 *    the model can understand. This is the path to save that metadata file.
 * - classifier - field : When classifying text - we group them into categories or labels, this is the field in your scraped data 
 *   that the model will use as the label.
 * - classifier - text : Similar to above, this is the field in your scraped json data we associate with the label.
 * - classifier - loss_function,epochs, ngrams, threads : only needs adjusting to improve accuracy or performance of the classifier:
 *    full documentation and examples on these settings can be found here: https://fasttext.cc/docs/en/supervised-tutorial.html
 * 
 */
class Toscraper extends Scraper
{
    public function run(string $feedPath) : bool 
    {
        $this->feedPath = $feedPath;
        $this->startOutput();

        $page = 1;
        $havePages = true;
        $lineNumber = 0;

        while($havePages) {

            // Makes a curl request and returns an HtmlDomParser object which you can query similar to jquery.
            // Learn more about how to query using the DOM object here: https://github.com/voku/simple_html_dom
            // NOTE: Exceptions can be thrown - we do not catch it here because we want to stop writing to the
            // json file if something goes wrong.
            $url = str_replace("#p#", $page, $this->baseUrl . "catalogue/page-#p#.html");
            $this->output->write("Crawling URL: " . $url . "\n");
            $data = $this->crawl($url);
    
            if(empty($data)) {
                $havePages = false;
                continue;
            }

            // Find all divs containing a product card
            $products = $data->find(".product_pod");
            $processed = 0;

            foreach($products as $i => $product) {
                $lineNumber += $i;

                // Map takes in an HtmlDomParser object and converts it to an array.
                $item = $this->map($product);
                if (!empty($item['url'])) {
                    $this->render($lineNumber, $item);
                    $processed++;
                }
            }

            if ($processed == 0) {
                $havePages = false;
            }

            $page++;
        }

        $this->endOutput();

        return true;
    }

    /**
     * Map - Convert an HtmlDomParser object to an array by mapping all needed fields.
     * @param voku\helper\HtmlDomParser $element
     * 
     * @return array
     */
    public function map($element) : array
    {
        $url = $element->findOne("a")->getAttribute("href");
        $image = $element->findOne("img")->getAttribute("src");
        $title = $element->findOne("h3 a")->text;
        $price = $element->findOne(".price_color")->text;
        return [
            "url" => $this->baseUrl . $url,
            "image" => $image,
            "price" => $price,
            "title" => $title,
            "category" => "Books"
        ];
    }

    /**
     * startOutput - writes opening line to json feed field.
     *
     * @return void
     */
    public function startOutput() 
    {
        file_put_contents($this->feedPath, "[\n");

    }

    /**
     * endOutput - closes out json file to finish the scrapping process.
     *
     * @return void
     */
    public function endOutput() 
    {
        file_put_contents($this->feedPath, "\n]", FILE_APPEND);

    }

    /**
     * render - writes an individual item to our json file.
     *
     * @param integer $lineNumber
     * @param array $item
     *
     * @return void
     */
    public function render(int $lineNumber, array $item)
    {
        $prefix = "";
        if ($lineNumber > 0) {
            $prefix = ",";
        }

        file_put_contents($this->feedPath, $prefix . json_encode($item) . "\n", FILE_APPEND);
    }
}