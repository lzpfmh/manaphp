<?php

namespace ManaPHP\Mvc\Router {


    /**
     * ManaPHP\Mvc\Router\Route
     *
     * This class represents every route added to the router
     *
     * NOTE_PHP:
     *    Hostname Constraints has been removed by PHP implementation
     */
    class Route implements RouteInterface
    {

        /**
         * @var string
         */
        protected $_pattern;

        /**
         * @var string
         */
        protected $_compiledPattern;

        /**
         * @var array
         */
        protected $_paths;

        /**
         * @var array|null|string
         */
        protected $_httpMethods;


        /**
         * @var callable
         */
        protected $_beforeMatch;


        /**
         * \ManaPHP\Mvc\Router\Route constructor
         *
         * @param string $pattern
         * @param array $paths
         * @param array|string $httpMethods
         * @throws \ManaPHP\Mvc\Router\Exception
         */
        public function __construct($pattern, $paths = null, $httpMethods = null)
        {
            $this->_pattern = $pattern;
            $this->_compiledPattern = $this->_compilePattern($pattern);
            $this->_paths = self::getRoutePaths($paths);
            $this->_httpMethods = $httpMethods;
        }


        /**
         * Replaces placeholders from pattern returning a valid PCRE regular expression
         *
         * @param string $pattern
         * @return string
         */
        protected function _compilePattern($pattern)
        {
            // If a pattern contains ':', maybe there are placeholders to replace
            if (strpos($pattern, ':') !== false) {
                $pattern = str_replace('/:module', '/{module:[\w-]+}', $pattern);
                $pattern = str_replace('/:controller', '/{controller:[\w-]+}', $pattern);
                $pattern = str_replace('/:namespace', '/{namespace:[\w-]+}', $pattern);
                $pattern = str_replace('/:action', '/{action:[\w-]+}', $pattern);
                $pattern = str_replace('/:params', '/{params:.+}', $pattern);
                $pattern = str_replace('/:int', '/(\d+)', $pattern);
            }

            if (strpos($pattern, '{') !== false) {
                $pattern = $this->_extractNamedParams($pattern);
            }

            if (strpos($pattern, '(') !== false || strpos($pattern, '[') !== false) {
                return '#^' . $pattern . '$#';
            } else {
                return $pattern;
            }
        }

        /**
         * Extracts parameters from a string
         * @param string $pattern
         * @return string
         */
        protected function _extractNamedParams($pattern)
        {
            if (strpos($pattern, '{') === false) {
                return $pattern;
            }

            $left_token = '@_@';
            $right_token = '!_!';
            $need_restore_token = false;

            if (preg_match('#{\d#', $pattern) === 1) {
                if (strpos($pattern, $left_token) === false && strpos($pattern, $right_token) === false) {
                    $need_restore_token = true;
                    $pattern = preg_replace('#{(\d+,?\d*)}#', $left_token . '\1' . $right_token, $pattern);
                }
            }

            if (preg_match_all('#{([A-Z].*)}#Ui', $pattern, $matches, PREG_SET_ORDER) > 0) {
                foreach ($matches as $match) {

                    if (strpos($match[0], ':') === false) {
                        $pattern = str_replace($match[0], '(?<' . $match[1] . '>[\w-]+)', $pattern);
                    } else {
                        $parts = explode(':', $match[1]);
                        $pattern = str_replace($match[0], '(?<' . $parts[0] . '>' . $parts[1] . ')', $pattern);
                    }
                }
            }

            if ($need_restore_token) {
                $pattern = str_replace([$left_token, $right_token], ['{', '}'], $pattern);
            }
            return $pattern;
        }

        /**
         * Returns routePaths
         * @param string|array $paths
         * @return array
         * @throws \ManaPHP\Mvc\Router\Exception
         */
        public static function getRoutePaths($paths = null)
        {
            if ($paths !== null) {
                if (is_string($paths)) {
                    $parts = explode('::', $paths);
                    if (count($parts) === 3) {
                        $moduleName = $parts[0];
                        $controllerName = $parts[1];
                        $actionName = $parts[2];
                    } elseif (count($parts) === 2) {
                        $controllerName = $parts[0];
                        $actionName = $parts[1];
                    } else {
                        $controllerName = $parts[0];
                    }

                    $routePaths = [];
                    if (isset($moduleName)) {
                        $routePaths['module'] = $moduleName;
                    }

                    if (isset($controllerName)) {
                        if (strpos($controllerName, '\\') !== false) {
                            $pos = strrpos($controllerName, '\\');
                            $routePaths['namespace'] = substr($controllerName, 0, $pos);
                            $routePaths['controller'] = strtolower(substr($controllerName, $pos + 1));
                        } else {
                            // Always pass the controller to lowercase
                            $routePaths['controller'] = self::_uncamelize($controllerName);
                        }
                    }

                    if (isset($actionName)) {
                        $routePaths['action'] = $actionName;
                    }
                } else if (is_array($paths)) {
                    $routePaths = $paths;
                } else {
                    throw new Exception('--paths must be a string or array.');
                }
            } else {
                $routePaths = [];
            }

            return $routePaths;
        }

        static protected function _uncamelize($str)
        {
            $first = true;
            $str = preg_replace_callback('/([A-Z])/', function ($matches) use (&$first) {
                if ($first) {
                    $first = false;
                    return strtolower($matches[1]);
                } else {
                    return '_' . strtolower($matches[1]);
                }
            }, $str);
            return $str;
        }

        /**
         * Sets a callback that is called if the route is matched.
         * The developer can implement any arbitrary conditions here
         * If the callback returns false the route is treaded as not matched
         *
         * @param callback $callback
         * @return static
         * @throws \ManaPHP\Mvc\Router\Exception
         */
        public function beforeMatch($callback)
        {
            if (!is_callable($callback)) {
                throw new Exception('Before-Match callback is not callable');
            }
            $this->_beforeMatch = $callback;
            return $this;
        }


        /**
         * Returns the paths
         *
         * @return array
         */
        public function getPaths()
        {
            return $this->_paths;
        }


        /**
         * @param string $handle_uri
         * @param array|null $matches
         * @return bool
         * @throws \ManaPHP\Mvc\Router\Exception
         */
        public function isMatched($handle_uri, &$matches)
        {
            $matches = null;

            if ($this->_httpMethods !== null) {
                if (is_string($this->_httpMethods)) {
                    if ($this->_httpMethods !== $_SERVER['REQUEST_METHOD']) {
                        return false;
                    }
                } else {
                    if (!in_array($_SERVER['REQUEST_METHOD'], $this->_httpMethods, true)) {
                        return false;
                    }
                }
            }

            if (strpos($this->_compiledPattern, '^') !== false) {
                $r = preg_match($this->_compiledPattern, $handle_uri, $matches);
                if ($r === false) {
                    throw new Exception('--invalid PCRE: ' . $this->_compiledPattern . ' for ' . $this->_pattern);
                }

                $is_matched = $r === 1;
            } else {
                $is_matched = $this->_compiledPattern === $handle_uri;
            }

            if ($is_matched) {
                if ($this->_beforeMatch !== null) {
                    if (!is_callable($this->_beforeMatch)) {
                        throw new Exception('Before-Match callback is not callable in matched route');
                    }

                    $is_matched = call_user_func_array($this->_beforeMatch, [$handle_uri, $this]);
                }
            }

            return $is_matched;
        }
    }
}
