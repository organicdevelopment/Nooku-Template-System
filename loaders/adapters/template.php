<?php
/**
 * User: Oli Griffiths
 * Date: 24/06/2012
 * Time: 13:47
 */

class TmplKoowaLoaderAdapterTemplate extends KLoaderAdapterAbstract
{
	/**
	 * The adapter type
	 *
	 * @var string
	 */
	protected $_type = 'tmpl';

	/**
	 * The class prefix
	 *
	 * @var string
	 */
	protected $_prefix = 'Tmpl';

	/**
	 * Get the path based on a class name
	 *
	 * @param  string		  	The class name
	 * @return string|false		Returns the path on success FALSE on failure
	 */
	public function findPath($classname, $basepath = null)
	{
		$path = false;

		$word  = strtolower(preg_replace('/(?<=\\w)([A-Z])/', ' \\1', $classname));
		$parts = explode(' ', $word);

		if (array_shift($parts) == 'tmpl')
		{
			//Switch the basepath
			if(!empty($basepath)) {
				$this->_basepath = $basepath;
			}

			$basepath = $this->_basepath;

			$template = strtolower(array_shift($parts));

			if($template == 'koowa') $basepath = JPATH_ROOT;

			if(empty($parts)) $path = 'template';
			else{
				$file = strtolower(array_pop($parts));
				foreach($parts AS &$part) $part = KInflector::pluralize(strtolower($part));
				$path = implode('/', $parts).(count($parts) ? '/' : '').$file;
			}

			$path = $basepath.'/templates/'.$template.'/'.$path.'.php';

		}

		return $path;

	}
}
