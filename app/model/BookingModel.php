<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class BookingModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Render booking detail.
	 */
    public function renderBook($thisP, $festivalID)
	{
		$festival = $this->database->table('festival')->get($festivalID);
		if (!$festival) {
			$thisP->error('Festival nenalezen');
		}

		$thisP->template->festival = $festival;
	}
	
	/**
	 * Book form factory.
	 */
	public function createBookForm($thisP)
	{
		$form = new Form;

		if (!$thisP->user->isLoggedIn()) {

			$form->addEmail('email', 'E-Mail: ')
				->setDefaultValue('@fest.cz')
				->setRequired('Prosím zadejte Váš email.')
				->setHtmlAttribute('placeholder', 'E-Mail')
				->addRule($form::MAX_LENGTH, 'Email je příliš dlouhý', 255)
				->setHtmlAttribute('class', 'form-control');

			$form->addText('phone', 'Telefon*: ')
				->setDefaultValue('123456789')
				->setRequired('Prosím zadejte Vaše telefoní číslo.')
				->setHtmlAttribute('placeholder', 'Telefon')
				->setHtmlAttribute('class', 'form-control')
				->addRule($form::PATTERN, 'špatný formát', '[0-9]{9}')
				->setHtmlType('tel');
		}

		for ($i = 1; $i <= 10; $i++) {
			$numbers[] = $i;
		}
			
		$form->addSelect('quantity', 'Počet vstupenek:', $numbers)
			->setDefaultValue(0);

		$form->addSubmit('send', 'Zarezervovat');

		// call method bookFestival() on success
		$form->onSuccess[] = [$thisP, 'bookFestival'];
		return $form;
	}

	/**
	 * Book festival.
	 */
	public function bookFestival($thisP, $values, $form)
	{
		$festivalID = $thisP->getParameter('festivalID');

		if (!$thisP->user->isLoggedIn()) {
			// not logged

			$roleID = $this->database->table('role')
				->where('nazev', 'unregister')
				->fetch();
			
			if (!$roleID) {
				$thisP->error('Rezervace se nezdařila');
			}

			// create unregistered account
			$account = $this->database->table('divak')
				->insert([
					'telefon' => $values->phone,
					'email' => $values->email,
					'rol_ID' => $roleID
				]);

			if (!$account) {
				$thisP->error('Registrace se nezdařila');
			}			
			
			// create reservation
			$row = $this->database->table('rezervace')
			->insert([
				'pocet_vstupenek' => $values->quantity + 1,
				'div_id' => $account->div_ID,
				'fes_id' => $festivalID
				]);
				
			if (!$row) {
				$thisP->error('Rezervace se nezdařila');
			}

			$thisP->flashMessage('Rezervace proběhla úspěšně, podrobné informace Vám zašleme na email.');
			$thisP->flashMessage('Nyní můžete dokončit registraci pro založení účtu.');
			$thisP->redirect('Registration:complete', $values->phone, $values->email, $account->div_ID);
			
		} else {
			// logged
			
			// create reservation
			$row = $this->database->table('rezervace')
			->insert([
				'pocet_vstupenek' => $values->quantity + 1,
				'div_id' => $thisP->user->getIdentity()->getId(),
				'fes_id' => $festivalID
				]);
				
			if (!$row) {
				$thisP->error('Rezervace se nezdařila');
			}

			$thisP->flashMessage('Rezervace proběhla úspěšně, podrobné informace Vám zašleme na email.');
			// TODO: redirect my reservation/detail
			$thisP->redirect('Homepage:');
		}
		
	}
}
