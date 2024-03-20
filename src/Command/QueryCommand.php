<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class QueryCommand extends Command
{
    protected static $defaultName = 'query:country';

    protected function configure()
    {
        $this
            ->setName('query:country') // Explicitly set the command name here.
            ->setDescription('Displays services provided by a specific country.')
            ->addArgument('countryCode', InputArgument::REQUIRED, 'The country code to query.');
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countryCode = strtoupper($input->getArgument('countryCode'));
        $filename = __DIR__ . '/../../services.csv';
    
        if (!file_exists($filename) || !is_readable($filename)) {
            $output->writeln('<error>Cannot read services file.</error>');
            return Command::FAILURE; // Ensure FAILURE and SUCCESS are properly defined as integers
        }
    
        $file = fopen($filename, 'r');
        $headers = fgetcsv($file);
        $services = [];
        while ($row = fgetcsv($file)) {
            $data = array_combine($headers, $row);
            // Change 'Country Code' to 'Country' to match your CSV header
            if (strtoupper($data['Country']) === $countryCode) {
                $services[] = $data;
            }
        }
    
        fclose($file);
    
        if (empty($services)) {
            $output->writeln("<comment>No services found for country code: $countryCode</comment>");
            return Command::SUCCESS; // Ensure SUCCESS is properly defined as 0
        }
    
        $table = new Table($output);
        $table->setHeaders($headers)->setRows($services);
        $table->render();
    
        return Command::SUCCESS; // Ensure SUCCESS is properly defined as 0
    }
    
}
