<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

interface Mail {
	public function send(
		string $to, string $subject, string $message, string $headers
	): void;
}