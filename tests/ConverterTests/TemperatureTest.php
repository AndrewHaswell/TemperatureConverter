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

    public function test_command_returns_successful_status()
    {
        $command = 'temperature -c 42';
        $status = $this->run_command_with_standard_setup($command);
        $this->assertEquals(0, $status);
    }

    public function test_command_returns_correct_celsius_calculations()
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

    public function test_if_handles_zero_temperatures()
    {
        $commands = ['temperature -c 0', 'temperature -f 0'];
        $application = new ConsoleApplication($this->config);
        foreach ($commands as $command) {
            $status = $application->run(new StringArgs($command), $this->inputStream, $this->outputStream, $this->errorStream);
            $this->assertEquals(0, $status);
        }
    }

    public function test_fails_if_both_c_and_f_are_set()
    {
        $command = 'temperature -c 10 -f 10';
        $status = $this->run_command_with_standard_setup($command);
        $this->assertEquals(1, $status);
        $this->assertNotEmpty($this->errorStream->fetch());
    }

    public function test_fails_if_neither_c_or_f_are_set()
    {
        $command = 'temperature';
        $status = $this->run_command_with_standard_setup($command);
        $this->assertEquals(1, $status);
        $this->assertNotEmpty($this->errorStream->fetch());
    }

    public function test_fails_if_non_numeric_values_are_used()
    {
        $command = 'temperature -c X';
        $status = $this->run_command_with_standard_setup($command);
        $this->assertEquals(1, $status);
        $this->assertNotEmpty($this->errorStream->fetch());
    }

    protected function run_command_with_standard_setup(string $args)
    {
        $application = new ConsoleApplication($this->config);
        return $application->run(new StringArgs($args), $this->inputStream, $this->outputStream, $this->errorStream);
    }

}