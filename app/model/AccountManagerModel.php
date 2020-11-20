<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class AccountManagerModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * ---------------------------------------- ACCOUNTS ----------------------------------------
	 */

	/**
     * Search account form factory.
     */
	public function createAccountSearchForm($thisP)
	{
		// select all roles, sorted by their IDs
		$allRoles = array();
		$roles = $this->database->table('role');
		foreach ($roles as $role) {
			if ($role->nazev != "unregister")
				$allRoles[$role->rol_ID] = $role->nazev;
		}

		$email = $thisP->getParameter('email');
		$phone = $thisP->getParameter('phone');
		$role = $thisP->getParameter('role');

		$form = new Form;
			
		$form->addText('email', 'E-Mail: ')
			->setDefaultValue($email)
			->setHtmlAttribute('placeholder', 'E-Mail')
			->addRule($form::MAX_LENGTH, 'E-Mail je příliš dlouhý', 255)
			->setHtmlAttribute('class', 'form-control');

		$form->addText('phone', 'Telefon*: ')
			->setDefaultValue($phone)
			->setHtmlAttribute('placeholder', 'Telefon')
			->setHtmlAttribute('class', 'form-control')
			->addRule($form::PATTERN, 'špatný formát', '[0-9]{9}')
			->setHtmlType('tel');

		$form->addSelect("role", "Role: ", $allRoles)
			->setDefaultValue($role);
			
		$form->addSubmit('send', 'Vyhledat');

		$form->onSuccess[] = [$thisP, 'searchAccount'];
		return $form;
	}

	/**
	 * Search account.
	 */
    public function searchAccount($thisP, $email, $phone, $roleID)
	{
		// search
		$accounts = array();

		if ($email != null) {
			// by email address
			$accounts = $this->database->table('divak')
				->where('email', $email);

		} elseif ($phone != null){
			// by phone
			$accounts = $this->database->table('divak')
				->where('telefon', $phone);
				
		} elseif ($roleID != null){
			// by role
			$accounts = $this->database->table('divak')
				->where('rol_id', $roleID);
		}

		$thisP->template->accounts = $accounts;
	}

	/**
	 * Delete account.
	 */
    public function deleteAccount($thisP, $divID) {
		
		// delete all reservations of this account
		$count = $this->database->table('rezervace')
			->where('div_id', $divID)
			->delete();

		$count = $this->database->table('divak')
			->where('div_ID', $divID)
			->delete();

		$thisP->flashMessage('Účet byl odstraněn');
	}

	/**
	 * Account detail form factory.
	 */
    public function createAccountEditForm($thisP)
	{
		$user = $this->database->table('divak')
			->where('div_ID', $thisP->getParameter('divID'))
			->fetch();

		if (!$user) {
			$thisP->error('Uživatel nenalezen');
		}

		$form = new Form;
		
		$form->addText('firstname', 'Jméno: ')
			->setDefaultValue($user->jmeno)
			->setRequired('Prosím zadejte Vaše jméno.')
			->setHtmlAttribute('placeholder', 'Jméno')
			->setHtmlAttribute('class', 'form-control');
		
		$form->addText('surname', 'Příjmení: ')
			->setDefaultValue($user->prijmeni)
			->setRequired('Prosím zadejte Vaše příjmení.')
			->setHtmlAttribute('placeholder', 'Příjmení')
			->setHtmlAttribute('class', 'form-control');

		$form->addEmail('email', 'E-Mail: ')
			->setDefaultValue($user->email)
			->setRequired('Prosím zadejte Váš email.')
			->setHtmlAttribute('placeholder', 'E-Mail')
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('phone', 'Telefon*: ')
			->setDefaultValue($user->telefon)
			->setRequired('Prosím zadejte Vaše telefoní číslo.')
			->setHtmlAttribute('placeholder', 'Telefon')
			->setHtmlAttribute('class', 'form-control')
			->setHtmlType('tel')
			->addRule($form::PATTERN, 'špatný formát', '[0-9]{9}');

		// select all roles, sorted by their IDs
		$allRoles = array();
		$roles = $this->database->table('role');
		foreach ($roles as $role) {
			if ($role->nazev != "unregister")
				$allRoles[$role->rol_ID] = $role->nazev;
		}

		$role = $this->database->table('role')
			->where(':divak.div_ID', $user->div_ID)
			->fetch();

		$form->addSelect("role", "Role: ", $allRoles)
			->setDefaultValue($role);

		$form->addSubmit('send', 'Uložit změny');

		// call method updateUser() on success
		$form->onSuccess[] = [$thisP, 'updateAccount'];
		return $form;
	}

	/**
	 * Update User information.
	 */
    public function updateAccount($thisP, $values, $form)
	{
		$count = $this->database->table('divak')
			->where('div_ID', $thisP->getParameter('divID'))
			->update([
				'jmeno' => $values->firstname,
				'prijmeni' => $values->surname,
				'email' => $values->email,
				'telefon' => $values->phone,
				'rol_id' => $values->role
			]);
		
		$thisP->flashMessage('Změny uloženy.');
	}
}
