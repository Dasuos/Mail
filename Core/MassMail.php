<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

final class MassMail implements Mail {

	private $origin;
	private $list;
	private $header;

	public function __construct(
		Mail $origin,
		array $list,
		string $header = 'Bcc'
	) {
		$this->origin = $origin;
		$this->list = $list;
		$this->header = $header;
	}

	public function send(
		string $receiver,
		string $subject,
		Message $message,
		array $extensions = self::NO_HEADERS
	): void {
		$this->origin->send(
			$receiver,
			$subject,
			$message,
			$extensions
				? $this->header($this->header, $this->list) + $extensions
				: $this->header($this->header, $this->list)
		);
	}

	private function header(string $header, array $list): array {
		if (!in_array($header, ['Bcc', 'Cc'], true))
			throw new \UnexpectedValueException(
				'Only Bcc anc Cc headers are allowed'
			);
		return [$header => implode(',', $this->emails($list))];
	}

	private function emails(array $list): array {
		if (
			$list &&
			count(
				array_filter(
					$list,
					function(string $email) {
						return filter_var($email, FILTER_VALIDATE_EMAIL);
					}
				)
			) === count($list)
		)
			return $list;
		throw new \UnexpectedValueException('Invalid email list');
	}
}
