<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\InterpretManagerModel;
use App\Model\FesticalManagerModel;


final class ManagementPresenter extends BasePresenter
{

    /** @var InterpretManagerModel */
    private $interpretManagerModel;

    /** @var FesticalManagerModel */
    private $festicalManagerModel;
    
    public function __construct(InterpretManagerModel $interpretManagerModel, FesticalManagerModel $festicalManagerModel)
    {
        $this->interpretManagerModel = $interpretManagerModel;
        $this->festicalManagerModel = $festicalManagerModel;
    }

    /**
	 * Check access roles.
	 */
    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }
    }
    
    /**
     * Get interpret ID.
	 */
    public function renderInterpretEdit(int $bandID): void
	{
        $this->template->bandID = $bandID;
    }

    /**
     * Get festival ID.
	 */
    public function renderFestivalEdit(int $festivalID): void
	{
        $this->template->festivalID = $festivalID;
    }
    
    /**
     * ---------------------------------------- INSERT INTERPRET ----------------------------------------
     */

    /**
     * Insert interpret form factory.
     */
    protected function createComponentInterpretInsertForm(): Form
    {
        return $this->interpretManagerModel->createInterpretInsertForm($this);
    }
        
    /**
     * Insert new interpret.
     */
    public function insertInterpret(Form $form, \stdClass $values): void
    {
        $this->interpretManagerModel->insertInterpret($this, $values, $form);
    }

    /**
	 * ---------------------------------------- EDIT INTERPRET ----------------------------------------
	 */

    /**
	 * Edit interpret form factory.
	 */
	protected function createComponentInterpretEditForm(): Form
	{
        return $this->interpretManagerModel->createInterpretEditForm($this);
    }
        
    /**
     * Update edited interpret info.
     */
    public function updateInterpret(Form $form, \stdClass $values): void
    {
        $this->interpretManagerModel->updateInterpret($this, $values, $form);
    }

    /**
	 * ---------------------------------------- DELETE INTERPRET ----------------------------------------
	 */

    /**
     * Delete interpret.
     */
    public function deleteInterpret(Nette\Forms\Controls\Button $button, $data): void
	{
        $form = $button->getForm();

        $this->interpretManagerModel->deleteInterpret($this, $form);
    }

    /**
     * ---------------------------------------- INSERT FESTIVAL ----------------------------------------
     */

    /**
     * Insert festival form factory.
     */
    protected function createComponentFestivalInsertForm(): Form
    {
        return $this->festicalManagerModel->createFestivalInsertForm($this);
    }
        
    /**
     * Insert new festival.
     */
    public function insertFestival(Form $form, \stdClass $values): void
    {
        $this->festicalManagerModel->insertFestival($this, $values, $form);
    }

    /**
	 * ---------------------------------------- EDIT FESTIVAL ----------------------------------------
	 */

    /**
	 * Edit festival form factory.
	 */
	protected function createComponentFestivalEditForm(): Form
	{
        return $this->festicalManagerModel->createFestivalEditForm($this);
    }
        
    /**
     * Update edited festival info.
     */
    public function updateFestival(Form $form, \stdClass $values): void
    {
        $this->festicalManagerModel->updateFestival($this, $values, $form);
    }

    /**
	 * ---------------------------------------- DELETE FESTIVAL ----------------------------------------
	 */

    /**
     * Delete festival.
     */
    public function deleteFestival(Nette\Forms\Controls\Button $button, $data): void
	{
        $form = $button->getForm();

        $this->festicalManagerModel->deleteFestival($this, $form);
    }
            
}
