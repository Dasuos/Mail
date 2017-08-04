<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class DefinedMail implements Mail {

	private const CHARSET = 'utf-8';
	private const PRIORITY_TYPES = [1, 3, 5];
	private const NO_HEADERS = '';

	private $from;
	private $priority;

	public function __construct(string $from, int $priority) {
		$this->from = $from;
		$this->priority = $priority;
	}

	public function send(
		string $to,
		string $subject,
		Message $message,
		string $headers = self::NO_HEADERS
	): void {
		if (!@mail(
			$to,
			$this->subject($subject),
			$message->content(),
			$this->headers(
				$this->from,
				$this->priority($this->priority),
				$message->type(),
				$headers
			)
		))
			throw new \UnexpectedValueException(
				'Mail was not accepted for delivery'
			);
	}

	private function subject(string $content): string {
		iconv_set_encoding('internal_encoding', self::CHARSET);
		return substr(
			iconv_mime_encode('Subject', $content),
			strlen('Subject: ')
		);
	}

	private function headers(
		string $from,
		int $priority,
		string $type,
		string $additional = self::NO_HEADERS
	): string {
		$headers = implode(
			PHP_EOL, [
				'MIME-Version: 1.0',
				'From: ' . $from,
				'Return-Path: ' . $from,
				'Date: ' . date('r'),
				'X-Sender: ' . $from,
				'X-Mailer: PHP/' . phpversion(),
				'X-Priority: ' . $priority,
				$type,
			]
		);
		if ($additional)
			$headers .= PHP_EOL . $additional;
		return $headers;
	}

	private function priority(int $number): int {
		if (!in_array($number, self::PRIORITY_TYPES, true))
			throw new \UnexpectedValueException(
				'Mail priority type must be either 1, 3 or 5'
			);

	}
}