<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;
use App\Model\CheckPasswordModel;

class AccountModel
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
	 * ---------------------------------------- REGISTRATION ----------------------------------------
	 */

	/**
	 * Registration form factory.
	 */
	public function createRegistrationForm($thisP)
	{
		$form = new Form;
		
		$form->addText('firstname', 'Jméno: ')
			->setDefaultValue('Leo')
			->setRequired('Prosím zadejte Vaše jméno.')
			->setHtmlAttribute('placeholder', 'Jméno')
			->addRule($form::MAX_LENGTH, 'Jméno je příliš dlouhé', 20)
			->setHtmlAttribute('class', 'form-control');
		
		$form->addText('surname', 'Příjmení: ')
			->setDefaultValue('Messi')
			->setRequired('Prosím zadejte Vaše příjmení.')
			->setHtmlAttribute('placeholder', 'Příjmení')
			->addRule($form::MAX_LENGTH, 'Příjmení je příliš dlouhé', 20)
			->setHtmlAttribute('class', 'form-control');

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

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
			->addRule($form::MAX_LENGTH, 'Heslo je příliš dlouhé', 255)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addPassword('passwordCheck', 'Heslo znovu:')
			->setRequired('Prosím zadejte Vaše nové heslo znovu.')
			->setHtmlAttribute('placeholder', 'Nové heslo znovu')
			->setHtmlAttribute('class', 'form-control');

		$form->addSubmit('send', 'Zaregistrovat');

		// call method registrateUser() on success
		$form->onSuccess[] = [$thisP, 'registrateUser'];
		return $form;
	}

	/**
	 * Registrate new User.
	 */
	public function registrateUser($thisP, $values, $form)
	{
		// check passwords
		if (strcmp($values->password, $values->passwordCheck) == 0) {
			
			// prepare
			$hashedPassword = sha1($values->password);
			$roleID = $this->database->table('role')
				->where('nazev', 'user')
				->fetch();
				
			if (!$roleID) {
				$thisP->error('Registrace se nezdařila');
			}

			// insert
			$row = $this->database->table('divak')
				->insert([
					'jmeno' => $values->firstname,
					'prijmeni' => $values->surname,
					'telefon' => $values->phone,
					'email' => $values->email,
					'heslo' => $hashedPassword,
					'rol_ID' => $roleID
				]);

			if (!$row) {
				$thisP->error('Registrace se nezdařila');
			}
			
			$thisP->flashMessage('Registrace proběhla úspěšně, můžete se přihlásit.');
			$thisP->redirect('Sign:in');
			
		} else {
			$form->addError('Zadaná hesla se neshodují!');
		}
	}

	/**
	 * Complete registration form factory.
	 */
	public function completeRegistrationForm($thisP)
	{
		$phone = $thisP->getParameter('phone');
		$email = $thisP->getParameter('email');

		$form = new Form;
		
		$form->addText('firstname', 'Jméno: ')
			->setDefaultValue('Leo')
			->setRequired('Prosím zadejte Vaše jméno.')
			->setHtmlAttribute('placeholder', 'Jméno')
			->addRule($form::MAX_LENGTH, 'Jméno je příliš dlouhé', 20)
			->setHtmlAttribute('class', 'form-control');
		
		$form->addText('surname', 'Příjmení: ')
			->setDefaultValue('Messi')
			->setRequired('Prosím zadejte Vaše příjmení.')
			->setHtmlAttribute('placeholder', 'Příjmení')
			->addRule($form::MAX_LENGTH, 'Příjmení je příliš dlouhé', 20)
			->setHtmlAttribute('class', 'form-control');

		$form->addEmail('email', 'E-Mail: ')
			->setDefaultValue($email)
			->setRequired('Prosím zadejte Váš email.')
			->setHtmlAttribute('placeholder', 'E-Mail')
			->addRule($form::MAX_LENGTH, 'Email je příliš dlouhý', 255)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addText('phone', 'Telefon*: ')
			->setDefaultValue($phone)
			->setRequired('Prosím zadejte Vaše telefoní číslo.')
			->setHtmlAttribute('placeholder', 'Telefon')
			->setHtmlAttribute('class', 'form-control')
			->addRule($form::PATTERN, 'špatný formát', '[0-9]{9}')
			->setHtmlType('tel');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
			->addRule($form::MAX_LENGTH, 'Heslo je příliš dlouhé', 255)
			->setHtmlAttribute('class', 'form-control');
			
		$form->addPassword('passwordCheck', 'Heslo znovu:')
			->setRequired('Prosím zadejte Vaše nové heslo znovu.')
			->setHtmlAttribute('placeholder', 'Nové heslo znovu')
			->setHtmlAttribute('class', 'form-control');

		$form->addSubmit('send', 'Zaregistrovat');

		// call method completeRegistration() on success
		$form->onSuccess[] = [$thisP, 'completeRegistration'];
		return $form;
	}

	/**
	 * Complete registration.
	 */
	public function completeRegistration($thisP, $values, $form)
	{
		$id = $thisP->getParameter('id');

		// check passwords
		if (strcmp($values->password, $values->passwordCheck) == 0) {
			
			// prepare
			$hashedPassword = sha1($values->password);
			$roleID = $this->database->table('role')
				->where('nazev', 'user')
				->fetch();
				
			if (!$roleID) {
				$thisP->error('Registrace se nezdařila');
			}

			// insert
			$row = $this->database->table('divak')
				->where('div_ID', $id)
				->update([
					'jmeno' => $values->firstname,
					'prijmeni' => $values->surname,
					'telefon' => $values->phone,
					'email' => $values->email,
					'heslo' => $hashedPassword,
					'rol_ID' => $roleID
				]);

			if (!$row) {
				$thisP->error('Registrace se nezdařila');
			}
			
			$thisP->flashMessage('Registrace proběhla úspěšně, můžete se přihlásit.');
			$thisP->redirect('Sign:in');
			
		} else {
			$form->addError('Zadaná hesla se neshodují!');
		}
	}
	
	/**
	 * ---------------------------------------- SIGN IN ----------------------------------------
	 */

	/**
	 * Sign-in form factory.
	 */
	public function createSignInForm($thisP)
	{
		$form = new Form;

		$form->addEmail('email', '')
			->setDefaultValue('@fest.cz')
			->setRequired('Prosím zadejte Váš email.')
			->setHtmlAttribute('placeholder', 'E-Mail')
			->setHtmlAttribute('class', 'form-control');

		$form->addPassword('password', '')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
			->setHtmlAttribute('class', 'form-control');

		$form->addSubmit('send', 'Přihlásit se');

		// call method signIn() on success
		$form->onSuccess[] = [$thisP, 'signIn'];
		return $form;
	}

	/**
	 * Sign-in form factory.
	 */
    public function signIn($thisP, $values, $form)
	{
		try {
			$row = $this->database->table('divak')
			->where('email', $values->email)
			->fetch();

			if (!$row) {
				$form->addError('Uživatel se zadaným emailem nenalezen.');
			} else {

				$hashedPassword = sha1($values->password);
			
				if (strcmp($row->heslo, $hashedPassword) == 0) {
					
					// successfully log in
					$thisP->flashMessage("Přihlášen uživatel $row->jmeno");
					$thisP->user->setExpiration('30 minutes');
					$thisP->user->login(new Nette\Security\Identity($row->div_ID, $row->rol_ID->nazev, null));
					$thisP->redirect('Homepage:');
				}
				$form->addError('Nesprávné přihlašovací údaje.');
			}
			
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací údaje.');
		}
	}
    
	/**
	 * ---------------------------------------- EDIT ----------------------------------------
	 */

    /**
	 * Account detail form factory.
	 */
    public function createDetailForm($thisP)
	{
		$user = $this->database->table('divak')
			->where('div_ID', $thisP->user->getId())
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

		$form->addSubmit('send', 'Uložit změny');

		// call method updateUser() on success
		$form->onSuccess[] = [$thisP, 'updateUser'];
		return $form;
	}

	/**
	 * Update User information.
	 */
    public function updateUser($thisP, $values, $form)
	{
		$count = $this->database->table('divak')
			->where('div_ID', $thisP->user->getId())
			->update([
				'jmeno' => $values->firstname,
				'prijmeni' => $values->surname,
				'email' => $values->email,
				'telefon' => $values->phone
			]);
		
		$thisP->flashMessage('Změny uloženy.');
	}
	
	/**
	 * Change password form factory.
	 */
    public function createPasswordForm($thisP)
	{
		$user = $this->database->table('divak')
			->where('div_ID', $thisP->user->getId())
			->fetch();

		if (!$user) {
			$thisP->error('Uživatel nenalezen');
		}

        $form = new Form;

        $form->addPassword('passOld', 'Staré heslo:')
			->setRequired('Prosím zadejte Vaše staré heslo.')
			->setHtmlAttribute('placeholder', 'Staré heslo')
			->setHtmlAttribute('class', 'form-control');

        $form->addPassword('passNew', 'Nové heslo:')
			->setRequired('Prosím zadejte Vaše nové heslo.')
			->setHtmlAttribute('placeholder', 'Nové heslo')
            ->setHtmlAttribute('class', 'form-control');

        $form->addPassword('passNewCheck', 'Nové heslo znovu:')
			->setRequired('Prosím zadejte Vaše nové heslo znovu.')
			->setHtmlAttribute('placeholder', 'Nové heslo znovu')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', 'Změnit heslo');

        $form->onSuccess[] = [$thisP, 'updateUserpassword'];
		return $form;
	}

	/**
	 * Update User password.
	 */
    public function updateUserPassword($thisP, $values, $form)
	{
		// check passwords
		if (!$this->checkPasswordModel->checkPassword($values->passOld, $thisP)) {
			$form->addError('Nesprávné heslo');
		} else {
			if (strcmp($values->passNew, $values->passNewCheck) == 0) {
				
				// update
				$count = $this->database->table('divak')
					->where('div_ID', $thisP->user->getId())
					->update([
						'heslo' => sha1($values->passNew),
					]);
				
				$thisP->flashMessage('Heslo úspěšně změněno.');
				$thisP->redirect('Account:detail');

			} else {
				$form->addError('Zadaná hesla se neshodují!');
			}
		}
	}

	/**
	 * ---------------------------------------- SIGN OUT ----------------------------------------
	 */
	
	/**
	 * Log out user.
	 */
    public function logOut($thisP)
	{
		$thisP->getUser()->logout();
		$thisP->flashMessage('Odhlášení proběhlo úspěšně.');
		$thisP->redirect('Homepage:');
	}
}
