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

use Nette\Utils\Json;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\Output;
use PHPStan\File\RelativePathHelper;

/**
 * @see https://docs.gitlab.com/ee/user/project/merge_requests/code_quality.html#implementing-a-custom-tool
 */
class GitlabErrorFormatter implements ErrorFormatter
{

	private RelativePathHelper $relativePathHelper;

	public function __construct(RelativePathHelper $relativePathHelper)
	{
		$this->relativePathHelper = $relativePathHelper;
	}

	public function formatErrors(AnalysisResult $analysisResult, Output $output): int
	{
		$errorsArray = [];

		foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
			$error = [
				'description' => $fileSpecificError->getMessage(),
				'fingerprint' => hash(
					'sha256',
					implode(
						[
							$fileSpecificError->getFile(),
							$fileSpecificError->getLine(),
							$fileSpecificError->getMessage(),
						]
					)
				),
				'location' => [
					'path' => $this->relativePathHelper->getRelativePath($fileSpecificError->getFile()),
					'lines' => [
						'begin' => $fileSpecificError->getLine(),
					],
				],
			];

			if (!$fileSpecificError->canBeIgnored()) {
				$error['severity'] = 'blocker';
			}

			$errorsArray[] = $error;
		}

		foreach ($analysisResult->getNotFileSpecificErrors() as $notFileSpecificError) {
			$errorsArray[] = [
				'description' => $notFileSpecificError,
				'fingerprint' => hash('sha256', $notFileSpecificError),
				'location' => [
					'path' => '',
					'lines' => [
						'begin' => 0,
					],
				],
			];
		}

		$json = Json::encode($errorsArray, Json::PRETTY);

		$output->writeRaw($json);

		return $analysisResult->hasErrors() ? 1 : 0;
	}

}
