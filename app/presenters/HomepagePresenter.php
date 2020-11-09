<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class HomepagePresenter extends BasePresenter
{
	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	public function renderDefault(int $page = 1): void
	{
		$this->template->page = $page;
		$this->template->clients = $this->database->table('divak')
			->order('div_ID DESC')
			->page($page, 5);
	}
}
