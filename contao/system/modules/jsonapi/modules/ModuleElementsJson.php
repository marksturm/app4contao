<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ModuleElementsJson extends \Module
{

	protected $strLimit = 5;
	
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MODULE JSON GET ELEMENTS BY PARENT ID ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$pid = \Input::get('pid');

		$this->json_models = implode("','", array('text','headline','image','gallery','list','youtube'));

			if($pid) {
				$this->where = "WHERE type IN ('".$this->json_models."') AND invisible != 1 AND pid IN (".$pid.") AND tstamp > 0 ORDER BY sorting ASC";
				}
			else {
				$this->where = "WHERE type IN ('".$this->json_models."') AND tstamp > 0 AND invisible != 1 ORDER BY sorting ASC";
			}

		return parent::generate();

	}

	protected function compile()
	{			
		
		$objCount = $this->Database->prepare("SELECT COUNT(id) AS cnt FROM tl_content ".$this->where)->execute(); 
		
		$page = \Input::get('page') ? : 1;
		
		$total = $objCount->cnt;
		$pages = ceil($objCount->cnt/$this->strLimit);

		$limit = $this->strLimit;
		
		$offset += (max($page, 1) - 1) * $this->strLimit;
			if ($offset + $limit > $total)
			{
				$limit = $total - $offset;
			}

		$objTable = $this->Database->prepare("SELECT id,type,sorting FROM tl_content ".$this->where)->limit($limit,$offset)->execute($this->table);

		while ($objTable->next())
		{
			switch ($objTable->type) {
				case "headline":
					$arrTable[]= ContaoJsonApiElements::getHeadlineElement($objTable->id);
				break;
				case "text":
					$arrTable[] = ContaoJsonApiElements::getTextElement($objTable->id,$this->imgSize);
				break;
				case "image":
					$arrTable[]= ContaoJsonApiElements::getImageElement($objTable->id,$this->imgSize);
				break;
				case "gallery":
					$arrTable[]= ContaoJsonApiElements::getGalleryElement($objTable->id,$this->imgSize);
				break;
				case "list":
					$arrTable[]= ContaoJsonApiElements::getListElement($objTable->id);
				break;
				case "youtube":
					$arrTable[]= ContaoJsonApiElements::getYoutubeElement($objTable->id);
				break;
			}
		}

		ContaoJsonApi::jsonOutput($arrTable,$pages,$total);			
	}

}

