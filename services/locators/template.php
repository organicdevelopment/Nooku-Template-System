<?php
/**
 * Service Locator for a template
 *
 * @author		Oli Griffiths <oli@organic-development.com>
 * @package     Koowa_Service
 * @subpackage 	Locator
 */
class TmplKoowaServiceLocatorTemplate extends KServiceLocatorAbstract
{
	/**
	 * The type
	 *
	 * @var string
	 */
	protected $_type = 'tmpl';

	/**
	 * Get the classname based on an identifier
	 *
	 * This locator will try to create an generic or default classname on the identifier information
	 * if the actual class cannot be found using a predefined fallback sequence.
	 *
	 * Fallback sequence : -> Named Module Specific
	 *                     -> Named Module Default
	 *                     -> Default Module Specific
	 *                     -> Default Module Default
	 *                     -> Framework Specific
	 *                     -> Framework Default
	 *
	 * @param mixed  		 An identifier object - mod:[//application/]module.[.path].name
	 * @return string|false  Return object on success, returns FALSE on failure
	 */
	public function findClass(KServiceIdentifier $identifier)
	{
		$path = KInflector::camelize(implode('_', $identifier->path));
		$classname = 'Tmpl'.ucfirst($identifier->package).$path.ucfirst($identifier->name);

		//Don't allow the auto-loader to load module classes if they don't exists yet
		if (!$this->getService('koowa:loader')->loadClass($classname, $identifier->basepath))
		{
			if(!$identifier->name) $identifier->name = 'template';

			$path = '';
			foreach($identifier->path AS $p) $path .= ucfirst($p);

			/*
			 * Find the classname to fallback too and auto-load the class
			 *
			 * Fallback sequence : -> Named Template Specific
			 *                     -> Named Template Default
			 *                     -> Default Template Specific
			 *                     -> Default Template Default
			 *                     -> Framework Specific
			 *                     -> Framework Default
			 */
			if(class_exists('Tmpl'.ucfirst($identifier->package).$path.ucfirst($identifier->name))) {
				$classname = 'Tmpl'.ucfirst($identifier->package).$path.ucfirst($identifier->name);
			} elseif(class_exists('Tmpl'.ucfirst($identifier->package).$path.'Default')) {
				$classname = 'Tmpl'.ucfirst($identifier->package).$path.'Default';
			} elseif(class_exists('TmplKoowa'.$path.ucfirst($identifier->name))) {
				$classname = 'TmplKoowa'.$path.ucfirst($identifier->name);
			} elseif(class_exists('TmplKoowa'.$path.'Default')) {
				$classname = 'TmplKoowa'.$path.'Default';
			} elseif(class_exists('K'.$path.ucfirst($identifier->name))) {
				$classname = 'K'.$path.ucfirst($identifier->name);
			} elseif(class_exists('K'.$path.'Default')) {
				$classname = 'K'.$path.'Default';
			} else {
				$classname = false;
			}
		}

		return $classname;
	}

	/**
	 * Get the path based on an identifier
	 *
	 * @param  object  	An identifier object - mod:[//application/]module.[.path].name
	 * @return string	Returns the path
	 */
	public function findPath(KServiceIdentifier $identifier)
	{
		$path  = '';
		$parts = $identifier->path;
		$name  = $identifier->package;

		if(!empty($identifier->name))
		{
			if(count($parts))
			{
				$path    = KInflector::pluralize(array_shift($parts)).
					$path   .= count($parts) ? '/'.implode('/', $parts) : '';
				$path   .= '/'.strtolower($identifier->name);
			}
			else $path  = strtolower($identifier->name);
		}

		$path = $identifier->basepath.'/templates/'.$name.'/'.$path.'.php';
		return $path;
	}
}