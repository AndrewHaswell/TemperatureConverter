<?php

namespace Converter\Handlers;

use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\IO\IO;

class TemperatureCommandHandler
{

    /**
     * Converts temperature from celsius to fahrenheit or vice versa
     *
     * @param Args $args
     * @param IO $io
     * @return int
     */
    public function handle(Args $args, IO $io)
    {
        $celsius = $args->isOptionSet('celsius') ? $args->getOption('celsius') : false;
        $fahrenheit = $args->isOptionSet('fahrenheit') ? $args->getOption('fahrenheit') : false;
        $verbose = $args->isOptionSet('verbose');

        if ($celsius !== false && $fahrenheit !== false) {
            $io->errorLine('Please use either the celsius or fahrenheit option.');
            return 1;
        }

        if ($celsius === false && $fahrenheit === false) {
            $io->errorLine('Requires either the celsius or fahrenheit option.');
            return 1;
        }

        if ($celsius !== false) {
            if (is_numeric($celsius)) {
                if ($verbose) {
                    $io->writeLine('<b>' . $celsius . '°C</b> in Fahrenheit is <b>' .
                        $this->convertCelsiusToFahrenheit($celsius) . '°F</b>');
                } else {
                    $io->writeLine($this->convertCelsiusToFahrenheit($celsius));
                }
            } else {
                $io->errorLine('The celsius option must be numerical.');
                return 1;
            }
        }

        if ($fahrenheit !== false) {
            if (is_numeric($fahrenheit)) {
                if ($verbose) {
                    $io->writeLine('<b>' . $fahrenheit . '°F</b> in Celsius is <b>' .
                        $this->convertFahrenheitToCelsius($fahrenheit) . '°C</b>');
                } else {
                    $io->writeLine($this->convertFahrenheitToCelsius($fahrenheit));
                }
            } else {
                $io->errorLine('The fahrenheit option must be numerical.');
                return 1;
            }
        }
        return 0;
    }

    /**
     * Calculation to convert C to F
     *
     * @param $celsius
     * @return float
     */
    protected function convertCelsiusToFahrenheit($celsius)
    {
        return round(($celsius * 9 / 5) + 32, 3);
    }

    /**
     * Calculation to convert F to C
     *
     * @param $fahrenheit
     * @return float
     */
    protected function convertFahrenheitToCelsius($fahrenheit)
    {
        return round(($fahrenheit - 32) / 1.8, 3);
    }
}

