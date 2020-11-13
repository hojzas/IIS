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
		$this->interpretModel->renderView($this, $page);
	}

	/**
	 * Render interpret detail.
	 */
	public function renderDetail(int $id): void
	{
		$this->interpretModel->renderDetail($this, $id);
	}

}
