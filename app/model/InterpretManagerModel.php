<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;
use App\Model\CheckPasswordModel;

class InterpretManagerModel
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
	 * ---------------------------------------- INSERT INTERPRET ----------------------------------------
	 */

	/**
	 * Insert interpret form factory.
	 */
    public function createInterpretInsertForm($thisP)
	{
		$form = new Form;
        
		$form->addText('name', 'Název: ')
			->setDefaultValue('Jméno interpreta')
            ->setRequired('Prosím zadejte název kapely.')
			->setHtmlAttribute('placeholder', 'Název kapely')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
            ->setHtmlAttribute('class', 'form-control');
            
		$form->addTextArea("members", 'Členové: ')
			->setDefaultValue("
Člen1 - zpěv, kytara
Člen2 - bicí
Člen3 - baskytara")
            ->setRequired('Prosím zadejte člena kapely.')
			->setHtmlAttribute('placeholder', 'Členové kapely')
			->addRule($form::MAX_LENGTH, 'Popis členů je příliš dlouhý', 200)
            ->setHtmlAttribute('class', 'form-control');
            
		$form->addText('logo', 'Logo kapely*: ')
			->setDefaultValue('default_band.jpeg')
            ->setRequired('Prosím zadejte název souboru loga kapely.')
            ->setHtmlAttribute('placeholder', 'Logo kapely')
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('facebook', '()Facebook kapely: ')
            ->setHtmlAttribute('placeholder', 'Facebook kapely')
            ->setHtmlAttribute('class', 'form-control');

        $allGenres = array();

        // select all genres
        $genres = $this->database->table('zanr');
        foreach ($genres as $genre) {
            $allGenres[] = $genre->nazev;
		}
		
		if (!$genres) {
			$thisP->error('Nepodařilo se načíst data z databáze');
		}

        $form->addMultiSelect('genres', 'Žánr:', $allGenres)
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addPassword('password', 'Heslo pro potvrzení:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addSubmit('send', 'Vytvořit');
            
        // call method insertInterpret() on success
        $form->onSuccess[] = [$thisP, 'insertInterpret'];
        return $form;
	}

	/**
	 * Insert new interpret.
	 */
    public function insertInterpret($thisP, $values, $form)
	{
		// check passwords
		if (!$this->checkPasswordModel->checkPassword($values->password, $thisP)) {
			$form->addError('Nesprávné heslo');
		} else {
				
			// insert band information
			$row = $this->database->table('interpret')
			->insert([
				'nazev' => $values->name,
				'logo' => $values->logo,
				'facebook' => $values->facebook,
				'clenove' => $values->members
			]);

			if (!$row) {
				$thisP->error('Operace se nezdařila');
			}

			// inserted band ID
			$bandID = $row->int_ID;

			// insert band genres
			foreach ($values->genres as $genre) {
				$row = $this->database->table('tagovani')
					->insert([
						'zan_id' => $genre + 1,
						'int_id' => $bandID
					]);
			}
				
			// complete
			$thisP->flashMessage("Nový interpret $values->name vytvořen.");
			$thisP->redirect('Interpret:detail', $bandID);
		}
	}

	/**
	 * ---------------------------------------- EDIT INTERPRET ----------------------------------------
	 */

	/**
	 * Edit interpret form factory.
	 */
    public function createInterpretEditForm($thisP)
	{
		$bandID = $thisP->getParameter('bandID');
			
		$band = $this->database->table('interpret')
			->where('int_ID', $bandID)
			->fetch();

		if (!$band) {
			$thisP->error('Nepodařilo se načíst data z databáze');
		}
		
		$form = new Form;
		
		$form->addText('name', 'Název: ')
			->setDefaultValue($band->nazev)
			->setRequired('Prosím zadejte název kapely.')
			->setHtmlAttribute('placeholder', 'Název kapely')
			->addRule($form::MAX_LENGTH, 'Název je příliš dlouhý', 50)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addTextArea("members", 'Členové: ')
			->setDefaultValue($band->clenove)
			->setRequired('Prosím zadejte člena kapely.')
			->setHtmlAttribute('placeholder', 'Členové kapely')
			->addRule($form::MAX_LENGTH, 'Popis členů je příliš dlouhý', 200)
			->setHtmlAttribute('class', 'form-control');            
			
		$form->addText('logo', 'Logo kapely*: ')
			->setDefaultValue($band->logo)
			->setRequired('Prosím zadejte název souboru loga kapely.')
			->setHtmlAttribute('placeholder', 'Logo kapely')
			->setHtmlAttribute('class', 'form-control');

		$form->addText('facebook', '()Facebook kapely: ')
			->setDefaultValue($band->facebook)
            ->setHtmlAttribute('placeholder', 'Facebook kapely')
            ->setHtmlAttribute('class', 'form-control');

		$allGenres = $bandGenres = array();
			
		// select all genres
		$genres = $this->database->table('zanr');
		if (!$genres) {
			$thisP->error('Nepodařilo se načíst data z databáze');
		}
		foreach ($genres as $genre) {
			$allGenres[] = $genre->nazev;
		}


		// select band genres
		foreach ($band->related('tagovani') as $genres) {
			$genre = $this->database->table('zanr')
				->where('zan_ID', $genres->zan_id)
				->fetch();
			$bandGenres[] = $genre->zan_ID-1;
		}

		$form->addMultiSelect('genres', 'Žánr:', $allGenres)
			->setHtmlAttribute('class', 'form-control');
		$form['genres']->setDefaultValue($bandGenres); 
			
		$form->addPassword('password', 'Heslo pro potvrzení:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
			->setHtmlAttribute('class', 'form-control');
			
		$form->addSubmit('send', 'Uložit změny');

		$styles = ['r' => 'background:red'];
		$form->addSubmit('delete', 'Odstranit')
			->onClick[] = [$thisP, 'deleteInterpret'];

		$form->onSuccess[] = [$thisP, 'updateInterpret'];
		return $form;
	}

	/**
	 * Update edited interpret info.
	 */
    public function updateInterpret($thisP, $values, $form)
	{
		// check passwords
		if (!$this->checkPasswordModel->checkPassword($values->password, $thisP)) {
			$form->addError('Nesprávné heslo');
		} else {

			$bandID = $thisP->getParameter('bandID');
			
			// update band information
			$count = $this->database->table('interpret')
				->where('int_ID', $bandID)
				->update([
					'nazev' => $values->name,
					'logo' => $values->logo,
					'facebook' => $values->facebook,
					'clenove' => $values->members,
				]);
			
			// update band genres
			$count = $this->database->table('tagovani')
				->where('int_id', $bandID)
				->delete();

			foreach ($values->genres as $genre) {
				$row = $this->database->table('tagovani')
					->insert([
						'zan_id' => $genre + 1,
						'int_id' => $bandID
					]);
			}
				
			// complete
			$thisP->flashMessage('Změny uloženy.');
			$thisP->redirect('Interpret:detail', $bandID);         
		}
	}

	/**
	 * ---------------------------------------- DELETE INTERPRET ----------------------------------------
	 */

	 /**
	 * Delete interpret.
	 */
    public function deleteInterpret($thisP, $form)
	{
		$password = $form->getValues()->password;

        // check passwords
        if (!$this->checkPasswordModel->checkPassword($password, $thisP)) {
            $form->addError('Nesprávné heslo');
        } else {

            $bandID = $thisP->getParameter('bandID');

            // delete bands genres
            $count = $this->database->table('tagovani')
                ->where('int_id', $bandID)
				->delete();

            // delete band
            $count = $this->database->table('interpret')
                ->where('int_ID', $bandID)
                ->delete();
			
            $thisP->flashMessage("Interpret odstraněn.");
            $thisP->redirect('Interpret:view');
        }
	}

}
