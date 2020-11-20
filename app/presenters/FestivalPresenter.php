<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\FestivalModel;
use App\Model\BookingModel;

final class FestivalPresenter extends BasePresenter
{
	/** @var FestivalModel */
	private $festivalModel;

	/** @var BookingModel */
    private $bookingModel;
		
	public function __construct(FestivalModel $festivalModel, BookingModel $bookingModel)
	{
		$this->festivalModel = $festivalModel;
		$this->bookingModel = $bookingModel;
	}

	/**
     * Get festival ID.
	 */
    public function renderBook(int $festivalID): void
	{
		$this->template->festivalID = $festivalID;
		$this->bookingModel->renderBook($this, $festivalID);
    }

	/**
	 * Render festival view.
	 */
    public function renderView(int $page = 1): void
	{
		$festivals = $this->festivalModel->findFestivals();

		// paginating
		$lastPage = 0;
		$this->template->festivals = $festivals->page($page, 3, $lastPage);
		$this->template->page = $page;
		$this->template->lastPage = $lastPage;
	}

	/**
	 * Render festival detail.
	 */
	public function renderInfo(int $id): void
	{
		$this->festivalModel->renderFestivalInfo($this, $id);
	}

	/**
	 * ---------------------------------------- BOOKING ----------------------------------------
	 */

	/**
	 * Book form factory.
	 */
	protected function createComponentBookForm(): Form
	{
        return $this->bookingModel->createBookForm($this);
	}
	
	/**
	 * Book festival.
	 */
    public function bookFestival(Form $form, \stdClass $values): void
	{
        $this->bookingModel->bookFestival($this, $values, $form);
    }
}
