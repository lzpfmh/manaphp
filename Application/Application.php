<?php
namespace Application{

    use ManaPHP\Db\Adapter\Mysql;
    use ManaPHP\DbInterface;
    use ManaPHP\Logger\Adapter\File;
    use ManaPHP\Mvc\Router\Group;
    use ManaPHP\AuthorizationInterface;

    class Authentication implements AuthorizationInterface{
        public function authorize($dispatcher)
        {
            return true;
        }
    }

    class Application extends \ManaPHP\Mvc\Application
    {
        protected $_appDataDirectory;

        protected function registerServices(){
            $this->router->mount(new Group(),'Home','/');
            $this->logger->addAdapter(new File($this->_appDataDirectory.'/Logs/'.date('Ymd').'.log'));
            $this->_dependencyInjector->set('db', function () {
                $mysql = new Mysql([
                    'host' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'dbname' => 'manaphp_unit_test',
                    'port' => 3306
                ]);
                $self=$this;
                $mysql->attachEvent('db:beforeQuery', function ($event, DbInterface $source, $data)use($self) {
                    $self->logger->debug('SQL: '.$source->getSQLStatement());
                });

                return $mysql;
            });
        }

        public function main(){
            date_default_timezone_set('PRC');

            $this->_appDataDirectory=dirname(__DIR__).'/AppData';
			
            $this->registerServices();
            $this->logger->debug('start');

         //   $this->useImplicitView(false);

            $this->registerModules(['Home']);
            $this->_dependencyInjector->setShared('authorization',new Authentication());
            return $this->handle()->getContent();
        }
    }
}