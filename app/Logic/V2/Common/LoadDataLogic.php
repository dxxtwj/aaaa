<?php
namespace App\Logic\V2\Common;

class LoadDataLogic{
    public function __construct($data = [])
    {
        $this->load($data);
    }

    public function load($data)
    {
        if (!empty($data)) {
            $this->setAttributes($data);
            return true;
        }else{
            return false;
        }
    }
    public function setAttributes($values)
    {
        // 必须是个数组
        if (is_array($values)) {
            $attributes = array_flip($this->attributes());
            foreach ($values as $name => $value) {
                if (isset($attributes[$name])) {
                    // 如果存在该属性，就直接赋值
                    $this->$name = $value;
                }
            }
        }
    }

    public function attributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }
        return $names;
    }
    public function getAttributes($keys = null, $isNotReturnArrays = [], $isNotReturnArraysType = true){
        if (is_string($keys)){
            $keys = explode(',', $keys);
        }
        $thisKeys = $this->attributes();
        $data = [];
        if (is_array($keys)){
            if ($keys[0]===true){ 
                $keysnew = [];
                foreach ($thisKeys as $index => $key){
                    if (!in_array($key,$keys,true)){
                        $keysnew[] =  $key;
                    }
                }
                $keys = $keysnew;
            }
        }else{
            $keys = $thisKeys;
        }
        if (!is_array($isNotReturnArrays)){
            $isNotReturnArrays = [$isNotReturnArrays];
        }
        if (is_array($keys)){
            foreach ($keys as $index => $key) {
                if (in_array($key, $thisKeys)) {
                    if (in_array($this->$key, $isNotReturnArrays, $isNotReturnArraysType)){
                        continue;
                    }
                    $data[$key] = $this->$key;
                }
            }
        }
        return $data;
    }
}