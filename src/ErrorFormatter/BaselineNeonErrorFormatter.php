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

namespace PHPStan\Command\ErrorFormatter;

use Nette\DI\Helpers;
use Nette\Neon\Neon;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\Output;
use PHPStan\File\RelativePathHelper;
use function preg_quote;

class BaselineNeonErrorFormatter implements ErrorFormatter
{

	private \PHPStan\File\RelativePathHelper $relativePathHelper;

	public function __construct(RelativePathHelper $relativePathHelper)
	{
		$this->relativePathHelper = $relativePathHelper;
	}

	public function formatErrors(
		AnalysisResult $analysisResult,
		Output $output
	): int
	{
		if (!$analysisResult->hasErrors()) {
			$output->writeRaw(Neon::encode([
				'parameters' => [
					'ignoreErrors' => [],
				],
			], Neon::BLOCK));
			return 0;
		}

		$fileErrors = [];
		foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
			if (!$fileSpecificError->canBeIgnored()) {
				continue;
			}
			$fileErrors[$fileSpecificError->getFilePath()][] = $fileSpecificError->getMessage();
		}

		$errorsToOutput = [];
		foreach ($fileErrors as $file => $errorMessages) {
			$fileErrorsCounts = [];
			foreach ($errorMessages as $errorMessage) {
				if (!isset($fileErrorsCounts[$errorMessage])) {
					$fileErrorsCounts[$errorMessage] = 1;
					continue;
				}

				$fileErrorsCounts[$errorMessage]++;
			}

			foreach ($fileErrorsCounts as $message => $count) {
				$errorsToOutput[] = [
					'message' => Helpers::escape('#^' . preg_quote($message, '#') . '$#'),
					'count' => $count,
					'path' => Helpers::escape($this->relativePathHelper->getRelativePath($file)),
				];
			}
		}

		$output->writeRaw(Neon::encode([
			'parameters' => [
				'ignoreErrors' => $errorsToOutput,
			],
		], Neon::BLOCK));

		return 1;
	}

}
