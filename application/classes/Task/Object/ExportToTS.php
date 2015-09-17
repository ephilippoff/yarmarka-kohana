<?php defined('SYSPATH') or die('No direct script access.');
 
class Task_Object_ExportToTS extends Minion_Task
{
	
    protected $_options = array(
		'skey' => NULL,
    );	
	
    protected function _execute(array $params)
    {
		if ($params['skey'] != 'g4HFhdeg7656trhdHGHe') die('access error');
		
		DEFINE ('EXPORT_PATH','/home/terrasoft/syncro/terrasoft'.Kohana::$config->load("common.sync_prefix").'/site/');
		
		$a=0;
		if (is_dir(EXPORT_PATH))
		{
			if ($dh = opendir(EXPORT_PATH)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
					if (filetype(EXPORT_PATH . $file) <> 'dir') 
					{
						$a++;
					}
				}
				closedir($dh);
			}
		} 
		else 
		{
			Minion_CLI::write('dir not found');
		}
	
		if ($a) die('folder not empty');
		
		$arrIDs = ORM::factory('Temp_Objects')->where('status', '=', 0)->find_all();
		$ids = array();
			
		if ($arrIDs->count())
		{
			$countObjectsImages=0;
			
			$xml=new DomDocument('1.0','utf-8');
			$sync = $xml->appendChild($xml->createElement('sync'));
			$attributes = $sync->appendChild($xml->createElement('attributes'));
			$values = $sync->appendChild($xml->createElement('values'));
			$advertisings = $sync->appendChild($xml->createElement('advertisings'));

			foreach ($arrIDs as $xkey => $object_id)
			{
				$ids[] = $object_id->record_id;
				$objectInfo = ORM::factory('Object', $object_id->record_id);
				
				if ($objectInfo->loaded())
				{
					$filename = $email = "";								
					
					$user = ORM::factory ('User', (int)$objectInfo->author);
					if ($user->loaded())
						$email = $user->email;					
					
					$advertisings->setAttribute("AdvertisingCount", $arrIDs->count());
					$advertisings->setAttribute("AdvertisingsWithImageCount", $countObjectsImages);	     
             
					$city_id=$objectInfo->city_id;
					$category=$objectInfo->category;
					$action=$objectInfo->action;
	     
					$price=(int)$objectInfo->price;

					if (!empty($objectInfo->date_created))
					{
					   $temp_created = explode(".", $objectInfo->date_created);
					   $date_created = $temp_created[0];
					}
					else
					{
					   $date_created = '';
					}
					
					if (!empty($objectInfo->date_expired))
					{
						$temp_expired = explode(".", $objectInfo->date_expired);
						$date_expired = $temp_expired[0];
					}
					else
					{
						$date_expired = '';
					}
             
					$full_text = $objectInfo->full_text;
        
					$advertising = $advertisings->appendChild($xml->createElement('advertising'));
					$advertising->setAttribute("Num", $xkey);
					$TSAdvertisingID = $advertising->appendChild($xml->createElement('TSAdvertisingID'));
					$SiteAdvertisingID = $advertising->appendChild($xml->createElement('SiteAdvertisingID'));
					$SiteAdvertisingID->appendChild($xml->createTextNode($object_id->record_id));
					$CityID = $advertising->appendChild($xml->createElement('CityID'));
					$CityID->appendChild($xml->createTextNode($city_id));
					$Prices = $advertising->appendChild($xml->createElement('Price'));
					$Prices->appendChild($xml->createTextNode($price));
					$HeadingID = $advertising->appendChild($xml->createElement('HeadingID'));
					$HeadingID->appendChild($xml->createTextNode($category));
					$SubHeadingID = $advertising->appendChild($xml->createElement('SubHeadingID'));
					$SubHeadingID->appendChild($xml->createTextNode($action));
					$CreateDate = $advertising->appendChild($xml->createElement('CreateDate'));
					$CreateDate->appendChild($xml->createTextNode($date_created));
					$TypeID = $advertising->appendChild($xml->createElement('TypeID'));
					$StartShowingDate = $advertising->appendChild($xml->createElement('StartShowingDate'));
					$StopShowingDate = $advertising->appendChild($xml->createElement('StopShowingDate'));
					$StopShowingDate->appendChild($xml->createTextNode($date_expired));
					$Text = $advertising->appendChild($xml->createElement('Text'));
					$Text->appendChild($xml->createTextNode($full_text));
					$Author = $advertising->appendChild($xml->createElement('User'));
					$Author->appendChild($xml->createTextNode($objectInfo->author));
					$Email_tag = $advertising->appendChild($xml->createElement('Email'));
					$Email_tag->appendChild($xml->createTextNode($email));			              
					$Command = $advertising->appendChild($xml->createElement('Command'));

					//Контакты	  
					$contacts = $objectInfo->get_contacts();
					$contacts_str = '';

					if ($contacts->count())
					{	
						$contacts_xml = $advertising->appendChild($xml->createElement('Contacts'));
		 
						foreach ($contacts as $key => $value) 
						{
							$contact_xml = $contacts_xml->appendChild($xml->createElement('Contact'));
							$contact_xml->appendChild($xml->createTextNode($value->contact));			 
							$contact_xml->setAttribute('type', $value->contact_type_id);
		     
							if (in_array($value->contact_type_id, array(1,2))) $contacts_str .= $value->contact.', ';
						}	     
					}
	     
					$Contact = $advertising->appendChild($xml->createElement('Contact'));
					$Contact->appendChild($xml->createTextNode($contacts_str));	     
	     
					//Фото
					$ImagesObjectArr = ORM::factory('Object_Attachment')
							->where('object_id', '=', $objectInfo->id)
							->where('type', '=', 0)
							->find_all();
             
					if ($ImagesObjectArr->count())
					{
						$images = $advertising->appendChild($xml->createElement('Images'));
						foreach ($ImagesObjectArr as $i_key => $image_item)
						{                     
							$filename = $image_item->filename; 
							$ImagesObject=Imageci::getSavePaths($image_item->filename);
							$path = substr($ImagesObject['272x203'], 2, strlen($ImagesObject['272x203']));
							$oldfile=$_SERVER['DOCUMENT_ROOT'].'/'.$path;
							if (file_exists($oldfile))
							{ 			    
								$image = $images->appendChild($xml->createElement('Image'));
								$image->appendChild($xml->createTextNode(Region::get_subdomain_by_city_id((int)$objectInfo->city_id).$path));
								if ($image_item->id == $objectInfo->main_image_id)
									$image->setAttribute('is_main', 1); 
								$countObjectsImages++;
							}
						} 
					}					
				}// $objectinfo->loaded()
			} //foreach $arrIDs

			$advertising_values = $sync->appendChild($xml->createElement('advertising_values'));
			
			foreach($arrIDs as $xkey=>$object_id)
			{
				$objectInfo = ORM::factory('Object', $object_id->record_id);
				
				if($objectInfo->loaded())
				{
					$AttributesObject = $objectInfo->get_attributes();
					
					if ($AttributesObject->count())
					{         
						$mCount=1;
						foreach ($AttributesObject as $attr_item)
						{
							if (isset($attr_item->id_tr) && $attr_item->id_tr <> '' && isset($attr_item->attr_value_id))
							{
								$row2 = $advertising_values->appendChild($xml->createElement('row'));
								$row2->setAttribute("advNum", $mCount); 
								$TSValueID = $row2->appendChild($xml->createElement('TSValueID'));
								if (isset($attr_item->tr_attr_value))
								{
									$TSValueID->appendChild($xml->createTextNode($attr_item->tr_attr_value));
								}
								$SiteValueID = $row2->appendChild($xml->createElement('SiteValueID'));
								$SiteValueID->appendChild($xml->createTextNode($attr_item->attr_value_id));
								$TSAdvertisingID = $row2->appendChild($xml->createElement('TSAdvertisingID'));
								$SiteAdvertisingID = $row2->appendChild($xml->createElement('SiteAdvertisingID'));
								$SiteAdvertisingID->appendChild($xml->createTextNode($object_id->record_id));//$attr_item->id
								$TypeDataID = $row2->appendChild($xml->createElement('TypeDataID'));
								$TypeDataID->appendChild($xml->createTextNode($attr_item->type));
								$Command = $row2->appendChild($xml->createElement('Command'));								
								$TSAttributeID = $row2->appendChild($xml->createElement('TSAttributeID'));
								
								if (isset($attr_item->id_tr))
								{
									$TSAttributeID->appendChild($xml->createTextNode($attr_item->id_tr));               
								}
								
								$SiteAttributeID = $row2->appendChild($xml->createElement('SiteAttributeID'));
								
								if (isset($attr_item->attribute_id))
								{
									$SiteAttributeID->appendChild($xml->createTextNode($attr_item->attribute_id));
								}
								
								$mCount++;
							}
						}
					}
				} 
			} //foreach $arrIDs
			
			$advertising_editions = $sync->appendChild($xml->createElement('advertising_editions'));

			foreach($arrIDs as $xkey=>$object_id)
			{
				$Publications = ORM::factory('Object_Region')
						->where('object', '=', $object_id->record_id)
						->find_all();
				
				if($Publications->count())
				{
					foreach($Publications as $p_item)
					{
						$row = $advertising_editions->appendChild($xml->createElement('row'));
						$row->setAttribute("advNum", $xkey);
						$TSAdvertisingID2 = $row->appendChild($xml->createElement('TSAdvertisingID'));
						$SiteAdvertisingID2 = $row->appendChild($xml->createElement('SiteAdvertisingID'));
						$SiteAdvertisingID2->appendChild($xml->createTextNode($object_id->record_id));
						$EditionID = $row->appendChild($xml->createElement('EditionID'));
						$EditionID->appendChild($xml->createTextNode($p_item->region));
					}
				}
			}
			
			$viaSMSAdverts = $sync->appendChild($xml->createElement('via_sms_adverts'));
			$smsObjects = ORM::factory('Object_Sms')
								->where('id', 'in', DB::select('record_id')
													->from('temp_objects')
													->where('tablename', '=', 'sms_object')
													->where('status', '=', 0)
								)->find_all();			

			if ($smsObjects->count())
			{
				foreach($smsObjects as $xkey => $object_id)
				{
					$row = $viaSMSAdverts->appendChild($xml->createElement('row'));
					$row->setAttribute("advSMSNum", $xkey);

					$SMSAdvertisingNumber= $row->appendChild($xml->createElement('Number'));
					$SMSAdvertisingNumber->appendChild($xml->createTextNode($object_id->number));

					$SMSAdvertisingShortNumber= $row->appendChild($xml->createElement('ShortNumber'));
					$SMSAdvertisingShortNumber->appendChild($xml->createTextNode($object_id->short_number));

					$SMSAdvertisingText= $row->appendChild($xml->createElement('Text'));
					$SMSAdvertisingText->appendChild($xml->createTextNode($object_id->text));

					$SMSAdvertisingKeyword= $row->appendChild($xml->createElement('KeyWord'));
					$SMSAdvertisingKeyword->appendChild($xml->createTextNode($object_id->keyword));              
					
					DB::update('temp_objects')
							->set(array('status' => 1))
							->where('record_id', '=', $object_id->id)
							->execute();
				}
			}
			
			$xml->formatOutput = true;
			$xml->save(EXPORT_PATH.'data.xml');
			DB::update('temp_objects')
					->set(array('status' => 1, 'date_status_change' => 'now()'))
					->where('record_id', 'in', $ids)					
					->execute();			
			
		} //if ($arrIDs->count())
		
		//получаем объявления со статусом 3 для записи в validation.xml
		$arrIDs2 = ORM::factory('Temp_Objects')->where('status', '=', 3)->find_all();
		$ids2 = array();
		
		if ($arrIDs2->count())
		{
			$validation_xml=new DomDocument('1.0','utf-8');
			$validation = $validation_xml->appendChild($validation_xml->createElement('validation'));
			$attributes2 = $validation->appendChild($validation_xml->createElement('attributes'));
			$values2 = $validation->appendChild($validation_xml->createElement('values'));
			$advertisings2 = $validation->appendChild($validation_xml->createElement('advertisings'));

			foreach($arrIDs2 as $xkey2 => $object_id2)
			{
				$ids2[] = $object_id2->record_id;
				$advertising2 = $advertisings2->appendChild($validation_xml->createElement('advertising'));
				$advertising2->setAttribute("Num", ($xkey2+1));
				$TSAdvertisingID2 = $advertising2->appendChild($validation_xml->createElement('TSAdvertisingID'));
				$TSAdvertisingID2->appendChild($validation_xml->createTextNode($object_id2->ts_id));
				$SiteAdvertisingID2 = $advertising2->appendChild($validation_xml->createElement('SiteAdvertisingID'));
				$SiteAdvertisingID2->appendChild($validation_xml->createTextNode($object_id2->record_id));
			}

			$validation_xml->formatOutput = true;
			$validation_xml->save(EXPORT_PATH.'validation.xml');
		    
			//устанавливаем статус 4
			DB::update('temp_objects')
					->set(array('status' => 4, 'date_status_change' => 'now()'))
					->where('record_id', 'in', $ids2)					
					->execute();		   
		}		
		
    } //_execute
}