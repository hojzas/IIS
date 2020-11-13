<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;
use App\Model\CheckPasswordModel;

class FesticalManagerModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	/** @var CheckPasswordModel */
    private $checkPasswordModel;

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
            ->setRequired('Prosím zadejte název festivalu.')
			->setHtmlAttribute('placeholder', 'Název festivalu')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addTextArea("description", 'Popis: ')
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
            ->setRequired('Prosím zadejte lokaci festivalu.')
			->setHtmlAttribute('placeholder', 'Lokace festivalu')
			->addRule($form::MAX_LENGTH, 'Lokalita je příliš dlouhá', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('address', 'Adresa: ')
            ->setRequired('Prosím zadejte adresu festivalu.')
			->setHtmlAttribute('placeholder', 'Adresa festivalu')
			->addRule($form::MAX_LENGTH, 'Adresa je příliš dlouhá', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addInteger('price', 'Cena: ')
            ->setRequired('Prosím zadejte cenu festivalu.')
			->setHtmlAttribute('placeholder', 'Cena festivalu')
			->addRule($form::RANGE, 'Cena musí být v rozsahu mezi %d a %d.', [0, 100000])
			->setHtmlAttribute('class', 'form-control');
			
		$form->addInteger('capacity', 'Kapacita: ')
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
				'kapacita' => $values->capacity
			]);

			if (!$row) {
				$thisP->error('Operace se nezdařila');
			}

			// inserted festival ID
			$festivalID = $row->fes_ID;
				
			// complete
			$thisP->flashMessage("Nový festival $values->name vytvořen, nyní můžete vytvořit stage.");
			$thisP->redirect('Festival:info', $festivalID);
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
			
		$form->addInteger('capacity', 'Kapacita: ')
			->setDefaultValue($festival->kapacita)
            ->setRequired('Prosím zadejte kapacitu festivalu.')
			->setHtmlAttribute('placeholder', 'Kapacita festivalu')
			->addRule($form::RANGE, 'Kapacita musí být v rozsahu mezi %d a %d.', [0, 99999999999])
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

            // delete festivals stages
           

            // delete festival
            $count = $this->database->table('festival')
                ->where('fes_ID', $festivalID)
                ->delete();
			
            $thisP->flashMessage("Festival odstraněn.");
            $thisP->redirect('Festival:view');
        }
	}

}
