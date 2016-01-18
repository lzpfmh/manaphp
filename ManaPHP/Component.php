<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/18
 */
namespace ManaPHP{

    use ManaPHP\Event\Manager;

    /**
     * ManaPHP\Component
     *
     * @property \ManaPHP\Mvc\DispatcherInterface $dispatcher;
     * @property \ManaPHP\Mvc\RouterInterface $router
     * @property \ManaPHP\Mvc\UrlInterface $url
     * @property \ManaPHP\Http\RequestInterface $request
     * @property \ManaPHP\Http\ResponseInterface $response
     * @property \ManaPHP\Http\Response\CookiesInterface $cookies
     * @property \ManaPHP\FilterInterface $filter
     * @property \ManaPHP\Flash\Direct $flash
     * @property \ManaPHP\Flash\Session $flashSession
     * @property \ManaPHP\Http\SessionInterface $session
     * @property \ManaPHP\Event\ManagerInterface $eventsManager
     * @property \ManaPHP\DbInterface $db
     * @property \ManaPHP\Security $security
     * @property \ManaPHP\CryptInterface $crypt
     * @property \ManaPHP\EscaperInterface $escaper
     * @property \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
     * @property \ManaPHP\Mvc\Model\MetadataInterface $modelsMetadata
//     * @property \ManaPHP\Assets\Manager $assets
     * @property \ManaPHP\Di|\ManaPHP\DiInterface $di
//     * @property \ManaPHP\Session\BagInterface $persistent
     * @property \ManaPHP\Mvc\ViewInterface $view
     */
    class Component implements ComponentInterface{
        /**
         * @var \ManaPHP\Event\Manager
         */
        protected $_trait_eventsManager = null;
        protected static $_trait_eventPeeks;

        /**
         * Dependency Injector
         *
         * @var \ManaPHP\DiInterface
         */
        protected $_dependencyInjector = null;

        /**
         * Sets the dependency injector
         *
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function setDI($dependencyInjector)
        {
            $this->_dependencyInjector = $dependencyInjector;
        }

        /**
         * Returns the internal dependency injector
         *
         * @return \ManaPHP\DiInterface
         */
        public function getDI()
        {
            if (!is_object($this->_dependencyInjector)) {
                $this->_dependencyInjector = Di::getDefault();
            }

            return $this->_dependencyInjector;
        }

        /**
         * Magic method __get
         *
         * @param string $propertyName
         * @return object
         */
        public function __get($propertyName)
        {
            if (!is_object($this->_dependencyInjector)) {
                $this->_dependencyInjector = Di::getDefault();
            }

            if ($this->_dependencyInjector->has($propertyName)) {
                return $this->{$propertyName} = $this->_dependencyInjector->getShared($propertyName);
            }

            if ($propertyName === 'di') {
                return $this->{'di'} = $this->_dependencyInjector;
            }

            trigger_error('Access to undefined property ' . $propertyName);

            return null;
        }

        /**
         * Attach a listener to the events manager
         *
         * @param string $event
         * @param object|callable $handler
         * @throws \ManaPHP\Event\Exception
         */
        public function attachEvent($event, $handler)
        {
            if ($this->_trait_eventsManager === null) {
                $this->_trait_eventsManager = new Manager();
            }

            $this->_trait_eventsManager->attachEvent($event, $handler);
        }


        /**
         * Fires an event in the events manager causing that the active listeners will be notified about it
         *
         * @param string $event
         * @param object $source
         * @param mixed $data
         * @return mixed
         */
        public function fireEvent($event, $source, $data = null)
        {
            if (self::$_trait_eventPeeks !== null) {
                foreach (self::$_trait_eventPeeks as $peek) {
                    $peek($event, $source, $data);
                }
            }

            if ($this->_trait_eventsManager !== null) {
                /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
                return $this->_trait_eventsManager->fireEvent($event, $source, $data);
            }

            return null;
        }

        public static function peekEvents($peek)
        {
            if (!$peek instanceof \Closure) {
                throw new Exception('Peek is invalid: not Closure.');
            }

            if (self::$_trait_eventPeeks === null) {
                self::$_trait_eventPeeks = [$peek];
            } else {
                self::$_trait_eventPeeks[] = $peek;
            }
        }
    }
}