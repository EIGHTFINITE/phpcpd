<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD;

use SebastianBergmann\CliParser\Exception as CliParserException;
use SebastianBergmann\CliParser\Parser as CliParser;

final class ArgumentsBuilder
{
    /**
     * @throws ArgumentsBuilderException
     */
    public function build(array $argv): Arguments
    {
        try {
            $options = (new CliParser)->parse(
                $argv,
                'hv',
                [
                    'suffix=',
                    'exclude=',
                    'log-pmd=',
                    'fuzzy',
                    'min-lines=',
                    'min-tokens=',
                    'head-equality=',
                    'edit-distance=',
                    'max-percentage=',
                    'verbose',
                    'help',
                    'version',
                    'algorithm=',
                ]
            );
        } catch (CliParserException $e) {
            throw new ArgumentsBuilderException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        $directories      = $options[1];
        $exclude          = [];
        $suffixes         = ['.php'];
        $pmdCpdXmlLogfile = null;
        $linesThreshold   = 5;
        $tokensThreshold  = 70;
        $editDistance     = 5;
        $headEquality     = 10;
        $maxPercentage    = 0;
        $fuzzy            = false;
        $verbose          = false;
        $help             = false;
        $version          = false;
        $algorithm        = 'rabin-karp';

        foreach ($options[0] as $option) {
            switch ($option[0]) {
                case '--suffix':
                    $suffixes[] = $option[1];

                    break;

                case '--exclude':
                    $exclude[] = $option[1];

                    break;

                case '--log-pmd':
                    $pmdCpdXmlLogfile = $option[1];

                    break;

                case '--fuzzy':
                    $fuzzy = true;

                    break;

                case '--min-lines':
                    $linesThreshold = (int) $option[1];

                    break;

                case '--min-tokens':
                    $tokensThreshold = (int) $option[1];

                    break;

                case '--head-equality':
                    $headEquality = (int) $option[1];

                    break;

                case '--edit-distance':
                    $editDistance = (int) $option[1];

                case '--max-percentage':
                    $maxPercentage = (float) $option[1];

                    if ($maxPercentage < 0 || $maxPercentage > 100) {
                        throw new ArgumentsBuilderException(
                            'Maximum percentage allowed must be between 0 and 100'
                        );
                    }

                    break;

                case '--verbose':
                    $verbose = true;

                    break;

                case 'h':
                case '--help':
                    $help = true;

                    break;

                case 'v':
                case '--version':
                    $version = true;

                    break;

                case '--algorithm':
                    $algorithm = (string) $option[1];

                    break;
            }
        }

        if (empty($options[1]) && !$help && !$version) {
            throw new ArgumentsBuilderException(
                'No directory specified'
            );
        }

        return new Arguments(
            $directories,
            $suffixes,
            $exclude,
            $pmdCpdXmlLogfile,
            $linesThreshold,
            $tokensThreshold,
            $maxPercentage,
            $fuzzy,
            $verbose,
            $help,
            $version,
            $algorithm,
            $editDistance,
            $headEquality
        );
    }
}
