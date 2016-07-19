<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ContaoJsonApiElements
{

	public function getHeadlineElement($str_contentId) {

		$objTable = Database::getInstance()->prepare("SELECT id,type,headline FROM tl_content WHERE id=?")->execute($str_contentId)->next();

		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = $objTable->headline;
		
		return $objOutput;
	}

	public function getTextElement($str_contentId,$arr_imgSize) {

		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,text,addImage,singleSRC,caption FROM tl_content WHERE id=?")->execute($str_contentId)->next();

		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = unserialize($objTable->headline);
		$objOutput['text'] = ContaoJsonApi::truncate($objTable->text,200);
		if($objTable->addImage) {
			$objOutput['picture'] = ContaoJsonApi::image2json($objTable->singleSRC,$objTable->caption,$arr_imgSize);
		}

		return $objOutput;
	}

	public function getImageElement($str_contentId,$arr_imgSize) {

		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,singleSRC,caption FROM tl_content WHERE id=?")->execute($str_contentId)->next();

		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = unserialize($objTable->headline);
		$objOutput['picture'] = ContaoJsonApi::image2json($objTable->singleSRC,$objTable->caption,$arr_imgSize);
		
		return $objOutput;
	}

	public function getGalleryElement($str_contentId,$arr_imgSize) {

		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,multiSRC FROM tl_content WHERE id=?")->execute($str_contentId)->next();

		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = unserialize($objTable->headline);	
		$objPictures = \FilesModel::findMultipleByUuids(deserialize($objTable->multiSRC)); 
		
		while ( $objPictures->next() ) { 
			$arrTempPictures[] = ContaoJsonApi::image2json($objPictures->uuid,$objTable->caption,$arr_imgSize);
		}
		
		$objOutput['pictures']=$arrTempPictures;

		return $objOutput;
	}

	public function getListElement($str_contentId) {

		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,listtype,listitems FROM tl_content WHERE id=?")->execute($str_contentId)->next();

		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = unserialize($objTable->headline);
		$objOutput['listtype'] = $objTable->listtype;
		$objOutput['list'] = unserialize($objTable->listitems);
		
		return $objOutput;
	}

	public function getYoutubeElement($str_contentId) {

		$objTable = Database::getInstance()->prepare("SELECT id,type,headline,youtube FROM tl_content WHERE id=?")->execute($str_contentId)->next();

		$objOutput['type'] = $objTable->type;
		$objOutput['id'] = $objTable->id;
		$objOutput['headline'] = unserialize($objTable->headline);
		$objOutput['youtube'] = $objTable->youtube;
		
		return $objOutput;
	}
}
?>
