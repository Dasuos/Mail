<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class AssembledMail implements Mail {

	public const HIGH_PRIORITY = 1,
		MIDDLE_PRIORITY = 3,
		LOWEST_PRIORITY = 5;

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
		array $extensions = self::NO_HEADERS
	): void {
		if (!@mail(
			$to,
			$this->subject($subject),
			$message->content(),
			$this->headers(
				$this->from,
				$this->priority($this->priority),
				$extensions ? array_merge($message->headers(), $extensions) :
					$message->headers()
			)
		))
			throw new \UnexpectedValueException(
				'Mail was not accepted for delivery'
			);
	}

	private function subject(string $subject): string {
		return '=?UTF-8?Q?' . imap_8bit($subject) . '?=';
	}

	private function headers(
		string $from, int $priority, array $extensions = self::NO_HEADERS
	): string {
		$headers = array_merge([
			'MIME-Version' => '1.0',
			'From' => $from,
			'Return-Path' => $from,
			'Date' => date('r'),
			'X-Sender' => $from,
			'X-Mailer' => 'PHP/' . phpversion(),
			'X-Priority' => $priority,
		], $extensions);
		return implode(
			PHP_EOL, array_map(
				function ($value, $header) {
					return sprintf('%s: %s', $header, $value);
				}, $headers, array_keys($headers)
			)
		);
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