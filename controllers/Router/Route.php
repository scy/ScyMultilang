<?php
// Taken from <http://blog.helmich.cz/305-howto-simple-multilingual-routes-in-zend-framework/>
/**
 * This class has been inherited to bring an additional feature to the
 * Zend_Controller_Router_Route class. It detects a route segment named
 * locale and sets a default locale for the runnig application based on
 * the segment's value
 *
 * If the value is not a valid Locale identifier the locale is not set
 * into the registry
 *
 * @author Jiri Helmich <jiri  @HELMICH.CZ>
 */
class ScyMultilang_Controller_Router_Route extends Zend_Controller_Router_Route
{
	/**
	 * Matches a user submitted path with parts defined by a map. Assigns and
	 * returns an array of variables on a successful match.
	 *
	 * @param string $path Path used to match against this routing map
	 * @return array|false An array of assigned values or a false on a mismatch
	 * @author Jiri Helmich <jiri  @HELMICH.CZ>
	 */
	public function match($path, $partial = false)
	{
		//make a copy of that for the parental class
		$originalPath = $path;

		//if the path is empty, there is no locale :-)
		if ($path !== '') {

			//path begins with a delimiter, so trim that and explode the path
			$path = trim($path, $this->_urlDelimiter);
			$path = explode($this->_urlDelimiter, $path);

			//loop over each part of the path
			foreach ($path as $pos => $value)
			{
				//a simple test if this could be a matching route
				if (!array_key_exists($pos, $this->_variables))
					break; //the route is probably longer than the path, not our business

				//get a name of current route segment
				$name = $this->_variables[$pos];

				//locale segment, that's the interesting stuff
				if ($name === 'LOCALE' && !empty($value))
				{
					try
					{
						//if the given value is not a valid locale identifier
						//an exception is thrown
						$locale = Zend_Locale::findLocale($value);

						//otherwise, we construct a new locale instance based on the identifier ...
						$locale = new Zend_Locale($locale);

						// ... and set that into the registry
						Zend_Registry::set("Zend_Locale",$locale);

						//BUT the default translator already has a locale set,
						//so we need to override that
						$this->getTranslator()->setLocale($locale);

						//we would also like if the assemble method of the
						//router would have the locale value automatically
						Zend_Controller_Front::getInstance()->getRouter()->setGlobalParam('LOCALE',$locale);

						//that's all we need to do, the rest is parent's job
						break;

					}catch(Zend_Locale_Exception $e)
					{
						// If the locale is invalid, throw a 404.
						throw new Zend_Controller_Action_Exception('Invalid locale', 404);
					}
				}
			}
		}

		//let the parental class to do its job
		return parent::match($originalPath, $partial);
	}

}
