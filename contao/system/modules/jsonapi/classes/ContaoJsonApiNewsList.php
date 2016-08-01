<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ContaoJsonApiNewsList
{
	public function NewsList($imgSize,$id,$pid,$limit,$ptable=tl_news,$page)
	{	

		if(!$limit) {
			$strLimit = 5;
			} else {
			$strLimit = $limit;
		}

		if($pid) {
			$where = "WHERE (start='' OR start<".time().") AND (stop='' OR stop>".time().") AND published=1 AND pid IN (".$pid.") ORDER BY date DESC";
			}
		if($id) {
			$where = "WHERE (start='' OR start<".time().") AND (stop='' OR stop>".time().") AND published=1 AND id IN (".$id.") ORDER BY date DESC";
		}

		$objCount = Database::getInstance()->prepare("SELECT COUNT(id) AS cnt FROM tl_news ".$where)->execute(); 
				
		$page = $page ? : 1;
		
		$total = $objCount->cnt;
		$pages = ceil($objCount->cnt/$strLimit);

		$limit = $strLimit;
		
		$offset += (max($page, 1) - 1) * $strLimit;
			if ($offset + $limit > $total)
				{
					$limit = $total - $offset;
				}

		$objTable = Database::getInstance()->prepare("SELECT id,headline,date,time,author,subheadline,teaser,addImage,singleSRC,caption FROM tl_news ".$where)->limit($limit,$offset)->execute();
				
		while ($objTable->next())
		{
			$objOutput['id'] = $objTable->id;
			$objOutput['headline'] = ContaoJsonApiHelper::parseText($objTable->headline);
			$objOutput['date'] = \System::parseDate(\Config::get('dateFormat'), $objTable->date);
			$objOutput['time'] = \System::parseDate(\Config::get('timeFormat'), $objTable->time);
			$objOutput['author'] = html_entity_decode(\UserModel::findById($objTable->author)->name);
			$objOutput['author_id'] = $objTable->author;
			$objOutput['subheadline'] = ContaoJsonApiHelper::parseText($objTable->subheadline);
			$objOutput['teaser'] = ContaoJsonApiHelper::parseText($objTable->teaser);
			$objOutput['newsurl'] = \Controller::replaceInsertTags('{{news_url::'.$objTable->id.'}}');
			
			if($objTable->addImage) {
				$objOutput['picture'] = ContaoJsonApiHelper::image2json($objTable->singleSRC,ContaoJsonApiHelper::parseText($objTable->caption),$imgSize);
				}

			$arrTable[]= $objOutput;
		}

		return ContaoJsonApiHelper::jsonOutput($arrTable,$pages,$total);
	
	}	
		
}
?>
