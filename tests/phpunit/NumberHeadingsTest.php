<?php

namespace MediaWiki\Extension\NumberHeadings\Tests;

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

	/**
	 * @dataProvider provideHeadingMarkup
	 * @param string $input
	 * @param string $expected
	 * @param string $prefix
	 */
	public function testHeadingMarkup( string $input, string $expected, string $prefix = '' ) {
		$headingNumbering = new NumberHeadings();
		$this->assertSame( $expected, $headingNumbering->execute( $input, $prefix ) );
	}

	public static function provideHeadingMarkup(): array {
		$number = '<span class="mw-headline-number">1 </span>';
		$editSection = '<span class="mw-editsection"><span class="mw-editsection-bracket">[</span>'
			. '<a href="/w/index.php?title=Headings&amp;veaction=edit&amp;section=1"'
			. ' title="Edit section: Heading" class="mw-editsection-visualeditor">Edit</a>'
			. '<span class="mw-editsection-divider"> | </span>'
			. '<a href="/w/index.php?title=Headings&amp;section=1&amp;veaction=editsource">Edit source</a>'
			. '<span class="mw-editsection-bracket">]</span></span>';

		return [
			'modern wrapper and edit links' => [
				'<div class="mw-heading mw-heading2"><h2 id="Heading">Heading</h2>' . $editSection . '</div>',
				'<div class="mw-heading mw-heading2"><h2 id="Heading">' . $number
					. 'Heading</h2>' . $editSection . '</div>',
			],
			'modern attributes and inline markup' => [
				'<h3 class="custom" id="Heading_(test)" lang="en"><span id="alias"></span>'
					. "<em>Heading</em>\nwith <span>markup</span></h3>",
				'<h3 class="custom" id="Heading_(test)" lang="en">' . $number
					. '<span id="alias"></span><em>Heading</em>'
					. "\nwith <span>markup</span></h3>",
			],
			'mixed formats and prefix' => [
				'<h2><span class="mw-headline" id="Old">Old</span></h2>'
					. '<div class="mw-heading mw-heading3"><h3 id="New">New</h3></div>'
					. '<h2 id="Next">Next</h2>',
				'<h2><span class="mw-headline" id="Old">A.' . $number . 'Old</span></h2>'
					. '<div class="mw-heading mw-heading3"><h3 id="New">'
					. 'A.<span class="mw-headline-number">1.1 </span>New</h3></div>'
					. '<h2 id="Next">A.<span class="mw-headline-number">2 </span>Next</h2>',
				'A.',
			],
			'literal replacement characters' => [
				'<h2 id="Price">$1 and \1</h2>',
				'<h2 id="Price">$2' . $number . '$1 and \1</h2>',
				'$2',
			],
			'duplicate headings' => [
				'<h2 id="Heading">Heading</h2><h2 id="Heading">Heading</h2>',
				'<h2 id="Heading">' . $number . 'Heading</h2>'
					. '<h2 id="Heading"><span class="mw-headline-number">2 </span>Heading</h2>',
			],
			'plain headings are not numbered or consumed' => [
				'<h2>Plain</h2><h2><span class="mw-headline" id="Old">Old</span></h2>'
					. '<h3 data-id="Not_an_id">Plain</h3>',
				'<h2>Plain</h2><h2><span class="mw-headline" id="Old">' . $number . 'Old</span></h2>'
					. '<h3 data-id="Not_an_id">Plain</h3>',
			],
		];
	}
}
