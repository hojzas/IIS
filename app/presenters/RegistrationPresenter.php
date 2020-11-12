<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class RegistrationPresenter extends BasePresenter
{

    /** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
        $this->database = $database;
	}

    /**
	 * Registration form factory.
	 */
	protected function createComponentRegistrationForm(): Form
	{
        $form = new Form;
        
        $form->addText('firstname', 'Jméno: ')
            ->setDefaultValue('Leo')
            ->setRequired('Prosím zadejte Vaše jméno.')
            ->setHtmlAttribute('placeholder', 'Jméno')
            ->setHtmlAttribute('class', 'form-control');
        
        $form->addText('surname', 'Příjmení: ')
            ->setDefaultValue('Messi')
			->setRequired('Prosím zadejte Vaše příjmení.')
			->setHtmlAttribute('placeholder', 'Příjmení')
            ->setHtmlAttribute('class', 'form-control');

        $form->addEmail('email', 'E-Mail: ')
            ->setDefaultValue('@fest.cz')
            ->setRequired('Prosím zadejte Váš email.')
            ->setHtmlAttribute('placeholder', 'E-Mail')
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addText('phone', 'Telefon*: ')
            ->setDefaultValue('123456789')
			->setRequired('Prosím zadejte Vaše telefoní číslo.')
			->setHtmlAttribute('placeholder', 'Telefon')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlType('tel')
            ->addRule($form::PATTERN, 'špatný formát', '[0-9]{9}');

        $form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addPassword('passwordCheck', 'Heslo znovu:')
			->setRequired('Prosím zadejte Vaše nové heslo znovu.')
			->setHtmlAttribute('placeholder', 'Nové heslo znovu')
			->setHtmlAttribute('class', 'form-control');

		$form->addSubmit('send', 'Zaregistrovat');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'RegistrateUser'];
		return $form;
    }

    /**
	 * Insert new User.
	 */
    public function RegistrateUser(Form $form, \stdClass $values): void
	{
        // check passwords
        if (strcmp($values->password, $values->passwordCheck) == 0) {
            
            // prepare
            $hashedPassword = sha1($values->password);
            $roleID = $this->database->table('role')
                ->where('nazev', 'user')
                ->fetch();

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

            $this->flashMessage('Registrace proběhla úspěšně, můžete se přihlásit.');
            $this->redirect('Sign:in');

        } else {
            $form->addError('Zadaná hesla se neshodují!');
        }
    }

}
