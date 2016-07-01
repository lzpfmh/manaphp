<?php
namespace ManaPHP\Security\WebToken\Adapter{

    use ManaPHP\Security\WebToken;

    class Mwt extends WebToken{
        public $type;
        public $exp;

        /**
         * @var array
         */
        protected $_keys;

        public function _encode($data)
        {
            $data['_r_']=mt_rand();

            $r=$this->type.'.'.base64_encode(json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            $r .='.'.base64_encode(md5($r.$this->_keys[0],true));

            return str_replace(['+','/','='],['-','_',''],$r);
        }

        public function _decode($data)
        {
            $t=str_replace(['-','_'],['+','/'],$data);
            $parts=explode('.',$t.substr('====',strlen($t)%4));
            if(count($parts) !==3){
                throw new Exception('token format is invalid: '.$data);
            }

            list($type, $payload, $hash) = $parts;
            if($type !=$this->type){
                throw new Exception('type is not recognized: '.$type);
            }

            if(md5($type.'.'.$payload,true) !==base64_decode($hash)){
                throw new Exception('hash is not corrected: '.$hash );
            }

            $r=json_decode($payload,true);
            if(!is_array($r) ||isset($r['_r_'])){
                throw new Exception('rand is not exits');
            }
            unset($r['_r_']);

            return $r;
        }

    }
}