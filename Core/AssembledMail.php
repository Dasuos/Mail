<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

final class AssembledMail implements Mail {

	public const HIGH_PRIORITY = 1, MIDDLE_PRIORITY = 3, LOWEST_PRIORITY = 5;

	private const PRIORITIES = [
		self::LOWEST_PRIORITY,
		self::MIDDLE_PRIORITY,
		self::HIGH_PRIORITY,
	];

	private $from;
	private $priority;

	public function __construct(string $from, int $priority = self::HIGH_PRIORITY) {
		$this->from = $from;
		$this->priority = $priority;
	}

	public function send(
		string $receiver,
		string $subject,
		Message $message,
		array $extensions = self::NO_HEADERS
	): void {
		if (!@mail(
			$receiver,
			$this->subject($subject),
			$message->content(),
			$this->headers(
				$this->from,
				$this->priority($this->priority),
				$extensions
					? $message->headers() + $extensions
					: $message->headers()
			)
		))
			throw new \UnexpectedValueException('Mail was not accepted for delivery');
	}

	private function subject(string $subject): string {
		return '=?UTF-8?B?' . base64_encode($subject) . '?=';
	}

	private function headers(
		string $from,
		int $priority,
		array $extensions = self::NO_HEADERS
	): string {
		return (string) new Headers(
			[
				'MIME-Version' => '1.0',
				'From' => $from,
				'Return-Path' => $from,
				'Date' => date('r'),
				'X-Sender' => $from,
				'X-Mailer' => 'PHP/' . phpversion(),
				'X-Priority' => $priority,
				'Message-Id' => new MessageIdentification,
			] + $extensions
		);
	}

	private function priority(int $priority): int {
		if (in_array($priority, self::PRIORITIES))
			return $priority;
		throw new \UnexpectedValueException(
			sprintf(
				'Allowed mail priority types are: %s',
				implode(', ', self::PRIORITIES)
			)
		);
	}
}