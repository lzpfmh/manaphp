<?php
namespace Application\Home\Controllers;

use Application\Home\Models\City;
use ManaPHP\Mvc\Controller;
use ManaPHP\Version;

class IndexController extends Controller
{
    public function indexAction()
    {
        $city=City::findFirst(1);
        var_dump($city);
        var_dump(memory_get_peak_usage());
    }

    public function aboutAction()
    {
        $this->view->setVar('version',Version::get());
        $this->view->setVar('current_time',date('Y-m-d H:i:s'));
     //   $this->view->setVar('view','abc');
    }
}
