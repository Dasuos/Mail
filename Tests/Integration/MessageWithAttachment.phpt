<?php
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Dasuos\Tests;

use Tester\Assert;
use Dasuos\Mail;

require __DIR__ . '/../bootstrap.php';

class MessageWithAttachment extends \Tester\TestCase {

	public function testReturningPlainTextWithAttachment() {
		Assert::same(
			preg_replace('~\s+~', ' ',
				'--boundary
				Content-Type: text/plain; charset=utf-8 
				Content-Transfer-Encoding: 7bit 
				
				content 
				
				--boundary 
				Content-Type: application/octet-stream; name="attachment.txt" 
				Content-Transfer-Encoding: base64 
				Content-Disposition: attachment; filename="attachment.txt" 
				
				Zm9yIHRlc3RpbmcgcHVycG9zZQ== 
				
				--boundary--'
			),
			preg_replace('~[0-9a-z]{20}~', 'boundary',
				preg_replace('~\s+~', ' ',
					(new Mail\MessageWithAttachment(
						new Mail\PlainMessage('content'),
						__DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt'
					))->content()
				)
			)
		);
	}

	public function testReturningHtmlWithAttachment() {
		Assert::same(
			preg_replace('~\s+~', ' ',
				'--boundary
				Content-Type: multipart/alternative; boundary="boundary"
				
				--boundary
				Content-Type: text/plain; charset=utf-8
				Content-Transfer-Encoding: 7bit
				
				\ntitle\n\ncontent\n
				
				--boundary
				Content-Type: text/html; charset=utf-8
				Content-Transfer-Encoding: 7bit
				
				<h1>title</h1><p>content</p>
				
				--boundary--
				
				--boundary
				Content-Type: application/octet-stream; name="attachment.txt"
				Content-Transfer-Encoding: base64
				Content-Disposition: attachment; filename="attachment.txt"
				
				Zm9yIHRlc3RpbmcgcHVycG9zZQ==
						
				--boundary--'
			),
			preg_replace('~[0-9a-z]{20}~', 'boundary',
				preg_replace('~\s+~', ' ',
					(new Mail\MessageWithAttachment(
						new Mail\HtmlMessage('<h1>title</h1><p>content</p>'),
						__DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt'
					))->content()
				)
			)
		);
	}
}

(new MessageWithAttachment())->run();