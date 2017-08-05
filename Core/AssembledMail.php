<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class AssembledMail implements Mail {

	public const HIGH_PRIORITY = 1;
	public const MIDDLE_PRIORITY = 3;
	public const LOWEST_PRIORITY = 5;

	private const CHARSET = 'utf-8';

	private $from;
	private $priority;

	public function __construct(
		string $from, int $priority = self::HIGH_PRIORITY
	) {
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
				'Content-Type: ' . $type,
			]
		);
		if ($additional)
			$headers .= PHP_EOL . $additional;
		return $headers;
	}

	private function priority(int $priority): int {
		if (!in_array($priority,
			[self::HIGH_PRIORITY, self::MIDDLE_PRIORITY, self::LOWEST_PRIORITY]
		))
			throw new \UnexpectedValueException(
				sprintf('Allowed mail priority types are: ', implode(
					', ', [
						self::HIGH_PRIORITY,
						self::MIDDLE_PRIORITY,
						self::LOWEST_PRIORITY
					]
				))
			);
		return $priority;
	}
}