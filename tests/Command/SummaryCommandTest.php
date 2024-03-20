<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Command\SummaryCommand;

class SummaryCommandTest extends TestCase
{
    public function testExecute()
    {
        // Initialize the application and add the SummaryCommand
        $application = new Application();
        $application->add(new SummaryCommand());

        $command = $application->find('summary:services');
        $commandTester = new CommandTester($command);

        // Execute the command without any arguments
        $commandTester->execute([]);

        // Check that the summary output is correct
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Country', $output); // Assert table headers are included
        $this->assertStringContainsString('Total Services', $output); // Make sure the correct column is shown
    }
}
