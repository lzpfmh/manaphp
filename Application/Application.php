<?php
namespace Application {

    use ManaPHP\Db\Adapter\Mysql;
    use ManaPHP\DbInterface;
    use ManaPHP\Log\Adapter\File;
    use ManaPHP\Log\Logger;
    use ManaPHP\Mvc\Router;
    use ManaPHP\Security\Crypt;

    class Application extends \ManaPHP\Mvc\Application
    {
        protected function registerServices()
        {
            $self = $this;

            $this->_dependencyInjector->setShared('router', function () {
                return (new Router())
                    ->mount(new Home\RouteGroup(), '/')
                    ->mount(Api\RouteGroup::class, '/api', 'Api');
            });

            $this->_dependencyInjector->setShared('logger', function () use ($self) {
                return (new Logger())->addAdapter(new File($self->configure->log->file));
            });

            $this->_dependencyInjector->setShared('crypt', function () use ($self) {
                return new Crypt($self->configure->crypt->key);
            });

            $this->_dependencyInjector->set('db', function () use ($self) {
                $mysql = new Mysql((array)$this->configure->database);
                $mysql->attachEvent('db:beforeQuery', function ($event, DbInterface $source, $data) use ($self) {
                    $self->logger->debug('SQL: ' . $source->getSQL());
                });

                return $mysql;
            });

            $this->_dependencyInjector->setShared('authorization', new Authorization());
        }

        public function main()
        {
            date_default_timezone_set('PRC');

            $this->_dependencyInjector->setShared('configure', new Configure());

            if ($this->configure->debugger->disableAutoResponse) {
                unset($_GET['_debugger']);//disable auto response to debugger data fetching request
            }

            $this->debugger->start();

            $this->registerServices();

            // $this->logger->debug('start');

            //   $this->useImplicitView(false);

            return $this->handle()->getContent();
        }
    }
}