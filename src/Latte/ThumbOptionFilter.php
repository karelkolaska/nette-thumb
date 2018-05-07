<?php

namespace NetteThumb\Latte;

use NetteThumb\ThumbGenerator;

/**
 * Latte filter, that submites given predefined properties
 * from application settings to ThumbGenerator
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class ThumbOptionFilter
{
	/** @var ThumbGenerator */
	private $thumbGenerator;

	/**
	 * 
	 * @param ThumbGenerator $thumbGenerator
	 */
	public function __construct(ThumbGenerator $thumbGenerator)
	{
		$this->thumbGenerator = $thumbGenerator;
	}

	/**
	 * 
	 * @param string $origPath
	 * @param string $option
	 * @return string
	 */
	public function __invoke($origPath, $option = NULL, $title = NULL)
	{		
		return $this->thumbGenerator->setOrigPath($origPath)
					->setTitle($title)
					->loadOption($option)
					->getThumbPath();	
	}
}
