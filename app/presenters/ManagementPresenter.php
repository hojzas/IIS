<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\InterpretManagerModel;
use App\Model\FestivalManagerModel;


final class ManagementPresenter extends BasePresenter
{

    /** @var InterpretManagerModel */
    private $interpretManagerModel;

    /** @var FestivalManagerModel */
    private $festivalManagerModel;
    
    public function __construct(InterpretManagerModel $interpretManagerModel, FestivalManagerModel $festivalManagerModel)
    {
        $this->interpretManagerModel = $interpretManagerModel;
        $this->festivalManagerModel = $festivalManagerModel;
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
     * Get festival ID.
	 */
    public function renderFestivalStageInsert(int $festivalID, int $stageNumber): void
	{
        $this->template->festivalID = $festivalID;
        $this->template->stageNumber = $stageNumber;
    }
    
    /**
     * Get festival ID.
	 */
    public function renderFestivalStageEdit(int $festivalID, int $stageID): void
	{
        $this->template->festivalID = $festivalID;
        $this->template->stageID = $stageID;
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
        return $this->festivalManagerModel->createFestivalInsertForm($this);
    }
        
    /**
     * Insert new festival.
     */
    public function insertFestival(Form $form, \stdClass $values): void
    {
        $this->festivalManagerModel->insertFestival($this, $values, $form);
    }

    /**
	 * ---------------------------------------- EDIT FESTIVAL ----------------------------------------
	 */

    /**
	 * Edit festival form factory.
	 */
	protected function createComponentFestivalEditForm(): Form
	{
        return $this->festivalManagerModel->createFestivalEditForm($this);
    }
        
    /**
     * Update edited festival info.
     */
    public function updateFestival(Form $form, \stdClass $values): void
    {
        $this->festivalManagerModel->updateFestival($this, $values, $form);
    }

    /**
	 * Festival stage form factory.
	 */
	protected function createComponentStageForm(): Form
	{
        return $this->festivalManagerModel->createStageForm($this);
    }

    /**
	 * Festival stage form factory.
	 */
	protected function createComponentNewStageForm(): Form
	{
        return $this->festivalManagerModel->createNewStageForm($this);
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
        $this->festivalManagerModel->deleteFestival($this, $form);
    }
    
    /**
     * ---------------------------------------- INSERT STAGE ----------------------------------------
	 */

    public function createStage(Form $form, \stdClass $values): void
	{
        $this->festivalManagerModel->createStage($this, $form);
    }
    
    /**
     * Insert new festival stage form factory.
     */
	protected function createComponentStageInsertForm(): Form
	{
        return $this->festivalManagerModel->createStageInsertForm($this);
    }

    /**
     * Insert new festival stage.
     */
    public function insertStage(Form $form, \stdClass $values): void
    {
        $this->festivalManagerModel->insertStage($this, $values, $form);
    }    
    
    /**
     * ---------------------------------------- EDIT STAGE ----------------------------------------
	 */
    /**
     * Insert new festival stage.
     */
    public function editStage(Form $form, \stdClass $values): void
    {
        $this->festivalManagerModel->editStage($this, $values);
    }

    /**
     * Edit festival stage form factory.
     */
	protected function createComponentStageEditForm(): Form
	{
        return $this->festivalManagerModel->createStageEditForm($this);
    }

    /**
	 * Update edited festival stage.
	 */
    public function updateStage(Form $form, \stdClass $values): void
    {
        $this->festivalManagerModel->updateStage($this, $values, $form);
    }

    /**
	 * ---------------------------------------- DELETE STAGE ----------------------------------------
	 */

    /**
     * Delete stage.
     */
    public function deleteStage(Nette\Forms\Controls\Button $button, $data): void
	{
        $form = $button->getForm();

        $this->festivalManagerModel->deleteFestival($this, $form);
    }
            
}
