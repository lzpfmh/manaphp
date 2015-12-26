<?php

namespace ManaPHP\Db {

    /**
     * ManaPHP\Db\PrepareEmulation
     */

    class PrepareEmulation {
        /**
         * @var \PDO
         */
        protected $_pdo;

        public function __construct($pdo){
            $this->_pdo =$pdo;
        }

        protected function _parseValue($value, $type){
            if($type===\PDO::PARAM_STR){
                return $this->_pdo->quote($value);
            }elseif($type===\PDO::PARAM_INT){
                return $value;
            } else if($type ===\PDO::PARAM_NULL){
                return 'NULL';
            } else if($type ===\PDO::PARAM_BOOL){
                return (int)$value;
            }else{
                return $value;
            }
        }

        /**
         * @param mixed $data
         * @return int
         */
        protected function _inferType($data){
            if(is_string($data)){
                return \PDO::PARAM_STR;
            }else if(is_int($data)){
                return \PDO::PARAM_INT;
            }else if(is_bool($data)){
                return \PDO::PARAM_BOOL;
            }else if($data ===null){
                return \PDO::PARAM_NULL;
            }else{
                return\PDO::PARAM_STR;
            }
        }
        /**
         * @param string $sqlStatement
         * @param array $bindParams
         * @param array $bindTypes
         * @return mixed
         */
        public function emulate($sqlStatement, $bindParams=null, $bindTypes=null){
            if($bindParams ===null ||count($bindParams) ===0){
                return $sqlStatement;
            }

            if(isset($bindParams[0])){
                $pos =0;

                $preparedStatement= preg_replace_callback('/(\?)/',
                    function($matches) use($bindParams, $bindTypes,&$pos){
                        $type =isset($bindTypes[$pos])?$bindTypes[$pos]:$this->_inferType($bindParams[$pos]);
                        return $this->_parseValue($bindParams[$pos++],$type);
                    },
                    $sqlStatement);

                if($pos !==count($bindParams)){
                    return 'infer failed:'.$sqlStatement;
                }else{
                    return $preparedStatement;
                }
            }else{
                $replaces=[];
                foreach($bindParams as $key=>$value){
                    $type =isset($bindTypes[$key])?$bindTypes[$key]:$this->_inferType($bindParams[$key]);
                    $replaces[$key]=$this->_parseValue($value,$type);
                }

                return strtr($sqlStatement,$replaces);
            }
        }
    }
}
