<?php

/**
 * @author Mark Sturm - privat@mark-sturm.de
 * @license CC BY-NC-SA 4.0
*/

class ContaoJsonApiHelper
{
	public function jsonOutput($arrTable,$str_pages,$str_entries)
	{
		$arrOutput = array();
		$arrOutput['date'] = time();
		
		if($arrTable) {
			$arrOutput['status'] = 'OK';
			$arrOutput['pages'] = $str_pages;
			$arrOutput['totalentries'] = $str_entries;
			$arrOutput['response'] = $arrTable;
		}
		else {
			$arrOutput['status'] = 'ERROR';
		}
		$arrOutput['@license'] = "CC BY-NC-SA 4.0";
		$arrOutput['@author'] = "Mark Sturm";	
		return $arrOutput;
		die();
	}

	public function parseText($str_text)
	{
		return \Controller::replaceInsertTags(html_entity_decode($str_text));	
	}

	public function stupidityCheck($checkfor,$str_get)	{

		if($checkfor=='num') {
			
				if(!is_numeric($str_get)) { $has_error = true;	}
			
		}
		elseif($checkfor=='ptable') {

				if(preg_match("/[^a-z_\-0-9]/i", $str_get)!=null) { $has_error = true;}

		}  else {
			
				if(!preg_match("/^\d+(?:,\d+)*$/", $str_get)) { $has_error = true;}	
			
		}

			if($has_error) {
				die('FU');
			}
	
		return $str_get;			
		}

	public function image2json($str_src,$str_caption,$arr_imgSize) {

		$str_singleSRC=FilesModel::findByUuid($str_src);
		
		$arrImage_id = array('id'=>$str_singleSRC->id);
		$arrImage_caption = array('caption'=>$str_caption);
		$arrImage = array('singleSRC'=>$str_singleSRC->path,'size'=>$arr_imgSize);
		
		$objTemplate = new FrontendTemplate();
		\Controller::addImageToTemplate($objTemplate, $arrImage);
		
		$arr_picture=$objTemplate->getData();

		$c_sources = count($arr_picture['picture']['sources']);
		
		for( $i_c_sources = 0; $i_c_sources < $c_sources; $i_c_sources++ ) {	

			unset($arr_picture['picture']['sources'][$i_c_sources]['srcset']);
		}

		unset($arr_picture['picture']['alt'],$arr_picture['picture']['title'],$arr_picture['picture']['img']['srcset']);
		
		$arrPictures = array_merge($arrImage_id, $arr_picture['picture']);
		$objOutput['picture'] = array_merge($arrPictures,$arrImage_caption);
		
		return $objOutput['picture'];
	}

	public function truncate($text, $length, $options = array()) {
	
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
?>
