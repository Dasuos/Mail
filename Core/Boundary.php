<?php
declare(strict_types = 1);

namespace Dasuos\Mail;

interface Boundary {

	public function hash(): string;
}