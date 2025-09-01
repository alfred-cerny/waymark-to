<?php

declare(strict_types=1);

namespace WaymarkTo\UI\Front\Home;

use Nette;
use WaymarkTo\Model\DTO\Signpost;
use WaymarkTo\Model\Repository\SignpostRepository;


final class HomePresenter extends Nette\Application\UI\Presenter {
	public function __construct(
		protected readonly SignpostRepository $signpostRepository,
		private Nette\Database\Explorer       $database
	) {
		parent::__construct();
	}

	public function renderDefault(): void {
		// Initialize shortUrl as empty to avoid undefined variable warning
		$this->template->shortUrl = null;
	}

	protected function createComponentShortenForm(): ShortenForm {
		$form = new ShortenForm();

		$form->addText('url', 'URL:')
			->setRequired('Please enter a URL.');

		$form->addText('alias', 'Alias (optional):');
		$form->addDateTime('expiration', 'Expiration (optional):')
			->setDefaultValue(new Nette\Utils\DateTime('+7 days'))/*->setRequired('Expiration is required.')*/
		;

		$form->addSubmit('shorten', 'Shorten');

		$form->onSuccess[] = [$this, 'shortenFormSucceeded'];

		return $form;
	}

	public function shortenFormSucceeded($form, $values): void {
		$url = $values['url'];
		$expiration = $values['expiration'];
		$signpost = null;

		if ($alias = $values['alias']) {
			$signpost = $this->signpostRepository->findByShortCode($alias);
		}

		if (is_null($signpost)) {
			$this->database->beginTransaction();
			$signpost = Signpost::new($url);
			$this->signpostRepository->save($signpost);
			$this->database->commit();
		}

		$this->flashMessage('You have successfully shortened the url. ' . json_encode((array)$signpost));
	}

}
