<?php
namespace ManaPHP\Security{

    use ManaPHP\Component;

    abstract  class WebToken  extends Component implements WebTokenInterface, WebToken\AdapterInterface{
        public function create($ttl)
        {
            return '';
        }
    }
}