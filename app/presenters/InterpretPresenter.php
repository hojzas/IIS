<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Model\InterpretModel;


final class InterpretPresenter extends BasePresenter
{

    /** @var InterpretModel */
    private $interpretModel;
    
    public function __construct(InterpretModel $interpretModel)
    {
        $this->interpretModel = $interpretModel;
    }
	
	/**
	 * Render interpret view.
	 */
    public function renderView(int $page = 1): void
	{
		$bands = $this->interpretModel->renderView();

		// paginating
		$lastPage = 0;
		$this->template->bands = $bands->page($page, 9, $lastPage);
		$this->template->page = $page;
		$this->template->lastPage = $lastPage;
	}

	/**
	 * Render interpret detail.
	 */
	public function renderDetail(int $id): void
	{
		$this->interpretModel->renderDetail($this, $id);
	}

}
