<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class FestivalModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
    /**
	 * Render festival detail.
	 */
    public function renderView($thisP, $page)
	{
		$thisP->template->page = $page;
		$thisP->template->festivals = $this->database->table('festival')
			->order('datum')
			->limit(15);
	}

	/**
	 * Render festival detail.
	 */
    public function renderFestivalInfo($thisP, $fesID)
	{
		$festival = $this->database->table('festival')->get($fesID);
		if (!$festival) {
			$thisP->error('Festival nenalezen');
		}
		
		/*
		$bandGenres = array();
		foreach ($festival->related('tagovani') as $genres) {
			$genre = $this->database->table('zanr')
				->where('zan_ID', $genres->zan_id)
				->fetch();
			$bandGenres[] = $genre->nazev;
		}
		*/

		$thisP->template->festival = $festival;
		//$thisP->template->bandGenres = $bandGenres;
	}

}
