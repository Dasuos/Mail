<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class HtmlMessage extends \Tester\TestCase {

	public function testReturningAlternativeContentType() {
		$content = '<h1>Foo</h1><p>Bar</p>';
		Assert::equal(
			'Content-Type: multipart/alternative; boundary="81fd830c85363675edb98d2879916d8c"',
			(new Mail\HtmlMessage(
				$content, 'boundary'
			))->headers()
		);
	}

	public function testReturningContent() {
		$content = '<h1>Foo</h1><p>Bar</p>';
		Assert::same(
			preg_replace('/\s+/', ' ',
				'--81fd830c85363675edb98d2879916d8c 
				Content-Type: text/plain; charset=utf-8 
				Content-Transfer-Encoding: 7bit 

				\nFoo\n\nBar\n 

				--81fd830c85363675edb98d2879916d8c 
				Content-Type: text/html; charset=utf-8 
				Content-Transfer-Encoding: 7bit 
				
				<h1>Foo</h1><p>Bar</p> 

				--81fd830c85363675edb98d2879916d8c--'
			),
			preg_replace('/\s+/', ' ',
				(new Mail\HtmlMessage(
					$content, 'boundary'
				))->content()
			)
		);
	}
}

(new HtmlMessage())->run();
