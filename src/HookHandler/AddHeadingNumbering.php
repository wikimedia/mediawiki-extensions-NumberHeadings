<?php

namespace MediaWiki\Extension\NumberHeadings\HookHandler;

use MediaWiki\Config\Config;
use MediaWiki\Content\Content;
use MediaWiki\Extension\NumberHeadings\ApplyHeadingNumbering;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Parser\ParserOutput;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\Title;

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
	 * @param Content $content
	 * @param Title $title
	 * @param ParserOutput &$output
	 * @return void
	 */
	public function onContentAlterParserOutput( Content $content, Title $title, ParserOutput &$output ) {
		if ( !$this->config->get( 'NumberHeadingsEnable' ) ) {
			return true;
		}
		$text = $output->getText();

		$applyHeadingNumbering = new ApplyHeadingNumbering(
			$this->config, $this->hookContainer, $this->namespaceInfo
		);

		$output->setText( $applyHeadingNumbering->apply( $title, $text ) );

		return true;
	}
}
