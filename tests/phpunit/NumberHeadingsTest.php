<?php

namespace MediaWiki\Extension\NumberHeadings\tests\phpunit;

use MediaWiki\Extension\NumberHeadings\NumberHeadings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\NumberHeadings\NumberHeadings
 */
class NumberHeadingsTest extends TestCase {

	/**
	 * @covers HeadingNumberation::execute
	 */
	public function testExecute() {
		$input = file_get_contents( __DIR__ . '/data/heading_numbering_input.html' );

		$expected = file_get_contents( __DIR__ . '/data/heading_numbering_output.html' );
		$headingNumbering = new NumberHeadings();
		$actual = $headingNumbering->execute( $input );
		$this->assertEquals( $expected, $actual );

		$expected = file_get_contents( __DIR__ . '/data/heading_numbering_with_prefix_output.html' );
		$headingNumbering = new NumberHeadings();
		$actual = $headingNumbering->execute( $input, 'A.' );
		$this->assertEquals( $expected, $actual );
	}
}
