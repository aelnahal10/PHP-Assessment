<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Command\QueryCommand;

class QueryCommandTest extends TestCase
{
    public function testExecute()
    {
        // Set up the application and command
        $application = new Application();
        $application->add(new QueryCommand());

        $command = $application->find('query:country');
        $commandTester = new CommandTester($command);

        // Execute the command with a mock country code
        $countryCode = 'fr'; 
        $commandTester->execute([
            'countryCode' => $countryCode,
        ]);

        // Verify the output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Ref', $output);
        $this->assertStringContainsString('Centre', $output);
        $this->assertStringContainsString('Service', $output);
        $this->assertStringContainsString('Country', $output);

    }
}
