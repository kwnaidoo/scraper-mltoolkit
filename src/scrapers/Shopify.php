<?php
namespace Plexcorp\Mltoolkit;
use Plexcorp\Mltoolkit\Core\Scraper;

/**
 * Below is an example of a Shopify scraper config - please note all your shopify scrapers should use "Shopify" as the class.
 */
//    {
//        "name": "myawesomeshopifyexamplestore.com",
//        "class": "Shopify",
//        "feed_path": "./feeds/myawesomeshopifyexamplestore.json",
//        "base_url": "https://www.myawesomeshopifyexamplestore.co/",
//        "description": "A sample scrapper that scrapes shopify products",
//        "classifier": {
//            "feed_path": "../feeds/myawesomeshopifyexamplestore.json",
//            "model_path": "../models/myawesomeshopifyexamplestore.bin",
//            "dataset_path": "../models/datasets/myawesomeshopifyexamplestore.txt",
//            "field": "category",
//            "text": "title",
//            "loss_function": "ova",
//            "epochs": 25,
//            "ngrams": 2,
//            "threads": 30
//         }
//     }

class Shopify extends Scraper
{
    /**
     * run - fetch data from shopify store and write to json file.
     *
     * @param string $feedPath
     *
     * @return boolean
     */
    public function run(string $feedPath) : bool
    {
        $this->feedPath = $feedPath;
        $this->startOutput();
        $page = 1;
        $havePages = true;
        $lineNumber = 0;
        while($havePages) {
            $processed = 0;

            $url = $this->baseUrl . "products.json?page=" . $page;
            $this->output->write("Crawling URL: " . $url . "\n");
            $data = json_decode($this->crawl($url, false), true);

            if(empty($data)) {
                $havePages = false;
                continue;
            }
            $products = $data['products'] ?? 0;
            foreach($products as $i => $product) {
                $lineNumber += $i;
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
     * map - take in an array from the run method and extract only fields we need.
     *
     * @param array $product
     * @return array
     */
    public function map($product) : array
    {
        return [
            "url" => $product['handle'],
            "title" => $product['title'],
            "description" => $product['body_html'],
            "image" => $product['images'][0]['src'],
            "price" => $product['variants'][0]['price'],
            "category" => $product['product_type']
        ];
    }

    /**
     * startOutput - Start writing to our feed file.
     *
     * @return void
     */
    public function startOutput() 
    {
        file_put_contents($this->feedPath, "[\n");
    }

    /**
     * endOutput - Close the feed file.
     *
     * @return void
     */
    public function endOutput() 
    {
        file_put_contents($this->feedPath, "\n]", FILE_APPEND);
    }

    /**
     * render - write each item to the feed file.
     *
     * @param integer $lineNumber
     * @param array $item
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
