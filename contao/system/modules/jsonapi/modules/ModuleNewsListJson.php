<?php

/**
 * @package		FacebookConnect
 * @author    	Mark Sturm - privat@mark-sturm.de
 * @license		CC BY-NC-SA 4.0
*/

class ModuleNewsListJson extends \Module
{

	protected $strLimit = 5;
	
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MODULE JSON NEWSLIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->strTable = "tl_news";
		
		$pid = \Input::get('pid');

		if($pid) {
			$this->where = "(start='' OR start<".time().") AND (stop='' OR stop>".time().") AND published=1 AND pid IN (".$pid.") ORDER BY date DESC";
			}
		else {
			$this->where = "(start='' OR start<".time().") AND (stop='' OR stop>".time().") AND published=1 ORDER BY date DESC";
		}

		return parent::generate();
	}

	protected function compile()
	{
				
		$objCount = $this->Database->prepare("SELECT COUNT(id) AS cnt FROM tl_news WHERE ".$this->where)->execute(); 
		
		$page = \Input::get('page') ? : 1;
		
		$total = $objCount->cnt;
		$pages = ceil($objCount->cnt/$this->strLimit);

		$limit = $this->strLimit;
		
		$offset += (max($page, 1) - 1) * $this->strLimit;
			if ($offset + $limit > $total)
			{
				$limit = $total - $offset;
			}

		$objTable = $this->Database->prepare("SELECT id,headline,date,time,author,subheadline,teaser,addImage,singleSRC,caption FROM tl_news WHERE ".$this->where)->limit($limit,$offset)->execute($this->table);
				
		while ($objTable->next())
		{
			$objOutput['id'] = $objTable->id;
			$objOutput['headline'] = $objTable->headline;
			$objOutput['date'] = $this->parseDate(\Config::get('dateFormat'), $objTable->date);
			$objOutput['time'] = $this->parseDate(\Config::get('timeFormat'), $objTable->time);
			$objOutput['author'] = \UserModel::findById($objTable->author)->name;
			$objOutput['author_id'] = $objTable->author;
			$objOutput['subheadline'] = $objTable->subheadline;
			$objOutput['teaser'] = ContaoJsonApi::truncate($objTable->teaser,180);

			if($objTable->addImage) {
				$objOutput['picture'] = ContaoJsonApi::image2json($objTable->singleSRC,$objTable->caption,$this->imgSize);
				}

			$arrTable[]= $objOutput;
		}
					
		ContaoJsonApi::jsonOutput($arrTable,$pages,$total);			
	}
}