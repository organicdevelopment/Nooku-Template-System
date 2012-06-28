<?php
/**
 * User: Oli Griffiths
 * Date: 24/06/2012
 * Time: 13:46
 */

require_once dirname(__FILE__).'/loaders/adapters/template.php';
require_once dirname(__FILE__).'/services/locators/template.php';

KLoader::addAdapter(new TmplKoowaLoaderAdapterTemplate(array('basepath' => JPATH_BASE)));
KServiceIdentifier::addLocator(
	new TmplKoowaServiceLocatorTemplate(
		new KConfig(
			array('service_container' => KService::getInstance())
		)
	)
);
