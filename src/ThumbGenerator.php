<?php

namespace NetteThumb;

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
	protected $flag = Image::FILL;
	
	/** @var string */
	protected $watermark;
	
	/** @var numeric */
	protected $watermarkLeft = '50%';
	
	/** @var numeric */
	protected $watermarkTop = '50%';
	
	/** @var int */
	protected $watermarkOpacity = 100;
	
	/** @var array */
	protected $options;	
	
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
	 * @param array $options
	 * @return ThumbGenerator
	 */
	public function setOptions($options)
	{		
		$this->options = $options;
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
		if (!file_exists($origPath) && !Strings::startsWith($origPath, $this->wwwDir)) {
			$origPath = $this->wwwDir . $origPath;
		}
		
		if (!file_exists($origPath)) {
			throw new Exception('File "' . $origPath . '" not found.');
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
	 * @param string $optionKey
	 * @return ThumbGenerator
	 * @throws Exception
	 */
	public function loadOption($optionKey)
	{
		if (!array_key_exists($optionKey, $this->options)) {
			throw new Exception('Option "' . $optionKey . '" is not defined.');
		}
				
		$this->setProperties($this->options[$optionKey]);
		return $this;
	}	
	
	/**
	 * 
	 * @param array $properties
	 * @return ThumbGenerator
	 */
	public function setProperties($properties)
	{
		if (isset($properties['width'])) {
			$this->width = $properties['width'];
		}
		if (isset($properties['height'])) {
			$this->height = $properties['height'];
		}
		if (isset($properties['flag'])) {
			$this->flag = constant('Nette\Utils\Image::' . Strings::upper($properties['flag']));
		}
		if (isset($properties['title'])) {
			$this->title = $properties['title'];
		}		
		if (isset($properties['watermark'])) {
			if (!file_exists($properties['watermark'])) {
				$properties['watermark'] = $this->wwwDir . $properties['watermark'];
			}
			$this->watermark = $properties['watermark'];			
		}
		if (isset($properties['watermarkLeft'])) {
			$this->watermarkLeft = $properties['watermarkLeft'];
		}
		if (isset($properties['watermarkTop'])) {
			$this->watermarkTop = $properties['watermarkTop'];
		}
		if (isset($properties['watermarkOpacity'])) {
			$this->watermarkOpacity = $properties['watermarkOpacity'];
		}

		return $this;
	}
	
	/**
	 * 
	 * 
	 */
	public function getThumbPath()
	{
		$pathinfo = pathinfo($this->origPath);
		$suffix = ($this->title ? Strings::webalize($this->title) . '-' : '') . ($this->width ? : '0') . 'x' . ($this->height ? : '0');
		$thumbpath = $this->thumbDir . '/' . $pathinfo['filename'] . '-' . $suffix . '.' . $pathinfo['extension'];
				
		if ($this->debugMode || !file_exists($thumbpath) || filemtime($this->origPath) > filemtime($thumbpath)) {
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
		$image = Image::fromFile($this->origPath);
		$image->resize($this->width, $this->height, $this->flag);
		
		if ($this->watermark) {
			$watermark = Image::fromFile($this->watermark);
			$image->place($watermark, $this->watermarkLeft, $this->watermarkTop, $this->watermarkOpacity);
		}
		
		$image->sharpen();
		$image->save($thumbpath, 100);		
	}
}
