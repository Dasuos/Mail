<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

interface Message {

	const CHARSET = 'utf-8';

	public function headers(): string;
	public function content(): string;
}