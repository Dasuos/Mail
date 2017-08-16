<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class MessageWithAttachment extends \Tester\TestCase {

	public function testReturningMixedContentType() {
		$path = __DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt';
		Assert::equal(
			sprintf(
				'Content-Type: multipart/mixed; boundary="%s"',
				md5($path)
			), (new Mail\MessageWithAttachment(
				new Mail\FakeMessage(
					'content',
					'headers'
				), $path))->headers()
		);
	}

	public function testReturningContentWithAttachment() {
		$path = __DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt';
		Assert::same(
			preg_replace('/\s+/', ' ',
				'--9fd357b3508c77710bf76d322a72fe1c
				Content-Type: text/plain; charset=utf-8 
				Content-Transfer-Encoding: 7bit 
			
				content 
			
				--9fd357b3508c77710bf76d322a72fe1c 
				Content-Type: application/octet-stream; name="attachment.txt" 
				Content-Transfer-Encoding: base64 
				Content-Disposition: attachment; filename="attachment.txt" 
			
				dGVzdGluZyBjb250ZW50 
			
				--9fd357b3508c77710bf76d322a72fe1c--'
			),
			preg_replace('/\s+/', ' ',
				(new Mail\MessageWithAttachment(
					new Mail\FakeMessage(
						'content',
						'Content-Type: text/plain; charset=utf-8' . PHP_EOL .
						'Content-Transfer-Encoding: 7bit'
					), $path
				))->content()
			)
		);
	}
}

(new MessageWithAttachment())->run();