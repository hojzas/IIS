<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\AccountModel;

final class AccountPresenter extends BasePresenter
{

    /** @var AccountModel */
    private $accountModel;
        
    public function __construct(AccountModel $accountModel)
    {
        $this->accountModel = $accountModel;
    }

    /**
	 * Check access roles.
	 */
    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    /**
	 * Account detail form factory.
	 */
	protected function createComponentDetailForm(): Form
	{
        return $this->accountModel->createDetailForm($this);
    }
    
    /**
	 * Update User information.
	 */
    public function updateUser(Form $form, \stdClass $values): void
	{
        $this->accountModel->updateUser($this, $values, $form);
    }

    /**
	 * Change password form factory.
	 */
    protected function createComponentPasswordForm(): Form
	{
        return $this->accountModel->createPasswordForm($this);
    }

    /**
	 * Update User password.
	 */
    public function updateUserpassword(Form $form, \stdClass $values): void
	{
        $this->accountModel->updateUserPassword($this, $values, $form);      
    }

    /**
	 * Log out user.
	 */
	public function actionOut(): void
	{
        $this->accountModel->logOut($this);
    }
    
    /**
	 * Render my reservation.
	 */
	public function renderMyReservation(): void
	{
		$this->accountModel->renderMyReservation($this);
	}
}
