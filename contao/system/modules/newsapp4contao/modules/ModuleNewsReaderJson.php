<?php

class ModuleNewsReaderJson extends \Module
{

	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MODULE JSON NEWSREADER ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->strTable = "tl_news";
		$this->$id = \Input::get('id');
		$this->where = "(start='' OR start<".time().") AND (stop='' OR stop>".time().") AND published=1 AND id=".$this->$id."";
		return parent::generate();
	}

	protected function compile()
	{
				
				$objTable = $this->Database->prepare("SELECT id,headline,date,time,author,subheadline,teaser,addImage,singleSRC,caption FROM " . $this->strTable ." WHERE ".$this->where)->execute($this->table);
				
				while ($objTable->next())
				{
						
						$objOutput['id'] = $objTable->id;
						$objOutput['headline'] = $objTable->headline;
						$objOutput['date'] = $this->parseDate(\Config::get('dateFormat'), $objTable->date);
						$objOutput['time'] = $this->parseDate(\Config::get('timeFormat'), $objTable->time);
						$objOutput['author'] = \UserModel::findById($objTable->author)->name;
						$objOutput['author_id'] = $objTable->author;
						$objOutput['subheadline'] = $objTable->subheadline;
						$objOutput['teaser'] = $objTable->teaser;

						
						if($objTable->addImage) {
							
							$str_singleSRC=FilesModel::findByUuid($objTable->singleSRC)->path;
							$arrImage = array('singleSRC'=>FilesModel::findByUuid($objTable->singleSRC)->path,'size'=>$this->imgSize);
							$objTemplate = new FrontendTemplate();
							\Controller::addImageToTemplate($objTemplate, $arrImage);
							
							$arr_picture=$objTemplate->getData();
							$c_sources = count($arr_picture['picture']['sources']);
							
							for( $i_c_sources = 0; $i_c_sources < $c_sources; $i_c_sources++ ) {	
								unset($arr_picture['picture']['sources'][$i_c_sources]['srcset']);
							}

							unset($arr_picture['picture']['alt'],$arr_picture['picture']['title'],$arr_picture['picture']['img']['srcset']);
							
							$objOutput['picture'] = $arr_picture['picture'];
							$objOutput['caption'] = $objTable->caption;
						}
						

						$objElement = \ContentModel::findPublishedByPidAndTable($objTable->id, $this->strTable);
						
						if ($objElement !== null)
						{
							while ($objElement->next())
							{
								$arrTemp = '';
								if($objElement->type=='text') {

									
									$arr_headline=unserialize($objElement->headline);
									if($arr_headline['value']) {
									$arrTemp['headline'] = $arr_headline;
									}
									$test=unserialize($objElement->headline);
									
									$arrTemp['text']=$objElement->text;
									
									if($objElement->addImage) {
										$objTemplate = new FrontendTemplate();
										$arrImage = array('singleSRC'=>FilesModel::findByUuid($objElement->singleSRC)->path,'size'=>$this->imgSize);
										\Controller::addImageToTemplate($objTemplate, $arrImage);
										$arr_picture=$objTemplate->getData();
										
										$c_sources = count($arr_picture['picture']['sources']);
										for( $i_c_sources = 0; $i_c_sources < $c_sources; $i_c_sources++ ) {
								            unset($arr_picture['picture']['sources'][$i_c_sources]['srcset']);
								         }

										unset($arr_picture['picture']['alt'],$arr_picture['picture']['title'],$arr_picture['picture']['img']['srcset']);

										$arrTemp['picture'] = $arr_picture['picture'];
										$arrTemp['caption'] = $objElement->caption;
										
									}
									

								}
								// Hier erfolgt die Zuweisung zu einem Block
								$objOutput['content'][] = $arrTemp;
							}
						}
						$arrTable[]= $objOutput;
					}	
					
					$this->jsonOutput($arrTable);
				
	}
		

	public function jsonOutput($arrTable)
	{
		$arrOutput = array();
		$arrOutput['@date'] = time();
		$arrOutput['@status'] = 'OK';
		$arrOutput['response'] = $arrTable;
		header('Access-Control-Allow-Origin: *');
		header('Content-type: application/json; charset=UTF-8');	
		echo json_encode($arrOutput);
		die();
	}	
}
