[Machine learning scraper toolkit](.)

*   [Search](#)

*   [Introduction!](#introduction)
    *   [Project layout](#project-layout)
        
    *   [Configuring scrapers](#configuring-scrapers)
        
    *   [Setting up docker](#setting-up-docker)
        

Introduction!
=============

Thank you for taking the time to checkout this project.

To get started you first will need to have docker running on your machine, you can get install steps here : https://docs.docker.com/engine/install/

It is possible to run this project without docker on linux and Mac OS by simply installing python 3 and PHP 7+. You will also need CURL installed on your system and as well as the php curl extension. Below is a list of suggested php extensions to install if you are running this project without docker:

*   bcmath
*   curl
*   zip
*   mbstring
*   dom

For python, you will need python 3+ and fasttext: `pip install fasttext`

Project layout
--------------

    documentation/   # Documentation for this project.
    src/
        mltoolkit/   # Here you will find all the Python code needed to generate Fasttext models. 
            classifier.py # All the functions needed to generate models and predictions.
            test.py # An example of how to use the train, load model and predict functions.
        scrapers/  # Implementations of PHP scrapers.
            Shopify.php # A scraper that will allow you to scrape most shopify stores.
            Toscraper.php # An example scraper of how to handle HTML DOM elements.
            Core/ # Provides some basic functionality such as crawling http links.
                Scraper.php # A base class for all scrapers.
        console.php # A CLI tool to run scrapers similar to laravel's "artisan" tool.
        models/  # Will contain .bin and .txt files generated for fasttext models.
        composer.phar # Composer to install php packages.
    

Configuring scrapers
--------------------

In the project root you will find a `config.json` file, which contains a list of scraper configuration nodes. Each scraper configuration should contain a definition similar to the following:

        {
            "name": "myawesomeshopifyexamplestore.com", // this is the name used when running the scraper
            "class": "Shopify", // PHP class name as found in the scrapers folder.
            "feed_path": "./feeds/myawesomeshopifyexamplestore.json", // Where to store the scraped data.
            "base_url": "https:www.myawesomeshopifyexamplestore.co/", // The URL to scrape.
            "description": "A sample scrapper that scrapes shopify products",
            "classifier": {
                "feed_path": "../feeds/myawesomeshopifyexamplestore.json", // Same as feed_path above
                "model_path": "../models/myawesomeshopifyexamplestore.bin", // Where to save the compiled model.
                // Where to store the dataset formatted from the scraped data.
                "dataset_path": "../models/datasets/myawesomeshopifyexamplestore.txt",
                "field": "category", // The field from the scraped data to use as a category label.
                "text": "title", // The field to use as text training data.
                // Fasttext configuration's - learn more here:
                // https://fasttext.cc/docs/en/options.html
                "loss_function": "ova",
                "epochs": 25,
                "ngrams": 2,
                "threads": 30
             }
         }
    

Setting up docker
-----------------

To get a container up and running with Python and PHP dependencies - there is a "Dockerfile" in the project root directory - you can build and run the docker container as follows:

Setting up the container:

    docker build . -t mltoolkit
    docker run --name mltoolkit -dit --rm -v ${PWD}:/app mltoolkit /bin/bash
    docker exec -it mltoolkit composer install
    

Note: Once you've built the container, you do not need to re-run all the above each time, simply run this whenever you want to start the container:

    docker run --name mltoolkit -dit --rm -v ${PWD}:/app mltoolkit /bin/bash
    

Get a list of scrapers:

    docker exec -it mltoolkit php console.php
    

Run individual scraper:

    docker exec -it mltoolkit php console.php toscraper
    

Run python model train, load and predict test:

    docker exec -it mltoolkit bash -c "cd /app/mltoolkit && python3 test.py"
    

* * *
