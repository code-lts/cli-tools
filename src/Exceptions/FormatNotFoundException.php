<?php

declare(strict_types = 1);

namespace CodeLts\CliTools\Exceptions;

class FormatNotFoundException extends \Exception
{

	public function __construct(string $formatName)
	{
		parent::__construct(sprintf('The format "%s" is not implemented.', $formatName));
	}

	public function getTip(): ?string
	{
		return null;
	}

}
