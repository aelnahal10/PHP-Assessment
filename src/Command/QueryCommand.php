<?php
namespace App\Command;

// Import necessary Symfony components for building the command.
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Command to query and display services provided by a specific country.
 */
class QueryCommand extends Command
{
    // Define a default name for the command for easier access.
    protected static $defaultName = 'query:country';

    /**
     * Configuration method to set up the command with a name, description, and arguments.
     */
    protected function configure()
    {
        $this
            ->setName('query:country') // Explicitly set the command name.
            ->setDescription('Displays services provided by a specific country.') // Describe what the command does.
            ->addArgument('countryCode', InputArgument::REQUIRED, 'The country code to query.'); // Define required input argument.
    }
    

    /**
     * The execute method contains the logic executed when the command is called.
     * It reads from a CSV file and displays services related to a specified country code.
     *
     * @param InputInterface $input Interface to the input stream.
     * @param OutputInterface $output Interface to the output stream.
     * @return int Returns status code of command execution.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Get the country code from the command line input, converting it to uppercase.
        $countryCode = strtoupper($input->getArgument('countryCode'));
        // Define the path to the CSV file containing services data.
        $filename = __DIR__ . '/../../services.csv';
    
        // Check if the services file exists and is readable. If not, display an error message.
        if (!file_exists($filename) || !is_readable($filename)) {
            $output->writeln('<error>Cannot read services file.</error>');
            return Command::FAILURE; // Signal a command failure.
        }
    
        // Open the CSV file for reading.
        $file = fopen($filename, 'r');
        // Read the first line to extract column headers.
        $headers = fgetcsv($file);
        // Initialize an array to store services matching the query.
        $services = [];
        // Iterate over each row in the file.
        while ($row = fgetcsv($file)) {
            $data = array_combine($headers, $row);
            // If the row's country matches the query, add it to the services array.
            if (strtoupper($data['Country']) === $countryCode) {
                $services[] = $data;
            }
        }
    
        // Close the file handle.
        fclose($file);
    
        // If no services were found for the specified country code, notify the user.
        if (empty($services)) {
            $output->writeln("<comment>No services found for country code: $countryCode</comment>");
            return Command::SUCCESS; // End the command with a success status code.
        }
    
        // Display the found services in a table format.
        $table = new Table($output);
        $table->setHeaders($headers)->setRows($services);
        $table->render();
    
        return Command::SUCCESS; // End the command with a success status code.
    }
    
}
