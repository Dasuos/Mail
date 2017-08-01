<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

interface Message {
	public function type(): string;
	public function content(): string;
}