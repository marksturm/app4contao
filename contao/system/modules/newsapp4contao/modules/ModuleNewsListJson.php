<?php

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
				
				$objCount = $this->Database->prepare("SELECT COUNT(id) AS cnt FROM ".$this->strTable." WHERE ".$this->where)->execute(); 
		
				$page = \Input::get('page') ? : 1;
				$total = $objCount->cnt;
				$limit = $this->strLimit;
				$offset += (max($page, 1) - 1) * $this->strLimit;
				if ($offset + $limit > $total)
				{
					$limit = $total - $offset;
				}

				$objTable = $this->Database->prepare("SELECT id,headline,date,time,author,subheadline,teaser,addImage,singleSRC,caption FROM " . $this->strTable ." WHERE ".$this->where)->limit($limit,$offset)->execute($this->table);
				
				while ($objTable->next())
				{
						
						$objOutput['id'] = $objTable->id;
						$objOutput['headline'] = $objTable->headline;
						$objOutput['date'] = $this->parseDate(\Config::get('dateFormat'), $objTable->date);
						$objOutput['time'] = $this->parseDate(\Config::get('timeFormat'), $objTable->time);
						$objOutput['author'] = \UserModel::findById($objTable->author)->name;
						$objOutput['author_id'] = $objTable->author;
						$objOutput['subheadline'] = $objTable->subheadline;
						$objOutput['teaser'] = $this->truncate($objTable->teaser,180);

						
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

	public function truncate($text, $length = 100, $options = array()) {
    $default = array(
        'ending' => '...', 'exact' => true, 'html' => true
    );
    $options = array_merge($default, $options);
    extract($options);

    if ($html) {
        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        $totalLength = mb_strlen(strip_tags($ending));
        $openTags = array();
        $truncate = '';

        preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
        foreach ($tags as $tag) {
            if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                    array_unshift($openTags, $tag[2]);
                } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                    $pos = array_search($closeTag[1], $openTags);
                    if ($pos !== false) {
                        array_splice($openTags, $pos, 1);
                    }
                }
            }
            $truncate .= $tag[1];

            $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
            if ($contentLength + $totalLength > $length) {
                $left = $length - $totalLength;
                $entitiesLength = 0;
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entitiesLength <= $left) {
                            $left--;
                            $entitiesLength += mb_strlen($entity[0]);
                        } else {
                            break;
                        }
                    }
                }

                $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                break;
            } else {
                $truncate .= $tag[3];
                $totalLength += $contentLength;
            }
            if ($totalLength >= $length) {
                break;
            }
        }
    } else {
        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }
    }
    if (!$exact) {
        $spacepos = mb_strrpos($truncate, ' ');
        if (isset($spacepos)) {
            if ($html) {
                $bits = mb_substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                if (!empty($droppedTags)) {
                    foreach ($droppedTags as $closingTag) {
                        if (!in_array($closingTag[1], $openTags)) {
                            array_unshift($openTags, $closingTag[1]);
                        }
                    }
                }
            }
            $truncate = mb_substr($truncate, 0, $spacepos);
        }
    }
    $truncate .= $ending;

    if ($html) {
        foreach ($openTags as $tag) {
            $truncate .= '</'.$tag.'>';
        }
    }

    return $truncate;
}
}

