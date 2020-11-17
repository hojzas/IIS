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
    public function renderView(): Nette\Database\Table\Selection
	{
		return $this->database->table('interpret')
			->order('nazev')
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
		
		// bands genres
		$bandGenres = array();
		foreach ($band->related('tagovani') as $genres) {
			$genre = $this->database->table('zanr')
				->where('zan_ID', $genres->zan_id)
				->fetch();
				$bandGenres[] = $genre->nazev;
			}
			
		// bands festivals
		$festivals = array();
		$festivals = $this->database->table('festival');
		$festivals->where(':stage:vystupuje.int_id.int_ID', $intID)
			->group('festival.fes_ID');

		$thisP->template->band = $band;
		$thisP->template->bandGenres = $bandGenres;
		$thisP->template->festivals = $festivals;
	}

}
