<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="None">
        
        
        <link rel="shortcut icon" href="img/favicon.ico">
        <title>Machine learning scraper toolkit</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet">
        <link href="css/base.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/styles/github.min.css">

        <script src="js/jquery-1.10.2.min.js" defer></script>
        <script src="js/bootstrap.min.js" defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.5.0/highlight.min.js"></script>
        <script>hljs.initHighlightingOnLoad();</script> 
    </head>

    <body class="homepage">
        <div class="navbar fixed-top navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href=".">Machine learning scraper toolkit</a>

                <!-- Expanded navigation -->
                <div id="navbar-collapse" class="navbar-collapse collapse">

                    <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-toggle="modal" data-target="#mkdocs_search_modal">
                                <i class="fa fa-search"></i> Search
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                    <div class="col-md-3"><div class="navbar-light navbar-expand-md bs-sidebar hidden-print affix" role="complementary">
    <div class="navbar-header">
        <button type="button" class="navbar-toggler collapsed" data-toggle="collapse" data-target="#toc-collapse" title="Table of Contents">
            <span class="fa fa-angle-down"></span>
        </button>
    </div>

    
    <div id="toc-collapse" class="navbar-collapse collapse card bg-secondary">
        <ul class="nav flex-column">
            
            <li class="nav-item" data-level="1"><a href="#introduction" class="nav-link">Introduction!</a>
              <ul class="nav flex-column">
        
            <li class="nav-item" data-level="2"><a href="#project-layout" class="nav-link">Project layout</a>
              <ul class="nav flex-column">
              </ul>
            </li>
            <li class="nav-item" data-level="2"><a href="#configuring-scrapers" class="nav-link">Configuring scrapers</a>
              <ul class="nav flex-column">
              </ul>
            </li>
            <li class="nav-item" data-level="2"><a href="#setting-up-docker" class="nav-link">Setting up docker</a>
              <ul class="nav flex-column">
              </ul>
            </li>
              </ul>
            </li>
            
     
        </ul>
    </div>
</div></div>
                    <div class="col-md-9" role="main">

<h1 id="introduction">Introduction!</h1>
<p>Thank you for taking the time to checkout this project.</p>
<p>To get started you first will need to have docker running on your machine, you can get install steps here : https://docs.docker.com/engine/install/</p>
<p>It is possible to run this project without docker on linux and Mac OS by simply installing python 3 and PHP 7+. You will also need CURL installed on your system and as well as the php curl extension. Below is a list of suggested php extensions to install if you are running this project without docker:</p>
<ul>
<li>bcmath</li>
<li>curl</li>
<li>zip</li>
<li>mbstring</li>
<li>dom</li>
</ul>
<p>For python, you will need python 3+ and fasttext: <code>pip install fasttext</code></p>

<h2 id="project-layout">Project layout</h2>
<pre><code>documentation/   # Documentation for this project.
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
</code></pre>
<h2 id="configuring-scrapers">Configuring scrapers</h2>
<p>In the project root you will find a <code>config.json</code> file, which contains a list of 
scraper configuration nodes. Each scraper configuration should contain a definition 
similar to the following:</p>
<pre><code>    {
        &quot;name&quot;: &quot;myawesomeshopifyexamplestore.com&quot;, // this is the name used when running the scraper
        &quot;class&quot;: &quot;Shopify&quot;, // PHP class name as found in the scrapers folder.
        &quot;feed_path&quot;: &quot;./feeds/myawesomeshopifyexamplestore.json&quot;, // Where to store the scraped data.
        &quot;base_url&quot;: &quot;https:www.myawesomeshopifyexamplestore.co/&quot;, // The URL to scrape.
        &quot;description&quot;: &quot;A sample scrapper that scrapes shopify products&quot;,
        &quot;classifier&quot;: {
            &quot;feed_path&quot;: &quot;../feeds/myawesomeshopifyexamplestore.json&quot;, // Same as feed_path above
            &quot;model_path&quot;: &quot;../models/myawesomeshopifyexamplestore.bin&quot;, // Where to save the compiled model.
            // Where to store the dataset formatted from the scraped data.
            &quot;dataset_path&quot;: &quot;../models/datasets/myawesomeshopifyexamplestore.txt&quot;,
            &quot;field&quot;: &quot;category&quot;, // The field from the scraped data to use as a category label.
            &quot;text&quot;: &quot;title&quot;, // The field to use as text training data.
            // Fasttext configuration's - learn more here:
            // https://fasttext.cc/docs/en/options.html
            &quot;loss_function&quot;: &quot;ova&quot;,
            &quot;epochs&quot;: 25,
            &quot;ngrams&quot;: 2,
            &quot;threads&quot;: 30
         }
     }
</code></pre>
<h2 id="setting-up-docker">Setting up docker</h2>
<p>To get a container up and running with Python and PHP dependencies - there is a "Dockerfile"
in the project root directory - you can build and run the docker container as follows:</p>
<p>Setting up the container:</p>
<pre><code>docker build . -t mltoolkit
docker run --name mltoolkit -dit --rm -v ${PWD}:/app mltoolkit /bin/bash
docker exec -it mltoolkit composer install
</code></pre>
<p>Note: Once you've built the container, you do not need to re-run all the above each time, simply run this whenever you want to start the container:</p>
<pre><code>docker run --name mltoolkit -dit --rm -v ${PWD}:/app mltoolkit /bin/bash
</code></pre>
<p>Get a list of scrapers:</p>
<pre><code>docker exec -it mltoolkit php console.php
</code></pre>
<p>Run individual scraper:</p>
<pre><code>docker exec -it mltoolkit php console.php toscraper
</code></pre>
<p>Run python model train, load and predict test:</p>
<pre><code>docker exec -it mltoolkit bash -c &quot;cd /app/mltoolkit &amp;&amp; python3 test.py&quot;
</code></pre>
</div>
            </div>
        </div>

        <footer class="col-md-12">
            <hr>
            <p>Documentation built with <a href="https://www.mkdocs.org/">MkDocs</a>.</p>
        </footer>
        <script>
            var base_url = ".",
                shortcuts = {"help": 191, "next": 78, "previous": 80, "search": 83};
        </script>
        <script src="js/base.js" defer></script>
        <script src="search/main.js" defer></script>

        <div class="modal" id="mkdocs_search_modal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="searchModalLabel">Search</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            </div>
            <div class="modal-body">
                <p>From here you can search these documents. Enter your search terms below.</p>
                <form>
                    <div class="form-group">
                        <input type="search" class="form-control" placeholder="Search..." id="mkdocs-search-query" title="Type search term here">
                    </div>
                </form>
                <div id="mkdocs-search-results" data-no-results-text="No results found"></div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div><div class="modal" id="mkdocs_keyboard_modal" tabindex="-1" role="dialog" aria-labelledby="keyboardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="keyboardModalLabel">Keyboard Shortcuts</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            </div>
            <div class="modal-body">
              <table class="table">
                <thead>
                  <tr>
                    <th style="width: 20%;">Keys</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="help shortcut"><kbd>?</kbd></td>
                    <td>Open this help</td>
                  </tr>
                  <tr>
                    <td class="next shortcut"><kbd>n</kbd></td>
                    <td>Next page</td>
                  </tr>
                  <tr>
                    <td class="prev shortcut"><kbd>p</kbd></td>
                    <td>Previous page</td>
                  </tr>
                  <tr>
                    <td class="search shortcut"><kbd>s</kbd></td>
                    <td>Search</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

    </body>
</html>

<!--
MkDocs version : 1.4.2
Build Date UTC : 2023-01-28 17:26:30.190084+00:00
-->
