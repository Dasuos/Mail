<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class RawMail implements Mail {

	public function send(
		string $to, string $subject, string $message, string $headers
	): void {
		mail($to, $subject, $message->content(), $headers);
	}
}