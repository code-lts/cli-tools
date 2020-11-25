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

namespace CodeLts\CliTools\File;

use function array_slice;
use function str_replace;

class ParentDirectoryRelativePathHelper implements RelativePathHelper
{

	/**
	 * @var string
	 */
	private $parentDirectory;

	public function __construct(string $parentDirectory)
	{
		$this->parentDirectory = $parentDirectory;
	}

	public function getRelativePath(string $filename): string
	{
		$parentParts = explode('/', trim(str_replace('\\', '/', $this->parentDirectory), '/'));
		$parentPartsCount = count($parentParts);
		$filenameParts = explode('/', trim(str_replace('\\', '/', $filename), '/'));
		$filenamePartsCount = count($filenameParts);

		$i = 0;
		for (; $i < $filenamePartsCount; $i++) {
			if ($parentPartsCount < $i + 1) {
				break;
			}

			$parentPath = implode('/', array_slice($parentParts, 0, $i + 1));
			$filenamePath = implode('/', array_slice($filenameParts, 0, $i + 1));

			if ($parentPath !== $filenamePath) {
				break;
			}
		}

		if ($i === 0) {
			return $filename;
		}

		$dotsCount = $parentPartsCount - $i;

		return str_repeat('../', $dotsCount) . implode('/', array_slice($filenameParts, $i));
	}

}
