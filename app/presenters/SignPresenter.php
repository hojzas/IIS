<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class SignPresenter extends BasePresenter
{
	protected function startup()
    {
        parent::startup();
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Account:detail');
        }
    }

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
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

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'signInFormSucceeded'];
		return $form;
	}

	public function signInFormSucceeded(Form $form, \stdClass $values): void
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
					$this->flashMessage("Přihlášen uživatel $row->jmeno");
					$this->user->setExpiration('30 minutes');
					$this->user->login(new Nette\Security\Identity($row->div_ID, $row->rol_ID->nazev, null));
					$this->redirect('Homepage:');
				}
				$form->addError('Nesprávné přihlašovací údaje.');
			}

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Nesprávné přihlašovací údaje.');
		}
	}
}
