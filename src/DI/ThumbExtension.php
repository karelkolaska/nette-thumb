<?php

namespace KarelKolaska\NetteThumb\DI;

use Nette\DI\CompilerExtension;

/**
 * Thumb Extension
 * 
 * @author Karel Koláska <karel@kolaska.cz>
 */
class ThumbExtension extends CompilerExtension
{
	/** @var array */
	private static $configDefaults = [
		'wwwDir' => '%wwwDir%',				
		'thumbDir' => NULL,
		'notFoundImage' => NULL,
		'debugMode' => FALSE
	];

	/**
	 * 
	 * 
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig(self::$configDefaults);
		
		// ThumbGenerator
		$builder->addDefinition($this->prefix('thumbGenerator'))
			->setClass('KarelKolaska\NetteThumb\ThumbGenerator')
			->addSetup('setWwwDir', [$config['wwwDir']])
			->addSetup('setThumbDir', [$config['thumbDir']])
			->addSetup('setDebugMode', [$config['debugMode']])
			->addSetup('setNotFoundImage', [$config['notFoundImage']])
			->addSetup('setDefaultProps', [$config['defaultProps']]);

		// Latte filters
		$builder->addDefinition($this->prefix('latte.thumbFilter'))
			->setClass('KarelKolaska\NetteThumb\Latte\ThumbFilter');
		$builder->addDefinition($this->prefix('latte.thumbPropsFilter'))
			->setClass('KarelKolaska\NetteThumb\Latte\ThumbPropsFilter');		

		$builder->getDefinition('nette.latteFactory')			
			->addSetup('addFilter', ['thumb', '@' . $this->prefix('latte.thumbFilter')])
			->addSetup('addFilter', ['thumbProps', '@' . $this->prefix('latte.thumbPropsFilter')]);
	}
}
