<?php

namespace OCA\OpenCatalogi\Exception;

use Exception;

class DirectoryUrlException extends Exception
{
	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

}
