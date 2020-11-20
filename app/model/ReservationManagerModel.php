<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class ReservationManagerModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * ---------------------------------------- RESERVATIONS ----------------------------------------
	 */

	/**
     * Search reservation form factory.
     */
	public function createSearchReservationsForm($thisP)
	{
		$reservationID = $thisP->getParameter('reservationID');
		$email = $thisP->getParameter('email');
		$festival = $thisP->getParameter('festival');

		$form = new Form;
		
		$form->addInteger('rezID', 'Číslo rezervace: ')
			->setDefaultValue($reservationID)
			->setHtmlAttribute('placeholder', 'Číslo rezervace')
			->addRule($form::RANGE, 'Číslo rezervace musí být v rozsahu mezi %d a %d.', [0, 1000000])
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('email', 'E-Mail: ')
			->setDefaultValue($email)
			->setHtmlAttribute('placeholder', 'E-Mail')
			->addRule($form::MAX_LENGTH, 'E-Mail je příliš dlouhý', 255)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('festival', 'ID nebo název festivalu: ')
			->setDefaultValue($festival)
			->setHtmlAttribute('placeholder', 'ID nebo název festivalu')
			->addRule($form::MAX_LENGTH, 'ID nebo název festivalu je příliš dlouhý', 255)
            ->setHtmlAttribute('class', 'form-control');
			
		$form->addSubmit('send', 'Vyhledat');

		$form->onSuccess[] = [$thisP, 'searchReservation'];
		return $form;
	}

	/**
	 * Search reservation.
	 */
    public function searchReservation($thisP, $resID, $email, $fesID)
	{
		$festivalID = false;
		if (ctype_digit($fesID)) {
			$festivalID = true;
		}

		// search
		$reservations = array();

		if ($resID != null) {
			// by reservation id
			$reservations = $this->database->table('rezervace')
				->where('rez_ID', $resID);

		} elseif ($fesID != null && $email != null) {
			// by email address & festival id
			if ($festivalID) {
				$reservations = $this->database->table('rezervace')
					->where('div_id.email', $email)
					->where('fes_id', $fesID);
			} else {
				$reservations = $this->database->table('rezervace')
					->where('div_id.email', $email)
					->where('fes_id.nazev', $fesID);
			}

		} elseif ($email != null){
			// by email address
			$reservations = $this->database->table('rezervace')
				->where('div_id.email', $email);
				
		} elseif ($fesID != null){
			// by festival id
			if ($festivalID) {
				$reservations = $this->database->table('rezervace')
					->where('fes_id', $fesID);
			} else {
				$reservations = $this->database->table('rezervace')
					->where('fes_id.nazev LIKE ?', '%' . $fesID . '%');
			}
		}

		// get unregister role id
		$unregisteredUser = $this->database->table('role')
			->where('nazev', 'unregister')
			->fetch();

		// get festival and user info
		$festivals = $users = array();
		foreach ($reservations as $reservation) {
			// festivals
			$festivalsDB = $this->database->table('festival')
			->where(':rezervace.rez_id', $reservation->rez_ID);

			foreach ($festivalsDB as $festivalDB) {
				$festivals[] = $festivalDB->fes_ID;
				$festivals[] = $festivalDB->nazev;
				$festivals[] = $festivalDB->misto;
				$festivals[] = $festivalDB->datum->format('j.n.Y');
				$festivals[] = $festivalDB->cena;
			}
			
			// users
			$usersDB = $this->database->table('divak')
				->where(':rezervace.rez_id', $reservation->rez_ID);

			foreach ($usersDB as $userDB) {
				// is registered or not
				if ($userDB->rol_id == $unregisteredUser->rol_ID) {
					$users[] = "unregistered";
				} else {
					$users[] = $userDB->div_ID;
				}

				$users[] = $userDB->email;
			}
		}

		$thisP->template->reservations = $reservations;
		$thisP->template->festivals = $festivals;
		$thisP->template->users = $users;
	}

	/**
	 * Set reservation state to paid.
	 */
    public function paid($thisP, $resID, $state) {
		
		$count = $this->database->table('rezervace')
			->where('rez_ID', $resID)
			->update([
				'uhrazeno' => $state
			]);

		$thisP->flashMessage('Změna rezervace provedena');
	}
}
