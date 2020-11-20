<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model\InterpretManagerModel;
use App\Model\FestivalManagerModel;
use App\Model\ReservationManagerModel;
use App\Model\AccountManagerModel;


final class ManagementPresenter extends BasePresenter
{

    /** @var InterpretManagerModel */
    private $interpretManagerModel;

    /** @var FestivalManagerModel */
    private $festivalManagerModel;

    /** @var ReservationManagerModel */
    private $reservationManagerModel;

    /** @var AccountManagerModel */
    private $accountManagerModel;
    
    public function __construct(InterpretManagerModel $interpretManagerModel, FestivalManagerModel $festivalManagerModel, ReservationManagerModel $reservationManagerModel, AccountManagerModel $accountManagerModel)
    {
        $this->interpretManagerModel = $interpretManagerModel;
        $this->festivalManagerModel = $festivalManagerModel;
        $this->reservationManagerModel = $reservationManagerModel;
        $this->accountManagerModel = $accountManagerModel;
    }

    /**
	 * Check access roles.
	 */
    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser') && !$this->getUser()->isInRole('accountant'))) {
            $this->redirect('Homepage:');
        }
    }
    
    /**
     * Get interpret ID.
	 */
    public function renderInterpretEdit(int $bandID): void
	{  
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }

        $this->template->bandID = $bandID;
    }

    /**
	 * Check access roles.
	 */
    public function renderInterpretInsert(): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }
    }

    /**
     * Get festival ID.
	 */
    public function renderFestivalEdit(int $festivalID): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }

        $this->template->festivalID = $festivalID;
    }

    /**
	 * Check access roles.
	 */
    public function renderFestivalInsert(): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }
    }

    /**
     * Get festival ID.
	 */
    public function renderFestivalStageInsert(int $festivalID, int $stageNumber): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }

        $this->template->festivalID = $festivalID;
        $this->template->stageNumber = $stageNumber;
    }
    
    /**
     * Get festival ID.
	 */
    public function renderFestivalStageEdit(int $festivalID, int $stageID): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin') && !$this->getUser()->isInRole('organiser'))) {
            $this->redirect('Homepage:');
        }

        $this->template->festivalID = $festivalID;
        $this->template->stageID = $stageID;
    }

    /**
	 * Check access roles & search account.
	 */
    public function renderAccount($email, $phone, $role): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin'))) {
            $this->redirect('Homepage:');
        }

        $this->accountManagerModel->searchAccount($this, $email, $phone, $role);
    }

    /**
	 * Check access roles & edit account.
	 */
    public function renderAccountEdit($divID): void
	{
        if (!$this->getUser()->isLoggedIn() || (!$this->getUser()->isInRole('admin'))) {
            $this->redirect('Homepage:');
        }

        $this->template->divID = $divID;
    }

    /**
	 * Search reservation.
	 */
    public function renderReservation($reservationID, $email, $festival) {
		$this->reservationManagerModel->searchReservation($this, $reservationID, $email, $festival);
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

        $this->festivalManagerModel->deleteStage($this, $form);
    }

    /**
	 * ---------------------------------------- RESERVATIONS ----------------------------------------
	 */

    /**
     * Search reservation form factory.
     */
    protected function createComponentSearchReservationsForm(): Form
    {
        return $this->reservationManagerModel->createSearchReservationsForm($this);
    }

    /**
	 * Search reservation.
	 */
    public function searchReservation(Form $form, \stdClass $values): void
    {
        $this->redirect('Management:reservation', $values->rezID, $values->email, $values->festival);
    }

    /**
	 * Set reservation state to paid.
	 */
	public function handlePaid(int $resID): void
	{
        $this->reservationManagerModel->paid($this, $resID, 1);
    }

    /**
	 * Set reservation state to unpaid.
	 */
	public function handleUnpaid(int $resID): void
	{
        $this->reservationManagerModel->paid($this, $resID, 0);
    }

    /**
	 * ---------------------------------------- ACCOUNTS ----------------------------------------
	 */
    
    /**
     * Search account form factory.
     */
    protected function createComponentAccountSearchForm(): Form
    {
        return $this->accountManagerModel->createAccountSearchForm($this);
    }

    /**
	 * Search account.
	 */
    public function searchAccount(Form $form, \stdClass $values): void
    {
        $this->redirect('Management:account', $values->email, $values->phone, $values->role);
    }

    /**
     * Edit account form factory.
     */
    protected function createComponentAccountEditForm(): Form
    {
        return $this->accountManagerModel->createAccountEditForm($this);
    }
    
    /**
     * Edit account.
	 */
    public function updateAccount(Form $form, \stdClass $values): void
    {
        $this->accountManagerModel->updateAccount($this, $values, $form);        
    }
    
    /**
	 * Delete account.
	 */
	public function handleDeleteAccount(int $divID): void
	{
        $this->accountManagerModel->deleteAccount($this, $divID);
    }
}
