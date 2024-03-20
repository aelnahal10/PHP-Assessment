<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class SummaryCommand extends Command
{
    protected static $defaultName = 'summary:services';

    protected function configure()
    {
        $this
            ->setName('summary:services')
            ->setDescription('Displays a summary of services by country.');
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = __DIR__ . '/../../services.csv';

        if (!file_exists($filename) || !is_readable($filename)) {
            $output->writeln('<error>Cannot read services file.</error>');
            return Command::FAILURE;
        }

        $file = fopen($filename, 'r');
        $headers = fgetcsv($file); // Read the header row

        if ($headers === false) {
            $output->writeln('<error>Failed to read headers from services file.</error>');
            return Command::FAILURE;
        }

        $countryCodeIndex = array_search('Country', $headers);
        if ($countryCodeIndex === false) {
            $output->writeln('<error>"Country Code" column not found in services file.</error>');
            return Command::FAILURE;
        }

        $summary = [];
        while ($row = fgetcsv($file)) {
            $countryCode = $row[$countryCodeIndex];
            if (!isset($summary[$countryCode])) {
                $summary[$countryCode] = 0;
            }
            $summary[$countryCode]++;
        }

        fclose($file);

        if (count($summary) === 0) {
            $output->writeln("<comment>No data found in the services file.</comment>");
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Country', 'Total Services']);
        foreach ($summary as $code => $count) {
            $table->addRow([$code, $count]);
        }
        $table->render();

        return Command::SUCCESS;
    }
}
