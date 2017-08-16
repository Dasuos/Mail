<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class PlainMessage extends \Tester\TestCase {

	public function testReturningContentType() {
		Assert::equal(
			'Content-Type: text/plain; charset=utf-8'
			. PHP_EOL . 'Content-Transfer-Encoding: 7bit',
			(new Mail\PlainMessage('foo bar'))->headers()
		);
	}

	public function testReturningContent() {
		Assert::equal(
			'foo bar',
			(new Mail\PlainMessage('foo bar'))->content()
		);
	}

	public function testReturningContentWithFooter() {
		Assert::equal(
			'foo bar-- signature',
			(new Mail\PlainMessage('foo bar', 'signature'))->content()
		);
	}
}

(new PlainMessage())->run();
