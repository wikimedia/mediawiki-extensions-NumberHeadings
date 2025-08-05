<?php

namespace MediaWiki\Extension\NumberHeadings\HookHandler;

use MediaWiki\Config\Config;
use MediaWiki\Extension\NumberHeadings\ApplyHeadingNumbering;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Output\OutputPage;
use MediaWiki\Title\NamespaceInfo;

class AddHeadingNumbering {

	/**
	 * @param Config $config
	 * @param HookContainer $hookContainer
	 * @param NamespaceInfo $namespaceInfo
	 */
	public function __construct(
		private readonly Config $config,
		private readonly HookContainer $hookContainer,
		private readonly NamespaceInfo $namespaceInfo
	) {
	}

	/**
	 * @param OutputPage $out
	 * @param string &$text
	 * @return bool
	 */
	public function onOutputPageBeforeHTML( OutputPage $out, &$text ): bool {
		if ( !$this->config->get( 'NumberHeadingsEnable' ) ) {
			return true;
		}
		$applyHeadingNumbering = new ApplyHeadingNumbering(
			$this->config, $this->hookContainer, $this->namespaceInfo
		);
		$text = $applyHeadingNumbering->apply( $out->getTitle(), $text );

		return true;
	}
}
