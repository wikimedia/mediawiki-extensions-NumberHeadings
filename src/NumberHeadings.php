<?php

namespace MediaWiki\Extension\NumberHeadings;

class NumberHeadings {

	/** @var array */
	private $headingCounter = [ 0, 0, 0, 0, 0, 0, 0 ];

	/** @var int */
	private $curHighestLevel = 6;

	/**
	 * @param string $html
	 * @param string $prefix
	 * @return string
	 */
	public function execute( string $html, string $prefix = '' ): string {
		$headings = $this->buildHeadingMap( $html );
		$newHtml = $this->processHeadings( $headings, $html, $prefix );

		return $newHtml;
	}

	/**
	 * @param string $html
	 * @return array
	 */
	private function buildHeadingMap( string $html ): array {
		// Branch reset keeps the captures identical for legacy and modern headings.
		$regEx = '#(?|'
			. '(<h([1-6])>(?:(?!</h\2>).)*?<span class="mw-headline" id="[^"]*">)'
			. '(.*?)(</span>.*?</h\2>)'
			. '|(<h([1-6])\b[^>]*\sid="[^"]*"[^>]*>)(.*?)(</h\2>)'
			. ')#s';

		$headings = [];
		$matches = [];
		$status = preg_match_all( $regEx, $html, $matches );
		if ( !$status ) {
			return $headings;
		}

		for ( $index = 0; $index < count( $matches[0] ); $index++ ) {
			$headings[] = [
				'search' => $matches[0][$index],
				'level' => (int)$matches[2][$index],
				'pre' => $matches[1][$index],
				'text' => $matches[3][$index],
				'post' => $matches[4][$index],
			];
		}

		return $headings;
	}

	/**
	 * @param array $headings
	 * @param string $html
	 * @param string $prefix
	 * @return string
	 */
	private function processHeadings( array $headings, string $html, string $prefix ): string {
		for ( $index = 0; $index < count( $headings ); $index++ ) {
			$level = $headings[$index]['level'];

			if ( $this->curHighestLevel > $level ) {
				$this->curHighestLevel = $level;
			}

			$level = $level - $this->curHighestLevel;

			$this->increaseHeadingCounter( $level );
			$this->resetHeadingCounter( $level + 1 );
			$numbering = $this->getHeadingNumbering( $prefix );

			$html = preg_replace_callback(
				$this->getReplacementRegEx( $headings[$index] ),
				fn () => $this->getReplacementHtml( $numbering, $headings[$index] ),
				$html,
				1
			);
		}

		return $html;
	}

	/**
	 * @param array $item
	 * @return string
	 */
	private function getReplacementRegEx( array $item ): string {
		return '#' . preg_quote( $item['search'], '#' ) . '#';
	}

	/**
	 * @param string $numbering
	 * @param array $item
	 * @return string
	 */
	private function getReplacementHtml( string $numbering, array $item ): string {
		$text = $item['text'];
		$pre = $item['pre'];
		$post = $item['post'];

		return $pre . $numbering . $text . $post;
	}

	/**
	 * @param int $level
	 * @return void
	 */
	private function increaseHeadingCounter( int $level ): void {
		$this->headingCounter[$level]++;
	}

	/**
	 * @param int $level
	 * @return void
	 */
	private function resetHeadingCounter( int $level ): void {
		for ( $index = $level; $index <= 6; $index++ ) {
			$this->headingCounter[$index] = 0;
		}
	}

	/**
	 * @param string $prefix
	 * @return string
	 */
	private function getHeadingNumbering( string $prefix ): string {
		$counter = $this->clearHeadingCounter();
		$numbering = implode( '.', $counter );

		$html = $prefix . '<span class="mw-headline-number">' . $numbering . ' </span>';

		return $html;
	}

	/**
	 * @return array
	 */
	private function clearHeadingCounter(): array {
		$counter = $this->headingCounter;
		for ( $index = 6; $index >= 0; $index-- ) {
			if ( $counter[$index] === 0 ) {
				unset( $counter[$index] );
			}
		}

		return $counter;
	}

}
