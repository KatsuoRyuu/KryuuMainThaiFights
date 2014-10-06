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

 * @version 20140701 
 * @link https://github.com/KatsuoRyuu/
 */

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class GlobalConfigServiceFactory implements FactoryInterface{
    
	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var BaseNamespace
	 */
	protected $baseNamespace;

	/**
	 * @var configuration
	 */
	protected $configuration;

	/**
	 * @var MailTransport
	 */
	protected $transport;
	
    /**
     *
     * @var eventmanager 
     */
    protected $events;
    
    /**
     * configuration array
     * @var array 
     */
    protected $config=array();
    
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->setConfiguration();

        return $this;
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
	* Sets the configuration for later easier access
	*
	* @access protected
	* @return PostController
	*/
	protected function setConfiguration($namespace) {
        $tmpConfig = $this->serviceLocator->get('config');
        $this->configuration[$namespace] = $tmpConfig[$namespace];
		return $this;
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
	public function getConfiguration($searchString=null,$global=false,$namespace)	{
        
		if (null === $this->configuration[$namespace]) {
			$this->setConfiguration($namespace);
		}
        
        if($global){
            $tmp = $this->serviceLocator->get('config');
            
            if (is_array($searchString)){
                return $this->getArrayParts($tmp,$searchString);
            }
            
            return $tmp[$searchString];
        }
        
        if($searchString==null){
            $searchString = $this->$namespace();
            return $this->configuration[$namespace];
        }
        
        if (is_array($searchString)){
            return $this->getArrayParts($this->configuration[$namespace],$searchString);
        }
        
		return $this->configuration[$namespace][$searchString];
	}  
    
    protected function getArrayParts($config,$searchArray,$key=0){
        $configTmp = $config[$searchArray[$key]];
        if (is_array( $configTmp ) && count($searchArray) > $key+1){
            return $this->getArrayParts($configTmp,$searchArray,$key+1);
        }
        return $configTmp;
    }
    
	/**
	* Sets the configuration for later easier access
	*
	* @access protected
	* @return PostController
	*/
	protected function setMailTransport() {

        $config = $this->getConfiguration(array('mailTransport'),true);
        
        $this->transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'name'              => $config['name'],
            'host'              => $config['host'],
            'connection_class'  => $config['connection_class'],
            'connection_config' => array(
                'username' => $config['connection_config']['username'],
                'password' => $config['connection_config']['password'],
            ),
        ));
        $this->transport->setOptions($options);
        return $this->transport;
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
        
		if (null === $this->transport) {
			$this->setMailTransport();
		}
		return $this->transport;
	}
    
    public function getNewMailMessage(){
        return new Message($this);
    }
    
    public function sendMail($message){
        
        $mail = new Mail\Message();
        
        $parts = array();
        
        if (is_array($message->__get('message'))){
            foreach ( $message->__get('message') as $mimetype => $messagepart ){
                
                $bodyMessage = new Mime\Part($messagepart);
                $bodyMessage->type = $mimetype;
                $parts[] = $bodyMessage;
            }
        }  
        
        if ($message->__get("file")->count() > 0){
            foreach ($message->__get("file") as $file) {
                $fileRepo = $this->getServiceLocator()->get('FileRepository');
                $fileContent = fopen($fileRepo->getRoot().'/'.$file->getSavePath(), 'r');
                
                $attachment = new Mime\Part($fileContent);
                $attachment->type = $file->getMimetype();
                $attachment->filename = $file->getName();
                $attachment->encoding = Mime\Mime::ENCODING_BASE64;
                $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
                $parts[] = $attachment;
            }

        }

        $bodyPart = new Mime\Message();

        // add the message body and attachment(s) to the MimeMessage
        $bodyPart->setParts($parts);
       
        /*
         * getting the from the sender.
         */
        $from = $message->__get('reply');
        if ($from == null){
            $from = array( $this->configService->get(array('mailTransport','default_sender_name'),true) => $this->configService->get(array('mailTransport','default_sender'),true) );
        }
        $fromName = array_keys($from);
        $fromMail = array_values($from);
        
        foreach($fromName as $index => $name){
            $mail
                ->addFrom($fromMail[$index],$name)
                ->addReplyTo($fromMail[$index],$name)
                ->setSender($fromMail[$index],$name);
        }
        
        /*
         * getting the from the sender.
         */
        $recievers = $message->__get('recievers');
        $recieversMail = array_values($recievers);
        
        foreach($recieversMail as $email){
            $mail->addTo($email);
        }
        
        $mail
            ->setSubject($message->__get('subject'))
            ->setBody($bodyPart)
            ->setEncoding("UTF-8")
            ->setBody($bodyPart);
        // Setup SMTP transport using LOGIN authentication
        
        $this->configService->getMailTransport()->send($mail);
        
    }
    
	/**
	* Sets the EntityManager
	*
	* @param EntityManager $em
	* @access protected
	* @return PostController
	*/
	private function setEntityManager(\Doctrine\ORM\EntityManager $em)
	{
		$this->entityManager = $em;
		return $this;
	}
	
	/**
	* Returns the EntityManager
	*
	* Fetches the EntityManager from ServiceLocator if it has not been initiated
	* and then returns it
	*
	* @access protected
	* @return Doctrine\ORM\EntityManager
	*/
	public function entityManager()
	{
		if (null === $this->entityManager) {
			$this->setEntityManager($this->getServiceLocator()->get('Doctrine\ORM\EntityManager'));
		}
		return $this->entityManager;
	}
    
    /**
     * Will check if the translator is avaiable if not then get it from the service 
     * locator and do the translation. Then return the translated string
     * 
     * @param type $string
     * @return string
     */
    public function translate($string)
	{
		if ($this->translate == NULL)
		{
	  		$this->translate = $this->getServiceLocator()->get('translator');
		}
		return $this->translate->translate($string);
	}
    
}

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

 * @version 20140508 
 * @link https://github.com/KatsuoRyuu/
 */

use Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;
use Zend\Form\Annotation;

class Message {

    /**
     * @var integer 
     */
    private $id;

    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $recievers;

    /**
     * @var String
     */
    private $reply;

    /**
     * @var String
     */
    private $subject;

    /**
     * @var String
     */
    private $file;

    /**
     * @var String
     */
    private $message;
    
    
    /**
     * 
     */
    private $service;
    /**
     * 
     * 
     */
    public function __construct($service) {
        $this->about = new ArrayCollection();
        $this->file = new ArrayCollection();
        $this->recievers = new ArrayCollection();
        $this->service = $service;
    }
    
    public function sendMail(){
        $this->service->sendMail($this);
    }
    
    public function __add($value,$key){
        if(!$this->$key instanceof ArrayCollection) {
            $this->$key = new ArrayCollection();
        }
        $this->$key->add($value);
    }

    /**
     * WARNING USING THESE IS NOT SAFE. there is no checking on the data and you need to know what
     * you are doing when using these.
     * But it a great function for lazy people ;)
     * 
     * @param ANY $value
     * @param ANY $key
     * @return $value
     */
    public function __set($value,$key){
        return $this->$key = $value;
    }    

    /**
     * WARNING USING THESE IS NOT SAFE. there is no checking on the data and you need to know what
     * you are doing when using these.
     * But it a great function for lazy people ;)
     * 
     * @param ANY $value
     * @param ANY $key
     * @return $value
     */
    public function __get($key){
        return $this->$key;
    }    

    /**
     * WARNING USING THESE IS NOT SAFE. there is no checking on the data and you need to know what
     * you are doing when using these.
     * This is used to exchange data from form and more when need to store data in the database.
     * and again ist made lazy, by using foreach without data checks
     * 
     * @param ANY $value
     * @param ANY $key
     * @return $value
     */
    public function populate($array){
        
        $this->__add($array['about'],'about');
        $this->__add($array['file'],'file' );
        
        $this->email    = $array['email'];
        $this->message  = $array['message'];
        $this->name     = $array['name'];
        $this->subject  = $array['subject'];
    }


    /**
    * Get an array copy of object
    *
    * @return array
    */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }


}
