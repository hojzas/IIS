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
    public function renderView($thisP)
	{
		$thisP->template->festivals = $this->database->table('festival')
			->where('datum > ?', new \DateTime)
			->order('datum')
			->limit(6);
	}

	/**
	 * Render festival detail.
	 */
    public function findFestivals(): Nette\Database\Table\Selection
	{
		return $this->database->table('festival')
				->where('datum > ?', new \DateTime)
				->order('datum');
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
			$stageInterprets = $this->database->table('vystupuje')
				->where('stg_id', $festStage->stg_ID)
				->order('cas');

			foreach ($stageInterprets as $stageInterpret) {
				
				$interprets[] = $stageInterpret->int_id;
				$interprets[] = $stageInterpret->cas->format('%H:%I');
				$interprets[] = $this->database->table('interpret')
					->where('int_ID', $stageInterpret->int_id)
					->fetch()->nazev;
			}
			$stages[] = $interprets;
			unset($interprets); 

		}

		$thisP->template->festival = $festival;
		$thisP->template->stages = $stages;
	}

}
