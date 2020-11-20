<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\SearchModel;


final class SearchPresenter extends BasePresenter
{

	/** @var SearchModel */
    private $searchModel;
		
	public function __construct(SearchModel $searchModel)
	{
		$this->searchModel = $searchModel;
	}

	/**
	 * Search render.
	 */
    public function renderDefault($q, $type, int $page = 1, $tookPlace) {

		$this->template->q = $q;
		$this->template->tookPlace = $tookPlace;

		// filter
		$this->template->all = false;
		$this->template->byFestival = false;
		$this->template->byInterpret = false;

		if ($type == 'all') {
			$this->template->all = true;
		}
		if ($type == 'festivals') {
			$this->template->byFestival = true;
		}
		if ($type == 'interprets') {
			$this->template->byInterpret = true;
		}

		// search results
		$festivals = $this->searchModel->searchFestivals($q, $tookPlace);
		$interprets = $this->searchModel->searchInterprets($q);

		
		// festival paginating
		$lastPageFest = 0;
		$this->template->festivals = $festivals->page($page, 15, $lastPageFest);
		$this->template->pageFest = $page;
		$this->template->lastPageFest = $lastPageFest;

		// interpret paginating
		$lastPage = 0;
		$this->template->interprets = $interprets->page($page, 15, $lastPage);
		$this->template->page = $page;
		$this->template->lastPage = $lastPage;
    }

	/**
	 * ---------------------------------------- SEARCH ----------------------------------------
	 */

	/**
	 * Search form factory.
	 */
	protected function createComponentSearchForm() 
	{
		return $this->searchModel->createSearchForm($this);
	}

	/**
	 * Check box form factory.
	 */
	protected function createComponentCheckBoxForm() 
	{
		return $this->searchModel->createCheckBoxForm($this);
	}

	 /**
	 * Search.
	 */
    public function search(Form $form, \stdClass $values): void
    {
        $this->redirect('Search:', $values->q, 'all', 1, 0);
	}
	
	/**
	 * Filter.
	 */
    public function tookPlace(Form $form, \stdClass $values): void
    {
		$q = $this->getParameter('q');
        $this->redirect('Search:', $q, 'festivals', 1, $values->tookPlace);
	}

}
