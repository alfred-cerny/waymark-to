<?php

declare(strict_types=1);

namespace WaymarkTo\UI\Front\Home;

use Nette;


final class HomePresenter extends Nette\Application\UI\Presenter {
	public function renderDefault(): void {
		// Initialize shortUrl as empty to avoid undefined variable warning
		$this->template->shortUrl = null;
	}

	protected function createComponentShortenForm(): ShortenForm {
		$form = new ShortenForm();

		$form->addText('url', 'URL:')
			->setRequired('Please enter a URL.');

		$form->addText('alias', 'Alias (optional):');

		$form->addSubmit('shorten', 'Shorten');

		$form->onSuccess[] = function (ShortenForm $form, array $values) {
			//todo: fetch the signpost
			$this->template->shortUrl = 'test';
		};

		$form->onSuccess[] = [$this, 'shortenFormSucceeded'];

		return $form;
	}

	public function shortenFormSucceeded($form, $values) {}

}
