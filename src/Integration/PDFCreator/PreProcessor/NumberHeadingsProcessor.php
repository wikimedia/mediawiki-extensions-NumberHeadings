<?php

namespace MediaWiki\Extension\NumberHeadings\Integration\PDFCreator\PreProcessor;

use MediaWiki\Config\Config;
use MediaWiki\Extension\NumberHeadings\ApplyHeadingNumbering;
use MediaWiki\Extension\PDFCreator\IPreProcessor;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Extension\PDFCreator\Utility\ExportPage;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Title\NamespaceInfo;
use MediaWiki\Title\Title;

class NumberHeadingsProcessor implements IPreProcessor {

	/** @var ApplyHeadingNumbering */
	private ApplyHeadingNumbering $applyHeadingNumbering;

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
		$this->applyHeadingNumbering = new ApplyHeadingNumbering(
			$this->config, $this->hookContainer, $this->namespaceInfo
		);
	}

	/**
	 * @param ExportPage[] &$pages
	 * @param array &$images
	 * @param array &$attachments
	 * @param ExportContext $context
	 * @param string $module
	 * @param array $params
	 *
	 * @return void
	 */
	public function execute(
		array &$pages,
		array &$images,
		array &$attachments,
		ExportContext $context,
		string $module = '',
		$params = []
	): void {
		if ( !$this->config->get( 'NumberHeadingsEnable' ) ) {
			return;
		}

		foreach ( $pages as &$page ) {
			$dbKey = $page->getPrefixedDBKey();
			if ( !$dbKey ) {
				continue;
			}

			$title = Title::newFromDBkey( $dbKey );
			$dom = $page->getDOMDocument();
			$html = $dom->saveHTML();
			$html = $this->applyHeadingNumbering->apply( $title, $html );
			$dom->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		}
	}
}
