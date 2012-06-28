<?php
/**
 * User: Oli Griffiths
 * Date: 24/06/2012
 * Time: 14:34
 */

class TmplKoowaTemplate extends KTemplateDefault
{
	public function __construct(KConfig $config)
	{
		parent::__construct($config);

		//Set the template filters
		if(!empty($config->filters)) {
			$this->addFilter($config->filters);
		}

		//Add alias filter for media:// namespace
		$this->getFilter('alias')->append(
			array(
				'tmpl://' => $config->tmpl_url.'/',
				'media://' => $config->media_url.'/',
				'base://' => $config->base_url.'/',
				'@route(' => 'JRoute::_('
			),
			KTemplateFilter::MODE_READ | KTemplateFilter::MODE_WRITE
		);
	}

	/**
	 * Initializes the config for the object
	 *
	 * Called from {@link __construct()} as a first step of object instantiation.
	 *
	 * @param   object  An optional KConfig object with configuration options
	 * @return  void
	 */
	protected function _initialize(KConfig $config)
	{
		$identifier = $this->getIdentifier();
		$config->append(array(
			'base_url'         => KRequest::base(),
			'tmpl_url'         => KRequest::base().'/templates/'.$identifier->package,
			'media_url'        => KRequest::root().'/media',
			'filters' => array('shorttag', 'alias', 'variable', 'script', 'style', 'link', 'template'),
		));

		parent::_initialize($config);
	}



	public function render($layout = 'default', $data = array(), $process = true)
	{
		if(KRequest::get('request.tmpl','string')) $layout = KRequest::get('request.tmpl','string');
		$identifier = clone $this->getIdentifier();
		$identifier->name = $layout;
		return $this->loadIdentifier($identifier);
	}


	public function loadIdentifier($template, $data = array(), $process = true)
	{
		if(!isset($data['document'])) $data['document'] = JFactory::getDocument();

		if(is_array($data['document']->params)){
			$params = new JParameter('');
			$params->loadArray($data['document']);
			$data['document']->params = $params;
		}

		return parent::loadIdentifier($template, $data, $process);
	}



	/**
	 * Searches for the file
	 *
	 * This function first tries to find a template override, if no override exists
	 * it will try to find the default template
	 *
	 * @param	string	The file path to look for.
	 * @return	mixed	The full path and file name for the target file, or FALSE
	 * 					if the file is not found
	 */
	public function findFile($path)
	{
		$template  = JFactory::getApplication()->getTemplate();
		$override  = JPATH_THEMES.'/'.$template.'/html';
		$override .= str_replace(array(JPATH_BASE.'/modules', JPATH_BASE.'/components', '/views'), '', $path);

		//Try to load the template override
		$result = parent::findFile($override);

		if($result === false)
		{
			//If the path doesn't contain the /tmpl/ folder add it
			if(strpos($path, '/tmpl/') === false) {
				$path_tmpl = dirname($path).'/tmpl/'.basename($path);
				if(parent::findFile($path_tmpl)) return $path_tmpl;
			}

			$result = parent::findFile($path);
		}

		return $result;
	}
}