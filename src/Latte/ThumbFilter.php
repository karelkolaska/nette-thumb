<?php

namespace NetteThumb\Latte;

use NetteThumb\ThumbGenerator;

/**
 * Latte filter, that submites given properties to ThumbGenerator
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class ThumbFilter
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
	 * @param array $properties
	 * @return string
	 */
	public function __invoke($origPath, $properties = array())
	{
		return $this->thumbGenerator->setOrigPath($origPath)
						->setProperties($properties)
						->getThumbPath();	
	}
}
