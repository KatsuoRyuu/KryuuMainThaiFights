<?php

namespace MODULE\Service;

/**
 * @encoding UTF-8
 * @note *
 * @todo *
 * @package PackageName
 * @author Anders Blenstrup-Pedersen - KatsuoRyuu <anders-github@drake-development.org>
 * @license *
 * The Ryuu Technology License
 *
 * Copyright 2014 Ryuu Technology by 
 * KatsuoRyuu <anders-github@drake-development.org>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * Ryuu Technology shall be visible and readable to anyone using the software 
 * and shall be written in one of the following ways: ?????????, Ryuu Technology 
 * or by using the company logo.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *

 * @version 20140506 
 * @link https://github.com/KatsuoRyuu/
 */

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * 
 */
class ConfigServiceFactory implements FactoryInterface
{
     
    private $service;

    private $baseNamespace;
    
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $configService = 'Kryuu'.$this->getBaseNamespace().'\GlobalConfigServiceFactory';
        $tmpConfig = $this->serviceLocator->get('config');
        if ($tmpConfig['GlobalConfigurationService'] != null){
            $configService = $serviceLocator->get('config');
        }
        
        $this->service = $serviceLocator->get($configService);

        return $this;
    }	
    
	public function get($search=null,$global=false){
        return $this->service->getConfiguration($search=null,$global=false,$this->getBaseNamespace());
    }
	
	/**
	 * Returns the configuration
	 *
	 * Fetches the string of the base configuration name ex
     * array(
     *      test => someconfig,
     *      foo  => array(
     *           foobar => barfoo,
     *           ),
     *      );
     * 
     * getConfiguration(test) returns string(someconfig)
     * getConfiguration(foo)  returns array(foobar => barfoo)
	 *
     * @param String $searchString the name of the base configuration
	 * @access protected
     * @return String or array.
	 */
	public function getMailTransport()	{

		return $this->service->getMailTransport();
	}
    
    public function getMailMessage(){
        return $this->service->getMailMessage();
    }
    
    public function sendMail($message){
        
    }
    
	/**
	* Sets the base namespace
	*
	* @param string $space
	* @access protected
	* @return PostController
	*/
	protected function setBaseNamespace($space)	{
        
        $space = explode('\\',$space);
		$this->baseNamespace = $space[0];
		return $this;
	}
	
	/**
	 * Returns the base namespace
	 *
	 * Fetches the string of the base Namespace ex. contact\controller 
     * will return contact only
	 *
	 * @access protected
     * @return String
	 */
	protected function getBaseNamespace() {
        
		if (null === $this->baseNamespace) {
			$this->setBaseNamespace(__NAMESPACE__);
		}
        
        return $this->baseNamespace;
	}
    
    /**
     * 
     * @return eventmanager
     */
    protected function events($class) 
    {
        if (!$this->events) {
            $this->events = new EventManager($class); 
        }

        return $this->events;
    }
}
