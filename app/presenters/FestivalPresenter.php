<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;


final class FestivalPresenter extends BasePresenter
{
    protected function createComponentSearchForm() {

		$form = new Form;
		$form->addText('value', '')
            ->setRequired(TRUE)
            ->setHtmlAttribute('placeholder', 'Vyhledat festival, interpreta, lokalitu...');
		$form->onSuccess[] = [$this, 'searchFormSucceeded'];
		return $form;
	}
		public function searchFormSucceeded($form, $values) {
		$tofind = $values->value;
		$rows = $this->database->table('nekrolog')->select('surname')->where('surname = ?', $tofind);
		foreach ($rows as $row) {
			echo $row->surname;
		}
	}
}
