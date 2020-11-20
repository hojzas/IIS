<?php

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class SearchModel
{
    use Nette\SmartObject;

	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}

	/**
     * Search form.
     */
	public function createSearchForm($thisP)
	{
		$searchValue = $thisP->getParameter('q');

		$form = new Form;

		$form->addText('q')
			->setDefaultValue($searchValue)
            ->setRequired(TRUE)
            ->setHtmlAttribute('placeholder', 'Vyhledat festival, interpreta, lokalitu, žánr...');
		
		$form->onSuccess[] = [$thisP, 'search'];

		return $form;
	}

	/**
     * Check box form factory.
     */
	public function createCheckBoxForm($thisP)
	{
		$tookPlace = $thisP->getParameter('tookPlace');

		$form = new Form;

		$form->addCheckBox('tookPlace', " Zobrazit i uskutečněné")
			->setDefaultValue($tookPlace);

		$form->addSubmit('send', 'Použít filtr');
		
		$form->onSuccess[] = [$thisP, 'tookPlace'];

		return $form;
	}

	/**
	 * Search festivals.
	 */
    public function searchFestivals($q, $tookPlace)
	{
		// search festivals
		$festivals = array();

		$term = "%$q%";

		$festivals = $this->database->table('festival')
			->where('
			festival.nazev LIKE ? OR
			festival.datum LIKE ? OR
			festival.misto LIKE ? OR
			festival.adresa LIKE ? OR
			festival.cena LIKE ? OR
			:stage:vystupuje.int_id:tagovani.zan_id.nazev LIKE ?
			', 
			$term, $term, $term, $term, $term, $term)
			->order('datum');

		if (!$tookPlace) {
			$festivals->where('datum > ?', new \DateTime);
		}

		return $festivals;
	}

	/**
	 * Search interprets.
	 */
    public function searchInterprets($q)
	{
		// search interprets
		$interprets = array();

		$term = "%$q%";

		$interprets = $this->database->table('interpret')
			->where('
			interpret.nazev LIKE ? OR
			interpret.clenove LIKE ? OR
			:tagovani.zan_id.nazev LIKE ?
			', 
			$term, $term, $term)
			->order('nazev');

		return $interprets;
	}

}
