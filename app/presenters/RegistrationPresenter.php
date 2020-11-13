<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\AccountModel;


final class RegistrationPresenter extends BasePresenter
{

    /** @var AccountModel */
    private $accountModel;
        
    public function __construct(AccountModel $accountModel)
    {
        $this->accountModel = $accountModel;
    }

    /**
	 * Registration form factory.
	 */
	protected function createComponentRegistrationForm(): Form
	{
        return $this->accountModel->createRegistrationForm($this);
    }

    /**
	 * Registrate new User.
	 */
    public function registrateUser(Form $form, \stdClass $values): void
	{
        $this->accountModel->registrateUser($this, $values, $form);
    }

}
