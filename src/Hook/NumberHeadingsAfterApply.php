<?php

namespace MediaWiki\Extension\NumberHeadings\Hook;

use MediaWiki\Title\Title;

interface NumberHeadingsAfterApply {

	/**
	 * @param Title $title
	 * @param string &$html
	 */
	public function onNumberHeadingsAfterApply( Title $title, string &$html );
}
