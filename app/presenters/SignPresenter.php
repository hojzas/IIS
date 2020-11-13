<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\AccountModel;


final class SignPresenter extends BasePresenter
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
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Account:detail');
        }
    }

	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		return $this->accountModel->createSignInForm($this);
	}

	/**
	 * Sign-in form factory.
	 */
	public function signIn(Form $form, \stdClass $values): void
	{
		$this->accountModel->signIn($this, $values, $form);
	}
}
