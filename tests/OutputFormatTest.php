<?php

declare(strict_types=1);

namespace CodeLts\CliTools\Tests;

use CodeLts\CliTools\AnalysisResult;
use CodeLts\CliTools\ErrorFormatter\ErrorFormatter;
use CodeLts\CliTools\File\NullRelativePathHelper;
use CodeLts\CliTools\OutputFormat;
use Exception;

class OutputFormatTest extends ErrorFormatterTestCase
{

    /**
     * @return array[]
     */
    public function dataProviderFormatsNames(): array
    {
        $formats = [];
        foreach (OutputFormat::VALID_OUTPUT_FORMATS as $format) {
            $formats[] = [$format];
        }
        return $formats;
    }

    /**
     * @dataProvider dataProviderFormatsNames
     */
    public function testValidFormats(string $formatName): void
    {
        $this->assertTrue(OutputFormat::checkOutputFormatIsValid($formatName));
    }

    /**
     * @dataProvider dataProviderFormatsNames
     */
    public function testInValidFormats(string $formatName): void
    {
        $formatName = 'foo' . $formatName;
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'Error formatter "' . $formatName . '" not found.'
            . ' Available error formatters are: raw, rawtext, table, checkstyle, json, junit, prettyJson, gitlab, github, teamcity'
        );
        $this->assertTrue(OutputFormat::checkOutputFormatIsValid($formatName));
    }

    /**
     * @dataProvider dataProviderFormatsNames
     */
    public function testGetFormatterForChoice(string $formatName): void
    {
        $this->assertInstanceOf(ErrorFormatter::class, OutputFormat::getFormatterForChoice($formatName, new NullRelativePathHelper()));
    }

    /**
     * @dataProvider dataProviderFormatsNames
     */
    public function testFormatterForChoice(string $formatName): void
    {
        OutputFormat::displayUserChoiceFormat(
            $formatName,
            $this->getAnalysisResult(3, 2, 2),
            null,
            $this->getOutput()
        );
        $outputContent = $this->getOutputContent();
        $this->assertNotEmpty($outputContent);
    }

}