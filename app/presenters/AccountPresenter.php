<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class AccountPresenter extends BasePresenter
{

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

}
