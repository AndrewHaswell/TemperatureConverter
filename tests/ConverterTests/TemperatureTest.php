<?php

namespace tests\ConverterTests;

use ConverterConfig\ConverterConfig;
use PHPUnit\Framework\TestCase;
use Webmozart\Console\Args\StringArgs;
use Webmozart\Console\ConsoleApplication;
use Webmozart\Console\IO\InputStream\StringInputStream;
use Webmozart\Console\IO\OutputStream\BufferedOutputStream;

class TemperatureTest extends TestCase
{

    /**
     * @var ConverterConfig
     */
    private ConverterConfig $config;

    /**
     * @var BufferedOutputStream
     */
    private BufferedOutputStream $errorStream;

    /**
     * @var BufferedOutputStream
     */
    private BufferedOutputStream $outputStream;

    /**
     * @var StringInputStream
     */
    private StringInputStream $inputStream;


    protected function setUp(): void
    {
        $this->config = new ConverterConfig();
        $this->config->setCatchExceptions(false);
        $this->config->setTerminateAfterRun(false);

        $this->inputStream = new StringInputStream();
        $this->outputStream = new BufferedOutputStream();
        $this->errorStream = new BufferedOutputStream();
    }

    public function testCommandReturnsSuccessfulStatus()
    {
        $command = 'temperature -c 42';
        $status = $this->runCommandWithStandardSetup($command);
        $this->assertEquals(0, $status);
    }

    public function testCommandReturnsCorrectTemperatureCalculations()
    {
        $commands = [];

        $celsiusTests = [32 => 0, '78.8' => 26, 122 => 50, 212 => 100];

        foreach ($celsiusTests as $fahrenheit => $celsius) {
            $commands[$fahrenheit] = 'temperature -c ' . $celsius;
        }

        $fahrenheitTests = ['-17.778' => 0, '-3.889' => 25, 10 => 50, '37.778' => 100];

        foreach ($fahrenheitTests as $celsius => $fahrenheit) {
            $commands[$celsius] = 'temperature -f ' . $fahrenheit;
        }

        $application = new ConsoleApplication($this->config);

        foreach ($commands as $temperature => $command) {
            $output = new BufferedOutputStream();
            $application->run(new StringArgs($command), $this->inputStream, $output, $this->errorStream);
            $this->assertEquals($temperature, trim($output->fetch()));
        }
    }

    public function testIfHandlesZeroTemperatures()
    {
        $commands = ['temperature -c 0', 'temperature -f 0'];
        $application = new ConsoleApplication($this->config);
        foreach ($commands as $command) {
            $status = $application->run(new StringArgs($command), $this->inputStream, $this->outputStream, $this->errorStream);
            $this->assertEquals(0, $status);
        }
    }

    public function testFailsIfBothCAndFAreSet()
    {
        $command = 'temperature -c 10 -f 10';
        $status = $this->runCommandWithStandardSetup($command);
        $this->assertEquals(1, $status);
        $this->assertNotEmpty($this->errorStream->fetch());
    }

    public function testFailsIfNeitherCOrFAreSet()
    {
        $command = 'temperature';
        $status = $this->runCommandWithStandardSetup($command);
        $this->assertEquals(1, $status);
        $this->assertNotEmpty($this->errorStream->fetch());
    }

    public function testFailsIfNonNumericValuesAreUsed()
    {
        $command = 'temperature -c X';
        $status = $this->runCommandWithStandardSetup($command);
        $this->assertEquals(1, $status);
        $this->assertNotEmpty($this->errorStream->fetch());
    }

    protected function runCommandWithStandardSetup(string $args)
    {
        $application = new ConsoleApplication($this->config);
        return $application->run(new StringArgs($args), $this->inputStream, $this->outputStream, $this->errorStream);
    }

}