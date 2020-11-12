<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class InterpretPresenter extends BasePresenter
{

    /** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
    public function renderView(int $page = 1): void
	{
        $this->template->page = $page;
		$this->template->bands = $this->database->table('interpret')
			->limit(9);
	}

	public function renderDetail(int $id): void
	{
		$band = $this->database->table('interpret')->get($id);
		if (!$band) {
			$this->error('band not found');
		}
		
		$bandGenres = array();
		foreach ($band->related('tagovani') as $genres) {
			$genre = $this->database->table('zanr')
				->where('zan_ID', $genres->zan_id)
				->fetch();
			$bandGenres[] = $genre->nazev;
		}

		$this->template->band = $band;
		$this->template->bandGenres = $bandGenres;
	}

}
