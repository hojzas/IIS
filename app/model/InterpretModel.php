<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class InterpretModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
    /**
	 * Render interpret view.
	 */
    public function renderView($thisP, $page)
	{
		$thisP->template->page = $page;
		$thisP->template->bands = $this->database->table('interpret')
			->limit(9);
	}

	/**
	 * Render interpret detail.
	 */
    public function renderDetail($thisP, $intID)
	{
		$band = $this->database->table('interpret')->get($intID);
		if (!$band) {
			$thisP->error('Interpret nenalezen');
		}
		
		$bandGenres = array();
		foreach ($band->related('tagovani') as $genres) {
			$genre = $this->database->table('zanr')
				->where('zan_ID', $genres->zan_id)
				->fetch();
			$bandGenres[] = $genre->nazev;
		}

		$thisP->template->band = $band;
		$thisP->template->bandGenres = $bandGenres;
	}

}
