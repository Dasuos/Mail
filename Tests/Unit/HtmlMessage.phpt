<?php
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class HtmlMessage extends \Tester\TestCase {

	public function testReturningAlternativeContentType() {
		Assert::same(
			['Content-Type' => preg_replace(
				'~"[0-9a-z]*"~',
				'""',
				'multipart/alternative; boundary="81fd830c85363675edb98d2879916d8c"'
			)],
			preg_replace(
				'~"[0-9a-z]*"~',
				'""',
				(new Mail\HtmlMessage(
					'<h1>Foo</h1><p>Bar</p>', 'boundary'
				))->headers()
			)
		);
	}

	public function testReturningPlainTextAndHtml() {
		Assert::same(
			preg_replace(
				'~--[0-9a-z]*(\s|--)~', '',
				preg_replace(
					'/\s+/', ' ',
					'--boundary 
					Content-Type: text/plain; charset=utf-8 
					Content-Transfer-Encoding: 7bit 
	
					\nFoo\n\nBar\n 
	
					--boundary  
					Content-Type: text/html; charset=utf-8 
					Content-Transfer-Encoding: 7bit 
					
					<h1>Foo</h1><p>Bar</p> 
	
					--boundary--'
				)
			),
			preg_replace(
				'~--[0-9a-z]*(\s|--)~', '',
				preg_replace(
					'/\s+/', ' ',
					(new Mail\HtmlMessage('<h1>Foo</h1><p>Bar</p>'))->content()
				)
			)
		);
	}
}

(new HtmlMessage())->run();
