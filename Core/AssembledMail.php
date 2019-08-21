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
			$this->email($receiver, 'receiver'),
			$this->subject($subject),
			$this->content($message),
			$this->headers(
				$this->from,
				$this->priority($this->priority),
				$extensions
					? $message->headers() + $extensions
					: $message->headers()
			)
		))
			throw new \UnexpectedValueException(
				'Mail was not accepted for delivery'
			);
	}

	private function email(string $address, string $description): string {
		if (filter_var($address, FILTER_VALIDATE_EMAIL))
			return $address;
		throw new \UnexpectedValueException(
			sprintf('Invalid %s email', $description)
		);
	}

	private function subject(string $subject): string {
		return '=?UTF-8?B?' . base64_encode(
			str_ireplace(["\r", "\n", '%0A', '%0D'], '', $subject)
		) . '?=';
	}

	private function content(Message $message): string {
		return str_replace("\n.", "\n..", $message->content());
	}

	private function headers(
		string $from,
		int $priority,
		array $extensions = self::NO_HEADERS
	): string {
		return (string) new Headers(
			[
				'MIME-Version' => '1.0',
				'From' => $this->email($from, 'sender'),
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
		if (in_array($priority, self::PRIORITIES, true))
			return $priority;
		throw new \UnexpectedValueException(
			sprintf(
				'Allowed mail priority types are: %s',
				implode(', ', self::PRIORITIES)
			)
		);
	}
}
