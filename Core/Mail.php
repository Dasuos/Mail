<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

interface Mail {
	public function send(
		string $to, string $subject, Message $message, string $headers
	): void;
}