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
			preg_replace('~--[0-9a-z]*(\s|--)~', '',
				preg_replace('/\s+/', ' ',
					'--boundary
					Content-Type: text/plain; charset=utf-8 
					Content-Transfer-Encoding: 7bit 
				
					content 
				
					--boundary 
					Content-Type: application/octet-stream; name="attachment.txt" 
					Content-Transfer-Encoding: base64 
					Content-Disposition: attachment; filename="attachment.txt" 
				
					dGVzdGluZyBjb250ZW50 
				
					--boundary--'
				)
			),
			preg_replace('~--[0-9a-z]*(\s|--)~', '',
				preg_replace('/\s+/', ' ',
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
			preg_replace('~--[0-9a-z]*(\s|--)~', '',
				preg_replace('~"[0-9a-z]*(\s|")~', '',
					preg_replace('/\s+/', ' ',
						'--boundary1
						Content-Type: multipart/alternative; boundary="boundary2"
						
						--boundary2
						Content-Type: text/plain; charset=utf-8
						Content-Transfer-Encoding: 7bit
						
						\ntitle\n\ncontent\n
						
						--boundary2
						Content-Type: text/html; charset=utf-8
						Content-Transfer-Encoding: 7bit
						
						<h1>title</h1><p>content</p>
						
						--boundary2--
						
						--boundary1
						Content-Type: application/octet-stream; name="attachment.txt"
						Content-Transfer-Encoding: base64
						Content-Disposition: attachment; filename="attachment.txt"
						
						dGVzdGluZyBjb250ZW50
						
						--boundary1--'
					)
				)
			),
			preg_replace('~--[0-9a-z]*(\s|--)~', '',
				preg_replace('~"[0-9a-z]*(\s|")~', '',
					preg_replace('~\s+~', ' ',
						(new Mail\MessageWithAttachment(
							new Mail\HtmlMessage('<h1>title</h1><p>content</p>'),
							__DIR__ . '/../TestCase/MessageWithAttachment/attachment.txt'
						))->content()
					)
				)
			)
		);
	}

}

(new MessageWithAttachment())->run();