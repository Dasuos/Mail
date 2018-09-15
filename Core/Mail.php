<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

interface Mail {

	public const NO_HEADERS = [];

	public function send(
		string $receiver,
		string $subject,
		Message $message,
		array $extensions
	): void;
}