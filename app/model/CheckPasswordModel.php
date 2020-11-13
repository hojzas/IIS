<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class CheckPasswordModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
    }
    
    /**
	 * Password check.
	 */
    public function checkPassword($password, $thisP)
	{
        // check passwords
        try {
            $row = $this->database->table('divak')
            ->where('div_ID', $thisP->user->getId())
            ->fetch();
            
            $hashedPassword = sha1($password);
            
            if (strcmp($row->heslo, $hashedPassword) == 0) {
				// OK
                return true;
            } else {
                return false;
            }
        } catch (Nette\Security\AuthenticationException $e) {
            $thisP->getUser()->logout();
            $thisP->flashMessage('Neznámá chyba, přihlašte se znovu.');
            $thisP->redirect('Sign:in');
        }
	}
}
