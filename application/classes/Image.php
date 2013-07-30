<?php
/**
 * управлениe картинками
 * @author tenzor
 *
 */
class Image {

    const IMAGE_PATH = '/uploads';

	private static $sizes = array(
		//'54x34' => array(
			//'width' => 54,
			//'height' => 34,
			//'fit' => 1,
			//'keep_aspect_ratio' => 1,
			//'crop' =>0
        //),
        //'65x65' => array(
			//'width' => 65,
			//'height' => 65,
			//'fit' => 1,
			//'keep_aspect_ratio' => 1,
			//'crop' =>0
        //),
        //'80x80' => array(
			//'width' => 80,
			//'height' => 80,
			//'fit' => 1,
			//'keep_aspect_ratio' => 1,
			//'crop' =>0
        //),
        //'125x83' => array(
			//'width' => 125,
			//'height' => 83,
			//'fit' => 1,
			//'keep_aspect_ratio' => 1,
			//'crop' =>0
        //),
        '120x90' => array(
			'width' => 120,
			'height' => 90,
			'fit' => 1,
			'keep_aspect_ratio' => 1,
			'crop' =>0
        ),
        '272x203' => array(
			'width' => 272,
			'height' => 203,
			'fit' => 1,
			'keep_aspect_ratio' => 1,
			'crop' =>0
        ),
        '1280x292' => array(
			'width' => 1280,
			'height' => 292,
			'fit' => 1,
			'keep_aspect_ratio' => 1,
			'crop' =>1
        ),
        //'433x289' => array(
			//'width' => 433,
			//'height' => 289,
			//'fit' => 1,
			//'keep_aspect_ratio' => 1,
			//'crop' =>0
        //),
	);

	const max_uploaded_image_size = 5242880; //8 MB

	/**
	 * Файл водяного знака
	 * @var string 
	 */
	private $watermark = 'watermark.png';
	/**
	 * Оригинальный массив из $_FILE
	 * @var array
	 */
	private $file = null;
	/**
	 * Расширение файла
	 * @var string
	 */
	private $ext = '';
	/**
	 * Массив, полученный из getimagesize()
	 * @var фккфн
	 */
	private $size;

	/**
	 * высота превьюшки
	 * @var int
	 */
	private $new_h;
	/**
	 * ширина превьюшки
	 * @var int
	 */
	private $new_w;

	/**
	 * Тип файла. GIF, JPEG, PNG
	 * @var string
	 */
	private $filetype;

	/**
	 * Ресурс с созданной картинкой
	 * @var resource
	 */
	private $thumbnail;

	/**
	 * Новое имя картинки и превьюшки (с расширением)
	 * @var string
	 */
	private $image_filename;

	/**
	 * Ресайзить оригинальный файл, если размеры указаны.
	 * Иначе записывается исходный файл.
	 */
	private $original_resize = array('width' => 800, 'height' => 600);
	
	private $is_uploaded_file = true;

	public function __construct($is_uploaded_file = true) {
		$this->is_uploaded_file = $is_uploaded_file;
	}

	/**
	 * Сделать миниатюру для картинки
	 * @param $file
	 * @return string имя файла картинки
	 */
	public function makeThumbnail( $file ) {

		$this->checkFile($file);

		$this->saveOriginal();

		if ($this->original_resize) 
		{
		   list($image_w, $image_h) = GetImageSize($this->getOriginalPath( $this->image_filename ));  
		}
		else 
		{
		    $image_w = $this->size[0];
		    $image_h = $this->size[1];		    
		}


		$imageCreateFrom = "imagecreatefrom" . $this->filetype;
		$src = $imageCreateFrom( $this->getOriginalPath( $this->image_filename ) );

		foreach (self::$sizes as $name => $s) {

			if ( $s['fit'] ) { //Вместить в рамку нужного размера

				$this->thumbnail = imagecreatetruecolor ($s['width'], $s['height']);

				if ( $s['keep_aspect_ratio'] ) { //Сохранять пропорции оригинала

				    if ( $s['crop'] ) {
					    
					    $thumb_ratio = ($s['width'] > $s['height']) ? $s['height']/$s['width'] : $s['width']/$s['height'];
					    $this->new_w = $image_w;
					    $this->new_h = $image_h;
					    
					    					    
					    if ($image_w > $image_h)
					    {	
						$w_border = $image_h / $thumb_ratio;
						$h_border = $image_w * $thumb_ratio;
						
						if ($w_border > $image_w)
						{
						    if ($h_border < $image_h)//1024x768, 1280x1024
						    {  
							$this->new_h = ceil($this->new_w * $thumb_ratio);
						    }
						}//if ($w_border > $image_w)
						elseif ($w_border < $image_w)
						{
						    if ($h_border > $image_h)//1386x768
						    {
							$this->new_w = ceil($image_h / $thumb_ratio);//w>h
						    }
						}//elseif ($w_border < $image_w)      						
					    }
					    else //($image_w <= $image_h)
					    {						
						$w_border = $image_h * $thumb_ratio;
						$h_border = $image_w / $thumb_ratio;	
						
						if ($w_border > $image_w)
						{
						    if ($h_border < $image_h)
						    {  	
							$this->new_h = ceil($this->new_w * $thumb_ratio);							
						    }
						}//if ($w_border > $image_w)
						elseif ($w_border < $image_w)
						{
						    if ($h_border > $image_h)
						    {
							$this->new_h = ceil($this->new_w * $thumb_ratio);
							
						    }
						}//elseif ($w_border < $image_w)
					    }					    		 
					    					    
					    $white = imagecolorallocate($this->thumbnail,255,255,255);
					    imagefilledrectangle($this->thumbnail, 0, 0, $s['width'], $s['height'], $white);					    
					    imageCopyResampled(
						    $this->thumbnail, $src, //куда, откуда
						    0, 0, //dst_x, dst_y - позиция начала
						    0, 0, //src_x, src_y - позиция начала на оригинале
						    $s['width'], $s['height'], //ширина и высота на миниатюре
						    $this->new_w, $this->new_h //ширина и высота на оригинале
					    );					    
					}
				    
				    else {
    
					    //Рамки
					    $width = $s['width'];
					    $height = $s['height'];			
					    $ratio_orig = $image_w/$image_h;

					    if ($image_w < $width and $image_h < $height)
					    {
						$width = $image_w;
						$height = $image_h;
					    }
					    else
						if ($width/$height > $ratio_orig) 
						{
						      $width = $height*$ratio_orig;
						} 
						else 
						{
						      $height = $width/$ratio_orig; 
						}

					    $this->new_w = $width;
					    $this->new_h = $height;	

					    $this->thumbnail = imagecreatetruecolor ($this->new_w, $this->new_h);
					    imageCopyResampled($this->thumbnail, $src, 0,  0,  0, 0, $this->new_w, $this->new_h, $image_w, $image_h);
				    }	

				} else { //Не сохранять пропорции оригинала
					$this->new_h = $s['height'];
					$this->new_w = $s['width'];
					imageCopyResampled($this->thumbnail, $src, 0,  0,  0, 0, $this->new_w, $this->new_h, $this->size[0], $this->size[1]);
				}

			} else {

				if ( $s['keep_aspect_ratio'] ) {
					if ( $image_w > $s['width'] || $image_h > $s['height'] ) { //Если надо уменьшить

						$ratio_h = $s['height'] / $image_h;
						$ratio_w = $s['width'] / $image_w;

						if ( $ratio_h < $ratio_w ) {
							$ratio = $ratio_h;
						} else {
							$ratio = $ratio_w;
						}

						//$ratio = floor($ratio);

						$image_h = floor($image_h * $ratio);
						$image_w = floor($image_w * $ratio);

					} else {//оставляем как есть
						//
					}
				} else {
					if ( $image_w > $s['width'] || $image_h > $s['height'] ) {
						$image_h = $s['height'];
						$image_w = $s['width'];
					}
				}

				$this->new_h = $image_h;
				$this->new_w = $image_w;

				$this->thumbnail = imagecreatetruecolor ($this->new_w, $this->new_h);
				imageCopyResampled($this->thumbnail, $src, 0,  0,  0, 0, $this->new_w, $this->new_h, $this->size[0], $this->size[1]);

			}

			$this->saveThumbnail($name);

		}

		return $this->image_filename;

	}

	/**
	 * Проверить файл на всякие страшные вещи.
	 * @param $file
	 */
	private function checkFile( $file ) {

		if ( $this->is_uploaded_file && !is_uploaded_file($file['tmp_name']) ) {
			throw new Exception("Нет загруженного файла", 400);
		}

		if ( !isset($file) || $file["size"] < 1 ) {
			throw new Exception("Нулевой размер файла", 417);
		}

		if ( $file["size"] > self::max_uploaded_image_size ) {
			throw new Exception("Извините, файл слишком большой для загрузки",2);
		}

		$this->file = $file;

		if ( !($this->size = GetImageSize( $file["tmp_name"] ) ) ) {
			throw new Exception("Не верный формат файла. Возможно это не картинка.", 409);
		}
		if ( !in_array($this->size[2], array(1, 2, 3, 7, 8) ) )  {
			throw new Exception("Не верный формат файла. Возможно это не картинка.", 5);
		}

		switch ( $this->size[2] ) {
			case 1:
				$this->filetype = "gif";
				break;

			case 2:
				$this->filetype = "jpeg";
				break;

			case 3:
				$this->filetype = "png";
				break;

			default:
				throw new Exception("Не верный формат файла. Возможно это не картинка.", 5);
		}

        $filename = $file["name"];
		$i = strrpos($filename, ".");
		if ( $i !== false ) {
			$ext = strtolower(substr($filename, $i));
		}

        if ( empty($ext) || ($ext != ".gif" && $ext != ".jpg" && $ext != ".jpeg" && $ext != ".png") ) {
            if ( $this->filetype == 'jpeg' ) {
                $this->ext = 'jpg';
            } else {
                $this->ext = $this->filetype;
            }

            throw new Exception("Не верное расширение файла. Поддерживаются форматы gif, jpg, png.",3);
        }

        $this->ext = $ext;
	}


	/**
	 * Уменьшить и сохранить оригинал картинки
	 */
	private function saveOriginal() {

		//Делаем уникальное имя файлу
		do {

			$filename = md5(uniqid(""));

			$tgtfile   = self::getOriginalPath( $filename . $this->ext);

			$folder1 = substr($filename, 0, 2);
			$folder2 = substr($filename, 2, 2);
			$folder3 = substr($filename, 4, 2);

		} while (file_exists($tgtfile) || $folder1 == "ad" || $folder2 == "ad" || $folder3 == "ad");

		//Записываем имя
		$this->image_filename = $filename . $this->ext;

		//Создаем директории для оригинала
		mkdir(dirname($tgtfile), 0777, true);

		//Перемещаем оригинал
//		if ( $this->is_uploaded_file ) {
//			move_uploaded_file($this->file["tmp_name"], $tgtfile);
//		} else {
//			copy($this->file['tmp_name'], $tgtfile);
//		}
		
		if ( $this->is_uploaded_file )
		    $new_original = $this->resizeOriginal($this->file["tmp_name"]);
		else 
		    copy($this->file['tmp_name'], $tgtfile);
		
		if (file_exists($this->file["tmp_name"])) unlink($this->file["tmp_name"]);
		
//		log_message('debug', 'tmp_file: '.$this->file["tmp_name"]);
//		log_message('debug', 'tgtfile: '.$tgtfile);
		
		$Image = "Image" . $this->filetype;
		$Image($new_original, $tgtfile );
		ImageDestroy($new_original);

	}
	
	/**
	 * Ресайз оригинальной фотографии.
	 * @return src
	 */
	private function resizeOriginal($filename)
	{
	    if (!$this->original_resize) return false;

	    //list($width, $height) = $this->original_resize;
	    $width = $this->original_resize['width'];
	    $height = $this->original_resize['height'];
	    
	    list($width_orig, $height_orig) = getimagesize($filename);	    

	    $ratio_orig = $width_orig/$height_orig;

	    if ($width_orig < $width and $height_orig < $height)
	    {
		$width = $width_orig;
		$height = $height_orig;
	    }
	    else
		if ($width/$height > $ratio_orig) {
		   $width = $height*$ratio_orig;
		} else {
		   $height = $width/$ratio_orig; 
	    }

	    $imageCreateFrom = "imagecreatefrom" . $this->filetype;
	    $src = $imageCreateFrom( $filename );
	    $image_p = imagecreatetruecolor($width, $height);

	    $white = imagecolorallocate($image_p,255,255,255);
	    imagefilledrectangle($image_p, 0, 0, $width, $height, $white);	    
	    imagecopyresampled($image_p, $src, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
	    
		//Наложение водяного знака
		if (Kohana::$config->load('images.watermark'))
		{

			$stamp = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/images/'.$this->watermark);
			$sx = imagesx($stamp);
			$sy = imagesy($stamp);

			if ($width >= 400)
				imagecopy($image_p, $stamp, $width/2 - $sx/2, $height/2 - $sy/2, 0, 0, $sx, $sy);
		}
		
		return $image_p;
	    
	}

	/**
	 * Получить путь в системе до оригинальной уменьшенной картинки по имени файла
	 * @param $filename
	 * @return string
	 */
	public static function getOriginalPath($filename) {
		return '.' . self::getOriginalSitePath($filename);
	}

	/**
	 * Получить путь для ссылки до оригинальной картинки по имени файла
	 * @param $filename string
	 * @return string
	 */
	public static function getOriginalSitePath($filename) {
		$folder1 = substr($filename, 0, 2);
		$folder2 = substr($filename, 2, 2);
		$folder3 = substr($filename, 4, 2);

		return self::IMAGE_PATH . '/orig/' . $folder1 . '/' . $folder2 . '/' . $folder3 . '/' . $filename;
	}

	/**
	 * Получить путь в системе до уменьшенной картинки по имени файла
	 * @param $filename string
	 * @param $type string тип картинки
	 * @return string путь
	 */
	public static  function getThumbnailPath($filename, $type)
	{
		return '.' . self::getThumbnailSitePath($filename, $type);
	}

	/**
	 * Получить путь для ссылки до уменьшенной картинки
	 * типа type по имени файла
	 * @param $filename string имя файла
	 * @param $type string тип картинки
	 * @return string
	 */
	
	private static function getThumbnailSitePath($filename, $type)
	{
		$folder1 = substr($filename, 0, 2);
		$folder2 = substr($filename, 2, 2);
		$folder3 = substr($filename, 4, 2);

		return self::IMAGE_PATH . '/'. $type . '/' . $folder1 . '/' . $folder2 . '/' . $folder3 . '/' . $filename;
	}
	
	public static  function getSitePaths($filename) {
		$res = array(
			'original' => self::getOriginalSitePath($filename)
		);

		foreach (self::$sizes as $k => $v) {
			$res[$k] = self::getThumbnailSitePath($filename, $k);
		}

		return $res;
	}

    public static function getSavePaths($filename) {
		$res = array(
			'original' => self::getOriginalPath($filename)
		);

		foreach (self::$sizes as $k => $v) {
			$res[$k] = self::getThumbnailPath($filename, $k);
		}

		return $res;
	}

	/**
	 * Сохранить картинку-миниатюру
	 */
	private function saveThumbnail($type) {

		$tgtfile = self::getThumbnailPath( $this->image_filename, $type );

		//Создаем директории
		@mkdir(dirname($tgtfile), 0777, true);

		$Image = "Image" . $this->filetype;
		$Image($this->thumbnail, $tgtfile );
		ImageDestroy($this->thumbnail);
	}

	public function deleteImage($filename)
	{
		foreach (self::getSitePaths($filename) as $filepath)
		{
			if (file_exists(".".$filepath))
			{
				unlink(".".$filepath);
			}
		}
	}

	public function getSizes()
	{
		return array_keys(self::$sizes);
	}
	
	public function makeThumbnailLogo( $file ) {

		$this->checkFile($file);

		$this->saveOriginal();

		//Исходные размеры картинки в зависимости от того, ресайзится ли оригинал
		if ($this->original_resize) 
		{
		   list($image_w, $image_h) = GetImageSize($this->getOriginalPath( $this->image_filename ));  
		}
		else 
		{
		    $image_w = $this->size[0];
		    $image_h = $this->size[1];		    
		}

		$imageCreateFrom = "imagecreatefrom" . $this->filetype;
		$src = $imageCreateFrom( $this->getOriginalPath( $this->image_filename ) );


		
		foreach (self::$sizes as $name => $s) 
		{		    
			//$this->thumbnail = imagecreatetruecolor ($s['width'], $s['height']);			  

			//Рамки
			$width = $s['width'];
			$height = $s['height'];			
			$ratio_orig = $image_w/$image_h;

			if ($image_w < $width and $image_h < $height)
			{
			    $width = $image_w;
			    $height = $image_h;
			}
			else
			    if ($width/$height > $ratio_orig) 
			    {
				  $width = $height*$ratio_orig;
			    } 
			    else 
			    {
				  $height = $width/$ratio_orig; 
			    }
			    
			$this->new_w = $width;
			$this->new_h = $height;	

			$this->thumbnail = imagecreatetruecolor ($this->new_w, $this->new_h);
			imageCopyResampled($this->thumbnail, $src, 0,  0,  0, 0, $this->new_w, $this->new_h, $image_w, $image_h);

			$this->saveThumbnail($name);

		}

		return $this->image_filename;
	    
	}
	
	public function set_original_resize(Array $sizes)
	{
		$this->original_resize = $sizes;
	}
	
}
