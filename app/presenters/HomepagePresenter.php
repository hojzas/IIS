<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\FestivalModel;


final class HomepagePresenter extends BasePresenter
{

	/** @var FestivalModel */
	private $festivalModel;
		
	public function __construct(FestivalModel $festivalModel)
	{
		$this->festivalModel = $festivalModel;
	}

	/**
	 * Render homepage view.
	 */
    public function renderDefault(): void
	{
		$this->festivalModel->renderView($this);
	}
}
