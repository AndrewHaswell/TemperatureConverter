<?php

namespace ConverterConfig;

use Converter\Handlers\TemperatureCommandHandler;
use Webmozart\Console\Api\Args\Format\Option;
use Webmozart\Console\Config\DefaultApplicationConfig;

class ConverterConfig extends DefaultApplicationConfig
{
    protected function configure()
    {
        parent::configure();

        $this->setName('Converter')
            ->setVersion('1.0.0')
            ->beginCommand('temperature')
            ->setDescription('Converts between Celsius and Fahrenheit.')
            ->setHelp('A converter to quickly convert between Celsius to Fahrenheit or Fahrenheit to Celsius. Use one of the options -c or -f followed by a numeric temperature to run the command. The result is a float rounded to 3dp. Using verbose mode (-v) will give the result as a more human friendly sentence.')
            ->setHandler(fn() => new TemperatureCommandHandler())
            ->addOption('celsius', 'c', Option::OPTIONAL_VALUE, 'The temperature in Celsius to convert to Fahrenheit')
            ->addOption('fahrenheit', 'f', Option::OPTIONAL_VALUE, 'The temperature in Fahrenheit to convert to Celsius')
            ->end();
    }
}
