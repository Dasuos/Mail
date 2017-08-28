<?php
declare(strict_types = 1);
namespace Dasuos\Mail;

interface Headers {
	public function list(): array;
}