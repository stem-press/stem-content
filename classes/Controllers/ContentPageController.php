<?php
namespace Stem\Content\Controllers;

use Stem\Content\Models\ContentBlock;
use Stem\Controllers\PageController;
use Stem\Core\Context;
use Stem\Core\Response;
use Stem\Content\Traits\HasContent;
use Stem\Content\Traits\HasContentInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContentPageController
 *
 * Controller for pages that use content blocks.
 *
 * @package Stem\Content\Controllers
 */
class ContentPageController extends PageController implements HasContentInterface {
	use HasContent;

	protected $targetPagePath;
	protected $defaultViewParameters;

	public function __construct(Context $context, $template=null) {
		if ($template == null) {
			$template = 'templates/content-page';
		}

		parent::__construct($context, $template);

		if (!$this->page && $this->targetPagePath) {
			$pagePost = get_page_by_path($this->targetPagePath);

			if ($pagePost) {
				$this->page = $context->modelForPost($pagePost);
				if (empty($this->title)) {
					$this->title = $this->page->title;
				}
			}
		}

		if ($this->page) {
			$this->buildContent($context, $this->page);
		}
	}

	protected function addIndexData($data) {
		return $data;
	}

	public function getIndex(Request $request) {
		$data = $this->addIndexData([
			'errors' => [],
			'params' => $request->request,
			'page' => $this
		]);

		return $this->renderContent($request->query->get('partial'), $request->query->get('partial-target'), $this->template, $data);
	}

}