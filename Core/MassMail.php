<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class MassMail implements Mail {

	private $origin;
	private $list;
	private $mode;

	public function __construct(
		Mail $origin,
		array $list,
		string $mode = 'Bcc'
	) {
		$this->origin = $origin;
		$this->list = $list;
		$this->mode = $mode;
	}

	public function send(
		string $receiver,
		string $subject,
		Message $message,
		array $extensions = self::NO_HEADERS
	): void {
		$header = [
			$this->cc($this->mode) => implode(',', $this->emails($this->list)),
		];
		$this->origin->send(
			$receiver,
			$subject,
			$message,
			$extensions ? $header + $extensions : $header
		);
	}

	private function cc(string $option): string {
		$option = ucfirst(strtolower($option));
		if (!in_array($option, ['Bcc', 'Cc'], true))
			throw new \UnexpectedValueException(
				'Only Bcc anc Cc headers are allowed'
			);
		return $option;
	}

	private function emails(array $list): array {
		$valid = $list && count(
			array_filter(
				$list,
				function(string $email) {
					return filter_var($email, FILTER_VALIDATE_EMAIL);
				}
			)
		) === count($list);
		if ($valid)
			return $list;
		throw new \UnexpectedValueException('Invalid email list');
	}
}
