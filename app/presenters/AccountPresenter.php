<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

final class AccountPresenter extends BasePresenter
{

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
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
	protected function createComponentDetailForm(): Form
	{
        $user = $this->database->table('divak')
			->where('div_ID', $this->user->getId())
			->fetch();

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

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'updateUser'];
		return $form;
    }
    
    /**
	 * Update User information.
	 */
    public function updateUser(Form $form, \stdClass $values): void
	{
        $count = $this->database->table('divak')
		->where('div_ID', $this->user->getId())
		->update([
            'jmeno' => $values->firstname,
            'prijmeni' => $values->surname,
            'email' => $values->email,
            'telefon' => $values->phone
        ]);
        
        $this->flashMessage('Změny uloženy.');
    }

    /**
	 * Password form factory.
	 */
    protected function createComponentPasswordForm(): Form
	{
        $user = $this->database->table('divak')
			->where('div_ID', $this->user->getId())
			->fetch();

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

        $form->onSuccess[] = [$this, 'updateUserpassword'];
        return $form;
    }

    /**
	 * Update User password.
	 */
    public function updateUserpassword(Form $form, \stdClass $values): void
	{
        // check
        try {
			$row = $this->database->table('divak')
			->where('div_ID', $this->user->getId())
			->fetch();
            
            $hash = sha1($values->passOld);

            if (strcmp($row->heslo, $hash) == 0) {
                if (strcmp($values->passNew, $values->passNewCheck) == 0) {
                    
                    // update
                    $count = $this->database->table('divak')
                    ->where('div_ID', $this->user->getId())
                    ->update([
                        'heslo' => sha1($values->passNew),
                    ]);
                    
                    $this->flashMessage('Heslo úspěšně změněno.');

                } else {
                    $form->addError('Zadaná hesla se neshodují!');
                }
            } else {
                $form->addError('Špatně zadané heslo!');
            }
            
		} catch (Nette\Security\AuthenticationException $e) {
            $this->getUser()->logout();
            $this->flashMessage('Neznámá chyba, přihlašte se znovu.');
            $this->redirect('Sign:in');
		}
        
    }
}
