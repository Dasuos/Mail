<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

interface Message {

	const CHARSET = 'utf-8';

	public function headers(): array;
	public function content(): string;
}