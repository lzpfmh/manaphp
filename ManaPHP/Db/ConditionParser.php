<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2015/12/27
 * Time: 15:53
 */
namespace ManaPHP\Db {

    use ManaPHP\Db\ConditionParser\Exception as ParserException;

    class ConditionParser{
        /**
         * @param array|string $conditions
         * @param array|null $binds
         * @return string
         * @throws \ManaPHP\Db\ConditionParser\Exception
         */
        public function parse($conditions, &$binds){
            $binds=[];

            if(is_string($conditions)){
                return $conditions;
            }

            if(!is_array($conditions)){
                throw new ParserException('invalid condition: '.json_encode($conditions));
            }

            if(count($conditions) ===0){
                return '';
            }

            $list=[];
            foreach($conditions as $key=>$value){
                if(!is_string($key)){
                    throw new ParserException('invalid condition key:'.$key);
                }

                if(strpos($key,' ') ===false){
                    $field=$key;
                    $operator='=';

                    $bindKey=$key;
                    if(is_scalar($value) ||$value ===null){
                        $realValue =$value;
                    }elseif(is_array($value)){
                        $cnt=count($value);

                        if($cnt ===1){
                            $realValue =$value[0];
                        }elseif($cnt ===2){
                            $realValue=$value[0];
                            $bindKey=$value[1];
                        }else {
                            throw new ParserException('too many items:'.json_encode($value));
                        }
                    }else{
                        throw new ParserException('bind value must be scalar: '.json_encode($value));
                    }
                }else{
                    $field=$key;
                    $operator='';
                    $realValue='';
                    $bindKey='k';
                }

                $list[]=$field.$operator.':'.$bindKey;

                $binds[$bindKey]=$realValue;
            }

            return implode(' AND ',$list);
        }
    }
}