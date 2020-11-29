<?php

declare(strict_types = 1);

namespace CodeLts\CliTools\ErrorFormatter;

use CodeLts\CliTools\AnalysisResult;
use CodeLts\CliTools\Output;

class RawTextErrorFormatter implements ErrorFormatter
{

	public function formatErrors(
		AnalysisResult $analysisResult,
		Output $output
	): int
	{
		foreach ($analysisResult->getNotFileSpecificErrors() as $notFileSpecificError) {
			$output->writeRaw('<fg=red>ERROR</>: ');
			$output->writeRaw($notFileSpecificError);
		}

		foreach ($analysisResult->getFileSpecificErrors() as $error) {
			$output->writeRaw('<fg=red>ERROR</>: ');
			$output->writeRaw(
				sprintf('%s in %s:%d', $error->getMessage(), $error->getFile() ?? '', $error->getLine())
			);
		}

		foreach ($analysisResult->getWarnings() as $warning) {
			$output->writeRaw('<fg=yellow>WARNING</>: ');
			$output->writeRaw($warning);
		}


		return $analysisResult->hasErrors() ? 1 : 0;
	}

}
