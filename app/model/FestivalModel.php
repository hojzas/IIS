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
		
		$stages = $interprets = array();

		// get festival stages
		$festStages = $this->database->table('stage')
			->where('fes_id', $fesID);

		foreach ($festStages as $festStage) {

			$interprets[] = $festStage->nazev;

			// get stage interprets
			$stageInterprets = $this->database->table('vystupuje')->where('stg_id', $festStage->stg_ID);
			foreach ($stageInterprets as $stageInterpret) {
				
				$interprets[] = $stageInterpret->cas->format('%H:%I');
				$interprets[] = strval($stageInterpret->int_id);
			}
			$stages[] = $interprets;
			unset($interprets); 

		}

		
		
		

		$thisP->template->festival = $festival;
		$thisP->template->stages = $stages;
	}

}
