<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class SignPresenter extends BasePresenter
{
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
			->setRequired('Prosím zadejte Váš email.');
		$form->setHtmlAttribute('placeholder', 'E-Mail');
		$form->setHtmlAttribute('class', 'form-control');

		$form->addPassword('password', '')
			->setRequired('Prosím zadejte Vaše heslo.');
		$form->setHtmlAttribute('placeholder', 'Heslo');

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
				$form->addError('User not found.');
			} else {
			
				if (strcmp($row->heslo, $values->password) == 0) {

					// successfully log in
					$this->user->setExpiration('30 minutes');
					$this->user->login(new Nette\Security\Identity($row->div_ID, null, null));
					$this->redirect('Homepage:');
				}
				$form->addError('Incorrect username or password.');
			}

		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError('Incorrect username or password.');
		}
	}


	// log out
	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Odhlášení proběhlo úspěšně.');
		$this->redirect('Homepage:');
	}
}
