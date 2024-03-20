<?php
// Define the namespace to organize the command within the application's structure.
namespace App\Command;

// Import the necessary components from the Symfony Console component.
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * A command to display a summary of services categorized by country.
 */
class SummaryCommand extends Command
{
    // Set a default name for this command to be used in the console.
    protected static $defaultName = 'summary:services';

    /**
     * Configures the command settings like name and description.
     */
    protected function configure()
    {
        $this
            ->setName('summary:services') // Explicitly set the command name.
            ->setDescription('Displays a summary of services by country.'); // Set a brief description of what this command does.
    }
    
    /**
     * Executes the command logic: Reads a CSV file and displays a summary of services by country.
     *
     * @param InputInterface $input The input interface provides access to the arguments and options.
     * @param OutputInterface $output The output interface is used to print messages to the console.
     * @return int Returns a status code indicating the outcome of the command execution.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Define the file path to the CSV file containing services data.
        $filename = __DIR__ . '/../../services.csv';

        // Check if the file exists and is readable, otherwise print an error.
        if (!file_exists($filename) || !is_readable($filename)) {
            $output->writeln('<error>Cannot read services file.</error>');
            return Command::FAILURE; // Indicate a failure with the appropriate status code.
        }

        // Open the CSV file for reading.
        $file = fopen($filename, 'r');
        // Read the header row to understand column structure.
        $headers = fgetcsv($file); 

        // Check if reading the header was successful.
        if ($headers === false) {
            $output->writeln('<error>Failed to read headers from services file.</error>');
            return Command::FAILURE;
        }

        // Find the index of the 'Country' column.
        $countryCodeIndex = array_search('Country', $headers);
        if ($countryCodeIndex === false) {
            $output->writeln('<error>"Country Code" column not found in services file.</error>');
            return Command::FAILURE;
        }

        // Initialize an array to hold the summary of services by country.
        $summary = [];
        // Loop through each row in the CSV file.
        while ($row = fgetcsv($file)) {
            $countryCode = $row[$countryCodeIndex]; // Extract the country code from the row.
            if (!isset($summary[$countryCode])) {
                $summary[$countryCode] = 0; // Initialize counter for this country if it doesn't exist.
            }
            $summary[$countryCode]++; // Increment the counter for each service found in this country.
        }

        // Close the file handle after reading.
        fclose($file);

        // If no data was found, inform the user and exit successfully.
        if (count($summary) === 0) {
            $output->writeln("<comment>No data found in the services file.</comment>");
            return Command::SUCCESS;
        }

        // Create a table instance to format the output nicely.
        $table = new Table($output);
        // Set the headers for the table.
        $table->setHeaders(['Country', 'Total Services']);
        // Populate the table with data from the summary.
        foreach ($summary as $code => $count) {
            $table->addRow([$code, $count]); // Add a row for each country with its service count.
        }
        // Render the table to display the data.
        $table->render();

        return Command::SUCCESS; // Indicate a successful execution.
    }
}
