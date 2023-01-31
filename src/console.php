<?php require __DIR__.'/vendor/autoload.php';

use Plexcorp\Mltoolkit\ProjectBuilder;
/*
* This is the entry point for all scrapers. You will need to add your scraper settings in the config.json
* file at the root of your project.
*
* After you've added your scraper settings, run - php console.php to get a list of available scrapers, you can
* then run any scraper by running php console.php [scrapername]
* This console is based on Symfony's console library - read more here: https://symfony.com/doc/current/components/console.html
*/
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$application = new Application();

// Load the configuration file to get a list of available scrapers.
$scrapers = json_decode(file_get_contents('./config.json'), true);

// Loop through add add each scraper as a console command.
foreach($scrapers as $scraper) {
    $application->register($scraper['name'])
        ->setDescription($scraper['description'])
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($scraper): int{

             // All scrapers should be in the scrapers/ folder and use this namespace: Plexcorp\Mltoolkit\Core\Scraper
            $klass = "Plexcorp\\Mltoolkit\\" . $scraper['class'];
            $runner = new $klass($output, $scraper['base_url']);
            $result = $runner->run($scraper['feed_path']);
            if (!$result) {
                return Command::FAILURE;
            }
            return Command::SUCCESS;
        });
}

$application->run();