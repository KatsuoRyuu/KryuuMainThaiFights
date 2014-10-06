<?php
namespace KryuuMainOmt\Controller;

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


class EntityUsingController extends Constants
{
    const CONFIG_SERVICE_FACTORY="";
    
    private $service;
    
    /**
	* Returns the EntityManager
	*
	* Fetches the EntityManager from ServiceLocator if it has not been initiated
	* and then returns it
	*
	* @access protected
	* @return Doctrine\ORM\EntityManager
	*/
	protected function entityManager(){
		$this->setConfigService();
		return $this->entityManager;
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
        $this->setConfigService();
        return $this->service->getBaseNamespace();
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
	protected function configuration($searchString,$global=false)	{
        $this->setConfigService();
		return $this->service->get($searchString,$global);
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
	protected function getMailTransport()	{
        $this->setConfigService();
		return $this->service->transport();
	}
    
    /**
     * 
     * @return eventmanager
     */
    protected function events() {
		$this->setConfigService();
        return $this->service->events();
    }
    
    /**
     * Will check if the translator is avaiable if not then get it from the service 
     * locator and do the translation. Then return the translated string
     * 
     * @param type $string
     * @return string
     */
    protected function translate($string){
		$this->setConfigService();
		return $this->service->translate($string);
	}
    
    private function setConfigService(){
        if (!$this->service){
            $this->service = $this->getServiceLocator()->get(static::CONFIG_SERVICE_FACTORY);
        }
    }
} 
