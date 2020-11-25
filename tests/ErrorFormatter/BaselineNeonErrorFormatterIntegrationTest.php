<?php

/*
 * (c) Copyright (c) 2016-2020 Ondřej Mirtes <ondrej@mirtes.cz>
 *
 * This source file is subject to the MIT license.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
declare(strict_types = 1);

namespace CodeLts\CliTools\ErrorFormatter\ErrorFormatter;

use Nette\Utils\Json;
use PHPUnit\Framework\TestCase;
use function chdir;
use function file_put_contents;
use function getcwd;

/**
 * @group exec
 */
class BaselineNeonErrorFormatterIntegrationTest extends TestCase
{

	public function testErrorWithTrait(): void
	{
		$output = $this->runPhpStan(__DIR__ . '/data/', null);
		$errors = Json::decode($output, Json::FORCE_ARRAY);
		$this->assertSame(10, array_sum($errors['totals']));
		$this->assertCount(6, $errors['files']);
	}

	public function testGenerateBaselineAndRunAgainWithIt(): void
	{
		$output = $this->runPhpStan(__DIR__ . '/data/', null, 'baselineNeon');
		$baselineFile = __DIR__ . '/../../../../baseline.neon';
		file_put_contents($baselineFile, $output);

		$output = $this->runPhpStan(__DIR__ . '/data/', $baselineFile);
		@unlink($baselineFile);
		$errors = Json::decode($output, Json::FORCE_ARRAY);
		$this->assertSame(0, array_sum($errors['totals']));
		$this->assertCount(0, $errors['files']);
	}

	public function testRunWindowsFileWithUnixBaseline(): void
	{
		$output = $this->runPhpStan(__DIR__ . '/data/WindowsNewlines.php', __DIR__ . '/data/unixBaseline.neon');
		$errors = Json::decode($output, Json::FORCE_ARRAY);
		$this->assertSame(0, array_sum($errors['totals']));
		$this->assertCount(0, $errors['files']);
	}

	public function testRunUnixFileWithWindowsBaseline(): void
	{
		$output = $this->runPhpStan(__DIR__ . '/data/UnixNewlines.php', __DIR__ . '/data/windowsBaseline.neon');
		$errors = Json::decode($output, Json::FORCE_ARRAY);
		$this->assertSame(0, array_sum($errors['totals']));
		$this->assertCount(0, $errors['files']);
	}

	private function runPhpStan(
		string $analysedPath,
		?string $configFile,
		string $errorFormatter = 'json'
	): string
	{
		$originalDir = getcwd();
		if ($originalDir === false) {
			throw new \PHPStan\ShouldNotHappenException();
		}
		chdir(__DIR__ . '/../../../..');
		exec(sprintf('%s %s clear-result-cache %s', escapeshellarg(PHP_BINARY), 'bin/phpstan', $configFile !== null ? '--configuration ' . escapeshellarg($configFile) : ''), $clearResultCacheOutputLines, $clearResultCacheExitCode);
		if ($clearResultCacheExitCode !== 0) {
			throw new \PHPStan\ShouldNotHappenException('Could not clear result cache.');
		}

		exec(sprintf('%s %s analyse --no-progress --error-format=%s --level=7 %s %s', escapeshellarg(PHP_BINARY), 'bin/phpstan', $errorFormatter, $configFile !== null ? '--configuration ' . escapeshellarg($configFile) : '', escapeshellarg($analysedPath)), $outputLines);
		chdir($originalDir);

		return implode("\n", $outputLines);
	}

}
