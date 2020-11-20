<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;
use App\Model\CheckPasswordModel;

class FestivalManagerModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	/** @var CheckPasswordModel */
	private $checkPasswordModel;
	
	private $number = 0;

	public function __construct(Nette\Database\Context $database, CheckPasswordModel $checkPasswordModel)
	{
		$this->database = $database;
		$this->checkPasswordModel = $checkPasswordModel;
	}

	/**
	 * ---------------------------------------- INSERT FESTIVAL ----------------------------------------
	 */

	/**
	 * Insert festival form factory.
	 */
    public function createFestivalInsertForm($thisP)
	{
		$form = new Form;
        
		$form->addText('name', 'Název: ')
			->setDefaultValue('Festival')
            ->setRequired('Prosím zadejte název festivalu.')
			->setHtmlAttribute('placeholder', 'Název festivalu')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
            ->setHtmlAttribute('class', 'form-control');
            
		$form->addTextArea("description", 'Popis: ')
			->setDefaultValue('Popis festivalu.')
            ->setRequired('Prosím zadejte popis festivalu.')
			->setHtmlAttribute('placeholder', 'Popis festivalu')
			->addRule($form::MAX_LENGTH, 'Popis je příliš dlouhý', 200)
            ->setHtmlAttribute('class', 'form-control');
            
		$form->addText('date', 'Datum: ')
			->setRequired('Prosím zadejte datum festivalu.')
			->setHtmlAttribute('placeholder', 'Datum festivalu')
			->setHtmlAttribute('class', 'form-control')
			->setType('date');
		
		$form->addText('location', 'Lokace: ')
			->setDefaultValue('Brno')
            ->setRequired('Prosím zadejte lokaci festivalu.')
			->setHtmlAttribute('placeholder', 'Lokace festivalu')
			->addRule($form::MAX_LENGTH, 'Lokalita je příliš dlouhá', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('address', 'Adresa: ')
			->setDefaultValue('Festivalová 123')
            ->setRequired('Prosím zadejte adresu festivalu.')
			->setHtmlAttribute('placeholder', 'Adresa festivalu')
			->addRule($form::MAX_LENGTH, 'Adresa je příliš dlouhá', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addInteger('price', 'Cena: ')
			->setDefaultValue(550)
            ->setRequired('Prosím zadejte cenu festivalu.')
			->setHtmlAttribute('placeholder', 'Cena festivalu')
			->addRule($form::RANGE, 'Cena musí být v rozsahu mezi %d a %d.', [0, 100000])
			->setHtmlAttribute('class', 'form-control');
			
		$form->addInteger('capacity', 'Kapacita: ')
			->setDefaultValue(2000)
            ->setRequired('Prosím zadejte kapacitu festivalu.')
			->setHtmlAttribute('placeholder', 'Kapacita festivalu')
			->addRule($form::RANGE, 'Kapacita musí být v rozsahu mezi %d a %d.', [0, 99999999999])
			->setHtmlAttribute('class', 'form-control');
            
        $form->addPassword('password', 'Heslo pro potvrzení:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addSubmit('send', 'Vytvořit');
            
        // call method insertFestival() on success
        $form->onSuccess[] = [$thisP, 'insertFestival'];
        return $form;
	}

	/**
	 * Insert new festival.
	 */
    public function insertFestival($thisP, $values, $form)
	{
		// check passwords
		if (!$this->checkPasswordModel->checkPassword($values->password, $thisP)) {
			$form->addError('Nesprávné heslo');
		} else {
				
			// insert festival information
			$row = $this->database->table('festival')
			->insert([
				'nazev' => $values->name,
				'popis' => $values->description,
				'datum' => $values->date,
				'misto' => $values->location,
				'adresa' => $values->address,
				'cena' => $values->price,
				'kapacita' => $values->capacity,
				'prodane' => 0
			]);

			if (!$row) {
				$thisP->error('Operace se nezdařila');
			}

			// inserted festival ID
			$festivalID = $row->fes_ID;
				
			// complete
			$thisP->flashMessage("Nový festival $values->name vytvořen, nyní můžete vytvořit stage.");
			$thisP->redirect('Management:festivalEdit', $festivalID);
		}
	}

	/**
	 * ---------------------------------------- EDIT FESTIVAL ----------------------------------------
	 */

	/**
	 * Edit festival form factory.
	 */
    public function createFestivalEditForm($thisP)
	{
		$festivalID = $thisP->getParameter('festivalID');
			
		$festival = $this->database->table('festival')
			->where('fes_ID', $festivalID)
			->fetch();

		if (!$festival) {
			$thisP->error('Nepodařilo se načíst data z databáze');
		}
		
		$form = new Form;
			
		$form->addText('name', 'Název: ')
			->setDefaultValue($festival->nazev)
            ->setRequired('Prosím zadejte název festivalu.')
			->setHtmlAttribute('placeholder', 'Název festivalu')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
            ->setHtmlAttribute('class', 'form-control');
            
		$form->addTextArea("description", 'Popis: ')
			->setDefaultValue($festival->popis)
            ->setRequired('Prosím zadejte popis festivalu.')
			->setHtmlAttribute('placeholder', 'Popis festivalu')
			->addRule($form::MAX_LENGTH, 'Popis je příliš dlouhý', 200)
            ->setHtmlAttribute('class', 'form-control');
			
		$form->addText('date', 'Datum: ')
			->setType('date')
			->setDefaultValue($festival->datum->format('Y-m-d'))
			->setRequired('Prosím zadejte datum festivalu.')
			->setHtmlAttribute('placeholder', 'Datum festivalu')
			->setHtmlAttribute('class', 'form-control');
		
        $form->addText('location', 'Lokace: ')
			->setDefaultValue($festival->misto)
            ->setRequired('Prosím zadejte lokaci festivalu.')
			->setHtmlAttribute('placeholder', 'Lokace festivalu')
			->addRule($form::MAX_LENGTH, 'Lokalita je příliš dlouhá', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('address', 'Adresa: ')
			->setDefaultValue($festival->adresa)
            ->setRequired('Prosím zadejte adresu festivalu.')
			->setHtmlAttribute('placeholder', 'Adresa festivalu')
			->addRule($form::MAX_LENGTH, 'Adresa je příliš dlouhá', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addInteger('price', 'Cena: ')
			->setDefaultValue($festival->cena)
            ->setRequired('Prosím zadejte cenu festivalu.')
			->setHtmlAttribute('placeholder', 'Cena festivalu')
			->addRule($form::RANGE, 'Cena musí být v rozsahu mezi %d a %d.', [0, 100000])
			->setHtmlAttribute('class', 'form-control');
			
		// check minimal amount, it's in reservation
		$minAmount = $festival->prodane;
		
		$form->addInteger('capacity', 'Kapacita: ')
			->setDefaultValue($festival->kapacita)
            ->setRequired('Prosím zadejte kapacitu festivalu.')
			->setHtmlAttribute('placeholder', 'Kapacita festivalu')
			->addRule($form::RANGE, 'Kapacita musí být v rozsahu mezi %d a %d.', [$minAmount, 99999999999])
			->setHtmlAttribute('class', 'form-control');
            
        $form->addPassword('password', 'Heslo pro potvrzení:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
            ->setHtmlAttribute('class', 'form-control');
			
		$form->addSubmit('send', 'Uložit změny');

		$form->addSubmit('delete', 'Odstranit')
			->onClick[] = [$thisP, 'deleteFestival'];

		$form->onSuccess[] = [$thisP, 'updateFestival'];
		return $form;
	}

	/**
	 * Update edited festival info.
	 */
    public function updateFestival($thisP, $values, $form)
	{
		// check passwords
		if (!$this->checkPasswordModel->checkPassword($values->password, $thisP)) {
			$form->addError('Nesprávné heslo');
		} else {

			$festivalID = $thisP->getParameter('festivalID');
			
			// update festival information
			$count = $this->database->table('festival')
				->where('fes_ID', $festivalID)
				->update([
					'nazev' => $values->name,
					'popis' => $values->description,
					'datum' => $values->date,
					'misto' => $values->location,
					'adresa' => $values->address,
					'cena' => $values->price,
					'kapacita' => $values->capacity
				]);
				
			// complete
			$thisP->flashMessage('Změny uloženy.');
			$thisP->redirect('Festival:info', $festivalID);         
		}
	}

	/**
	 * Festival stage form factory.
	 */
    public function createStageForm($thisP)
	{
		$festivalID = $thisP->getParameter('festivalID');
			
		$festival = $this->database->table('festival')
			->where('fes_ID', $festivalID)
			->fetch();

		if (!$festival) {
			$thisP->error('Nepodařilo se načíst data z databáze');
		}

		// select all festivals stages, sorted by their IDs
		$festivalStages = array();
		foreach ($festival->related('stage') as $stage) {
			$festivalStages[$stage->stg_ID] = $stage->nazev;
		}

		$form = new Form;
			
		if ($festivalStages) {
			$form->addSelect('name', 'Název: ', $festivalStages)
				->setRequired('Prosím vyberte stage festivalu.')
				->setHtmlAttribute('placeholder', 'Stage festivalu')
				->setHtmlAttribute('class', 'form-control');
			
			$form->addSubmit('send', 'Upravit');
		} else {
			echo "<p>Festival nemá vytvořené žádné stage</p><br>";
		}

		$form->onSuccess[] = [$thisP, 'editStage'];
		return $form;
	}

	/**
	 * New festival stage form factory.
	 */
    public function createNewStageForm($thisP)
	{
		$form = new Form;

		$form->addInteger('stageNumber', 'Počet interpretů: ')
            ->setRequired('Prosím zadejte počet interpretů na stage.')
			->setHtmlAttribute('placeholder', 'Počet interpretů')
			->addRule($form::RANGE, 'Počet interpretů musí být v rozsahu mezi %d a %d.', [1, 50])
			->setHtmlAttribute('class', 'form-control');
		
		$form->addSubmit('send', 'Vytořit');

		$form->onSuccess[] = [$thisP, 'createStage'];
		return $form;
	}

	/**
	 * ---------------------------------------- DELETE FESTIVAL ----------------------------------------
	 */

	/**
	 * Delete festival.
	 */
    public function deleteFestival($thisP, $form)
	{
		$password = $form->getValues()->password;

        // check passwords
        if (!$this->checkPasswordModel->checkPassword($password, $thisP)) {
            $form->addError('Nesprávné heslo');
        } else {

            $festivalID = $thisP->getParameter('festivalID');

            // delete festival
            $count = $this->database->table('festival')
                ->where('fes_ID', $festivalID)
                ->delete();
			
            $thisP->flashMessage("Festival odstraněn.");
            $thisP->redirect('Festival:view');
        }
	}

	/**
     * ---------------------------------------- INSERT STAGE ----------------------------------------
	 */

	/**
	 * Create festival stage.
	 */
    public function createStage($thisP, $form)
	{
		$stageNumber = $form->getValues()->stageNumber;

		$festivalID = $thisP->getParameter('festivalID');		
		
		$thisP->redirect("Management:festivalStageInsert", $festivalID, $stageNumber);
	}
    
    /**
     * Insert new festival stage form factory.
     */
	public function createStageInsertForm($thisP)
	{		
		$stageNumber = $thisP->getParameter('stageNumber');

		// select all interprets, sorted by their IDs
		$allInterprets = array();
		$interprets = $this->database->table('interpret');
		foreach ($interprets as $interpret) {
			$allInterprets[$interpret->int_ID] = $interpret->nazev;
		}

		$form = new Form;

		$form->addText('name', 'Název: ')
            ->setRequired('Prosím zadejte název stage.')
			->setHtmlAttribute('placeholder', 'Název stage')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
			->setHtmlAttribute('class', 'form-control');
		
		// inputs for all interprets
		for ($i = 1; $i <= $stageNumber; $i++) {
			$form->addSelect("interpret_$i", "$i. vytupující: ", $allInterprets);

			$time = 15 + $i;
			$form->addText("time_$i",)
				->setDefaultValue("$time:00")
				->setRequired('Prosím zadejte čas vystoupení.')
				->setHtmlAttribute('placeholder', 'Čas vystoupení')
				->setHtmlAttribute('class', 'form-control')
				->setType('time');
		}

		$form->addSubmit('send', 'Vytvořit');

		$form->onSuccess[] = [$thisP, 'insertStage'];
		return $form;
	}

	/**
	 * Insert new festival stage.
	 */
    public function insertStage($thisP, $values, $form)
	{
		$festivalID = $thisP->getParameter('festivalID');
		$stageNumber = $thisP->getParameter('stageNumber');

		// insert festival stage
		$row = $this->database->table('stage')
		->insert([
			'nazev' => $values->name,
			'fes_id' => $festivalID
		]);
		
		if (!$row) {
			$thisP->error('Operace se nezdařila');
		}
		
		// inserted stage ID
		$stageID = $row->stg_ID;
		
		
		// insert stage interprets & time
		for ($i = 1; $i <= $stageNumber; $i++) {

			$selectedInterpret = "interpret_$i";
			$selectedTime = "time_$i";
			
			$row = $this->database->table('vystupuje')
				->insert([
					'stg_id' => $stageID,
					'int_id' => $values->$selectedInterpret,
					'cas' => $values->$selectedTime
				]);
		}
			
		// complete
		$thisP->flashMessage("Nová stage $values->name vytvořena.");
		$thisP->redirect('Management:festivalEdit', $festivalID);
	}

	/**
     * ---------------------------------------- EDIT STAGE ----------------------------------------
	 */

	/**
	 * Edit festival stage.
	 */
    public function editStage($thisP, $values)
	{
		$stageID = $values->name;

		$festivalID = $thisP->getParameter('festivalID');
		
		$thisP->redirect("Management:festivalStageEdit", $festivalID, $stageID);
	}

	/**
     * Edit festival stage form factory.
     */
	public function createStageEditForm($thisP)
	{		
		$stageID = $thisP->getParameter('stageID');

		// select stage
		$stage = $this->database->table('stage')
			->where('stg_ID', $stageID)
			->fetch();

		if (!$stage) {
			$thisP->error('Nepodařilo se načíst data z databáze');
		}

		// select all interprets, sorted by their IDs
		$allInterprets = array();
		$interprets = $this->database->table('interpret');
		foreach ($interprets as $interpret) {
			$allInterprets[$interpret->int_ID] = $interpret->nazev;
		}

		// select stage schedule
		$schedule = $this->database->table('vystupuje')
			->where('stg_ID', $stageID);

		$form = new Form;

		$form->addText('name', 'Název: ')
			->setDefaultValue($stage->nazev)
            ->setRequired('Prosím zadejte název stage.')
			->setHtmlAttribute('placeholder', 'Název stage')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
			->setHtmlAttribute('class', 'form-control');
		
		// inputs for all interprets
		for ($i = 1; $i <= count($schedule); $i++) {
			$form->addSelect("interpret_$i", "$i. vytupující: ", $allInterprets)
				->setDefaultValue($schedule[$i-1]->int_id);

			$form->addText("time_$i",)
				->setType('time')
				->setDefaultValue($schedule[$i-1]->cas->format("%H:%I"))
				->setRequired('Prosím zadejte čas vystoupení.')
				->setHtmlAttribute('placeholder', 'Čas vystoupení')
				->setHtmlAttribute('class', 'form-control');
				
			//echo $schedule[$i-1]->cas->format("%H:%i");
		}

		$form->addSubmit('delete', 'Odstranit')
			->onClick[] = [$thisP, 'deleteStage'];

		$form->addSubmit('send', 'Uložit změny');

		$form->onSuccess[] = [$thisP, 'updateStage'];
		return $form;
	}

	/**
	 * Update edited festival stage.
	 */
    public function updateStage($thisP, $values, $form)
	{
		$stageID = $thisP->getParameter('stageID');
		$festivalID = $thisP->getParameter('festivalID');

		// update festival stage
		$row = $this->database->table('stage')
			->where('stg_ID', $stageID)
			->update([
				'nazev' => $values->name
			]);

		// select previous stage schedule
		$co = count($this->database->table('vystupuje')
			->where('stg_ID', $stageID));

		$count = $this->database->table('vystupuje')
			->where('stg_id', $stageID)
			->delete();

		// update stage interprets & time
		for ($i = 1; $i <= $co; $i++) {

			$selectedInterpret = "interpret_$i";
			$selectedTime = "time_$i";

			$row = $this->database->table('vystupuje')
				->insert([
					'stg_id' => $stageID,
					'int_id' => $values->$selectedInterpret,
					'cas' => $values->$selectedTime
				]);
		}
			
		// complete
		$thisP->flashMessage('Změny uloženy.');
		$thisP->redirect('Management:festivalEdit', $festivalID);   
	}

	/**
	 * ---------------------------------------- DELETE STAGE ----------------------------------------
	 */

	 /**
	 * Delete stage.
	 */
    public function deleteStage($thisP, $form)
	{
		$stageID = $thisP->getParameter('stageID');
		$festivalID = $thisP->getParameter('festivalID');

		// delete stage references
		$count = $this->database->table('vystupuje')
			->where('stg_id', $stageID)
			->delete();

		// delete stage
		$count = $this->database->table('stage')
			->where('stg_ID', $stageID)
			->delete();
		
		$thisP->flashMessage("Stage odstraněna.");
		$thisP->redirect('Management:festivalEdit', $festivalID);
	}

}
