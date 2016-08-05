<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ContaoJsonApiElements
{
	public function Elements($imgSize,$id,$pid,$limit,$ptable,$page)
	{	
		if(!$limit) {
			$strLimit = 5;
			} else {
			$strLimit = $limit;
		}

		$json_models = implode("','", array('text','headline','image','gallery','list','youtube'));
		
		if($pid) {
			$where = "WHERE type IN ('".$json_models."') AND ptable='".$ptable."' AND invisible != 1 AND pid IN (".$pid.") AND tstamp > 0 ORDER BY sorting ASC";
		}
		
		if($id) {
			$where = "WHERE type IN ('".$json_models."') AND ptable='".$ptable."' AND invisible != 1 AND id IN (".$id.") AND tstamp > 0 ORDER BY sorting ASC";
		}
		

		$objCount = Database::getInstance()->prepare("SELECT COUNT(id) AS cnt FROM tl_content ".$where)->execute(); 

		$page = $page ? : 1;
		
		$total = $objCount->cnt;
		$pages = ceil($objCount->cnt/$strLimit);

		$limit = $strLimit;
		
		$offset += (max($page, 1) - 1) * $strLimit;
			if ($offset + $limit > $total)
				{
					$limit = $total - $offset;
				}

		$objTable = Database::getInstance()->prepare("SELECT id,type,sorting,ptable FROM tl_content ".$where)->limit($limit,$offset)->execute();

		while ($objTable->next())
		{

			switch ($objTable->type) {
				case "headline":
					$arrTable[]= ContaoJsonApiElements::getHeadlineElement($objTable->id);
				break;
				case "text":
					$arrTable[] = ContaoJsonApiElements::getTextElement($objTable->id,$imgSize);
				break;
				case "image":
					$arrTable[]= ContaoJsonApiElements::getImageElement($objTable->id,$imgSize);
				break;
				case "gallery":
					$arrTable[]= ContaoJsonApiElements::getGalleryElement($objTable->id,$imgSize);
				break;
				case "list":
					$arrTable[]= ContaoJsonApiElements::getListElement($objTable->id);
				break;
				case "youtube":
					$arrTable[]= ContaoJsonApiElements::getYoutubeElement($objTable->id);
				break;
			}
		}
		return ContaoJsonApiHelper::jsonOutput($arrTable,$pages,$total);
	}


	public function getHeadlineElement($str_contentId) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = ContaoJsonApiHelper::parseText(unserialize($objTable->headline)['value']);
		
		return $objOutput;
	}
	public function getTextElement($str_contentId,$arr_imgSize) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,text,addImage,singleSRC,caption FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = ContaoJsonApiHelper::parseText(unserialize($objTable->headline)['value']);
		$objOutput['text'] = ContaoJsonApiHelper::parseText($objTable->text);
		if($objTable->addImage) {
			$objOutput['picture'] = ContaoJsonApiHelper::image2json($objTable->singleSRC,ContaoJsonApiHelper::parseText($objTable->caption),$arr_imgSize);
		}
		return $objOutput;
	}
	public function getImageElement($str_contentId,$arr_imgSize) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,singleSRC,caption FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = ContaoJsonApiHelper::parseText(unserialize($objTable->headline)['value']);
		$objOutput['picture'] = ContaoJsonApiHelper::image2json($objTable->singleSRC,ContaoJsonApiHelper::parseText($objTable->caption),$arr_imgSize);
		
		return $objOutput;
	}
	public function getGalleryElement($str_contentId,$arr_imgSize) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,multiSRC,orderSRC,sortBy FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['test'] = '';
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = ContaoJsonApiHelper::parseText(unserialize($objTable->headline)['value']);
		
		/*
		// In dieser Version der API wird zuerst nur die individuelle Reihenfolge bei der Gallerie unterstützt.
		*/

		if($objTable->sortBy == "custom") {
			$multiSource = $objTable->orderSRC;
		} else {
			$multiSource = $objTable->multiSRC;
		}
		
		$objPictures = \FilesModel::findMultipleByUuids(deserialize($multiSource));
		while ($objPictures->next()) { 
			if($objPictures->type == "folder") {
				$objSubfiles = \FilesModel::findByPid($objPictures->uuid);		
					while ($objSubfiles->next()) {
						$arrTempPictures[] = ContaoJsonApiHelper::image2json($objSubfiles->uuid,$objTable->caption,$arr_imgSize);
						}
			} else {
				$arrTempPictures[] = ContaoJsonApiHelper::image2json($objPictures->uuid,$objTable->caption,$arr_imgSize);	
			}
		}

		$objOutput['pictures']=$arrTempPictures;
		return $objOutput;
	}
	public function getListElement($str_contentId) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,listtype,listitems FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = ContaoJsonApiHelper::parseText(unserialize($objTable->headline)['value']);
		$objOutput['listtype'] = $objTable->listtype;
		$objOutput['list'] = unserialize($objTable->listitems);
		
		return $objOutput;
	}
	public function getYoutubeElement($str_contentId) {
		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,youtube FROM tl_content WHERE id=?")->execute($str_contentId)->next();
		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = ContaoJsonApiHelper::parseText(unserialize($objTable->headline)['value']);
		$objOutput['youtube'] = $objTable->youtube;
		
		return $objOutput;
	}

}
?>