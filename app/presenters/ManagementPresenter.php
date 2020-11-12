<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class ManagementPresenter extends BasePresenter
{
    
    /** @var Nette\Database\Context */
    private $database;
    
    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }
    }

	public function __construct(Nette\Database\Context $database)
	{
        $this->database = $database;
    }
    
    /**
	 * Get band ID as parameter.
	 */
    public function renderInterpret(int $bandID): void
	{
	}

    /**
	 * Manage band form factory.
	 */
	protected function createComponentManagementForm(): Form
	{
        $bandID = $this->getParameter('bandID');
        
        $band = $this->database->table('interpret')
        ->where('int_ID', $bandID)
        ->fetch();
        
        $form = new Form;
        
        $form->addText('name', 'Název: ')
            ->setDefaultValue($band->nazev)
            ->setRequired('Prosím zadejte název kapely.')
            ->setHtmlAttribute('placeholder', 'Název kapely')
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addTextArea("members", 'Členové: ')
            ->setDefaultValue($band->clenove)
            ->setRequired('Prosím zadejte člena kapely.')
            ->setHtmlAttribute('placeholder', 'Člen kapely')
            ->setHtmlAttribute('class', 'form-control');            
            
        $form->addText('logo', 'Logo kapely*: ')
            ->setDefaultValue($band->logo)
            ->setRequired('Prosím zadejte název souboru loga kapely.')
            ->setHtmlAttribute('placeholder', 'Logo kapely')
            ->setHtmlAttribute('class', 'form-control');

        // select all genres
        $allGenres = $bandGenres = array();

        $genres = $this->database->table('zanr');
        foreach ($genres as $genre) {
            $allGenres[] = $genre->nazev;
        }

        // select band genres
        foreach ($band->related('tagovani') as $genres) {
            $genre = $this->database->table('zanr')
                ->where('zan_ID', $genres->zan_id)
                ->fetch();
            $bandGenres[] = $genre->zan_ID-1;
        }

        $form->addMultiSelect('genres', 'Žánr:', $allGenres)
            ->setHtmlAttribute('class', 'form-control');
        $form['genres']->setDefaultValue($bandGenres); 
            
        $form->addPassword('password', 'Heslo pro potvrzení:')
			->setRequired('Prosím zadejte Vaše heslo.')
			->setHtmlAttribute('placeholder', 'Heslo')
            ->setHtmlAttribute('class', 'form-control');
            
        $form->addSubmit('send', 'Uložit změny');
            
        // call method UpdateBand() on success
        $form->onSuccess[] = [$this, 'UpdateBand'];
        return $form;
    }
        
    /**
     * Update band info.
     */
    public function UpdateBand(Form $form, \stdClass $values): void
    {
        // check passwords
        try {
            $row = $this->database->table('divak')
            ->where('div_ID', $this->user->getId())
            ->fetch();
            
            $hashedPassword = sha1($values->password);
            
            if (strcmp($row->heslo, $hashedPassword) == 0) {

                $bandID = $this->getParameter('bandID');
                
                // update band information
                $count = $this->database->table('interpret')
                    ->where('int_ID', $bandID)
                    ->update([
                        'nazev' => $values->name,
                        'logo' => $values->logo,
                        'clenove' => $values->members,
                    ]);
                
                // update band genres
                $count = $this->database->table('tagovani')
                    ->where('int_id', $bandID)
                    ->delete();
                foreach ($values->genres as $genre) {
                    $row = $this->database->table('tagovani')
                        ->insert([
                            'zan_id' => $genre + 1,
                            'int_id' => $bandID
                        ]);
                }
                    
                // complete
                $this->flashMessage('Změny uloženy.');
                $this->redirect('Interpret:detail', $bandID);
                    
            } else {
                $form->addError('Nesprávné heslo');
            }
        } catch (Nette\Security\AuthenticationException $e) {
            $this->getUser()->logout();
            $this->flashMessage('Neznámá chyba, přihlašte se znovu.');
            $this->redirect('Sign:in');
        }
    }
            
}
