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
     * Get details for registration
	 */
    public function renderComplete($phone, $email, $id): void
	{
		$this->template->phone = $phone;
		$this->template->email = $email;
		$this->template->id = $id;
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

    /**
	 * Complete registration form factory.
	 */
	protected function createComponentCompleteRegistrationForm(): Form
	{
        return $this->accountModel->completeRegistrationForm($this);
    }

    /**
	 * Complete registration.
	 */
    public function completeRegistration(Form $form, \stdClass $values): void
	{
        $this->accountModel->completeRegistration($this, $values, $form);
    }

}
