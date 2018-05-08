<?php

namespace KarelKolaska\NetteThumb;

use Nette\Utils\Image,
	Nette\Utils\Strings;
use Exception;

/**
 * Generates thumbnail by given properties
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class ThumbGenerator
{
	/** @var bool */
	protected $debugMode = FALSE;
	
	/** @var string */
	protected $wwwDir;
	
	/** @var string */
	protected $thumbDir;	
	
	/** @var string */
	protected $origPath;
	
	/** @var string */
	protected $title;	
	
	/** @var int */
	protected $width;
	
	/** @var int */
	protected $height;
	
	/** @var int */
	protected $flag;
	
	/** @var string */
	protected $watermark;
	
	/** @var numeric */
	protected $watermarkLeft;
	
	/** @var numeric */
	protected $watermarkTop;
	
	/** @var int */
	protected $watermarkOpacity;
	
	/** @var string */
	protected $notFoundImage;	
	
	/** @var array */
	protected $defaultProps;	
	
	/**
	 * 
	 * @param bool $debugMode
	 * @return ThumbGenerator
	 */
	public function setDebugMode($debugMode)
	{		
		$this->debugMode = $debugMode;
		return $this;
	}
	
	/**
	 * 
	 * @param string $wwwDir
	 * @return ThumbGenerator
	 */
	public function setWwwDir($wwwDir)
	{		
		$this->wwwDir = $wwwDir;
		return $this;
	}
	
	/**
	 * 
	 * @param string $thumbDir
	 * @return ThumbGenerator
	 */
	public function setThumbDir($thumbDir)
	{		
		$this->thumbDir = $thumbDir;
		return $this;
	}
	
	/**
	 * 
	 * @param string $notFoundImage
	 * @return ThumbGenerator
	 */
	public function setNotFoundImage($notFoundImage)
	{		
		$this->notFoundImage = $notFoundImage;
		return $this;
	}	
	
	/**
	 * 
	 * @param array $defaultProps
	 * @return ThumbGenerator
	 */
	public function setDefaultProps($defaultProps)
	{		
		$this->defaultProps = $defaultProps;
		return $this;
	}	
	
	/**
	 * 
	 * @param string $origPath
	 * @return ThumbGenerator
	 * @throws Exception
	 */
	public function setOrigPath($origPath)
	{		
		if (!file_exists($origPath) && file_exists($this->wwwDir . $origPath)) {
			$origPath = $this->wwwDir . $origPath;
		}
		
		$this->origPath = $origPath;
		return $this;
	}
	
	/**
	 * 
	 * @param string $title
	 * @return ThumbGenerator
	 */
	public function setTitle($title)
	{		
		$this->title = $title;
		return $this;
	}	
	
	/**
	 * 
	 * @param string $propsKey
	 * @return ThumbGenerator
	 * @throws Exception
	 */
	public function loadPropsFromDefault($propsKey)
	{
		if (!array_key_exists($propsKey, $this->defaultProps)) {
			throw new Exception('Default properties for key "' . $propsKey . '" are not defined.');
		}
				
		$this->setProperties($this->defaultProps[$propsKey]);
		return $this;
	}	
	
	/**
	 * 
	 * @param array $properties
	 * @return ThumbGenerator
	 */
	public function setProperties($properties)
	{
		// Thumbnail width
		if (isset($properties['width'])) {
			$this->width = $properties['width'];
		} else {
			$this->width = NULL;
		}
		
		// Thumbnail height
		if (isset($properties['height'])) {
			$this->height = $properties['height'];
		} else {
			$this->height = NULL;
		}
		
		// Resize flag
		if (isset($properties['flag'])) {
			$this->flag = constant('Nette\Utils\Image::' . Strings::upper($properties['flag']));
		} else {
			$this->flag = Image::FIT;
		}
		
		// Image SEO title
		if (isset($properties['title'])) {
			$this->title = $properties['title'];
		} else {
			$this->title = NULL;
		}
		
		// Watermark Image
		if (isset($properties['watermark'])) {
			if (!file_exists($properties['watermark'])) {
				$properties['watermark'] = $this->wwwDir . $properties['watermark'];
			}
			$this->watermark = $properties['watermark'];			
		} else {
			$this->watermark = NULL;
		}
		
		// Watermark Left Position
		if (isset($properties['watermarkLeft'])) {
			$this->watermarkLeft = $properties['watermarkLeft'];
		} else {
			$this->watermarkLeft = '50%';
		}
		
		// Watermark Top Position
		if (isset($properties['watermarkTop'])) {
			$this->watermarkTop = $properties['watermarkTop'];
		} else {
			$this->watermarkTop = '50%';
		}
		
		// Watermark Opacity
		if (isset($properties['watermarkOpacity'])) {
			$this->watermarkOpacity = $properties['watermarkOpacity'];
		} else {
			$this->watermarkOpacity = 100;
		}

		return $this;
	}
	
	/**
	 * 
	 * @return string
	 * @throws Exception
	 */
	public function getThumbPath()
	{
		if (!file_exists($this->thumbDir)) {
			throw new Exception('Thumb directory "' . $this->thumbDir . '" doesn\'t exists.');
		}
		
		$pathinfo = pathinfo($this->origPath);
		$suffix = ($this->title ? Strings::webalize($this->title) . '-' : '') . ($this->width ? : '0') . 'x' . ($this->height ? : '0');
		$thumbpath = $this->thumbDir . '/' . $pathinfo['filename'] . '-' . $suffix . '.' . $pathinfo['extension'];
				
		if ($this->debugMode || !file_exists($thumbpath) || (file_exists($this->origPath) && filemtime($this->origPath) > filemtime($thumbpath))) {
			$this->generateThumb($thumbpath);
		}
		
		return str_replace($this->wwwDir, '', $thumbpath);
	}
	
	/**
	 * 
	 * @param string $thumbpath
	 */
	protected function generateThumb($thumbpath)
	{
		if (file_exists($this->origPath)) {
			$image = Image::fromFile($this->origPath);
		} else if (!file_exists($this->origPath) && file_exists($this->notFoundImage)) {
			$image = Image::fromFile($this->notFoundImage);
		} else {
			$image = Image::fromBlank(300, 300, Image::rgb(230, 230, 230));
		}
		
		$image->resize($this->width, $this->height, $this->flag);
		
		if ($this->watermark) {
			$watermark = Image::fromFile($this->watermark);
			$image->place($watermark, $this->watermarkLeft, $this->watermarkTop, $this->watermarkOpacity);
		}
		
		$image->sharpen();
		$image->save($thumbpath, 100);		
	}
}
