<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\FestivalModel;


final class FestivalPresenter extends BasePresenter
{
	/** @var FestivalModel */
	private $festivalModel;
		
	public function __construct(FestivalModel $festivalModel)
	{
		$this->festivalModel = $festivalModel;
	}

	/**
	 * Render festival view.
	 */
    public function renderView(int $page = 1): void
	{
		$this->festivalModel->renderView($this, $page);
	}

	/**
	 * Render festival detail.
	 */
	public function renderInfo(int $id): void
	{
		$this->festivalModel->renderFestivalInfo($this, $id);
	}

	/**
	 * Search.
	 */
    protected function createComponentSearchForm() {

		$form = new Form;
		$form->addText('value', '')
            ->setRequired(TRUE)
            ->setHtmlAttribute('placeholder', 'Vyhledat festival, interpreta, lokalitu...');
		$form->onSuccess[] = [$this, 'searchFormSucceeded'];
		return $form;
	}
		public function searchFormSucceeded($form, $values) {
		$tofind = $values->value;
		$rows = $this->database->table('nekrolog')->select('surname')->where('surname = ?', $tofind);
		foreach ($rows as $row) {
			echo $row->surname;
		}
	}
}
