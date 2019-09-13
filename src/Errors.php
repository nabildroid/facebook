<?php
namespace Facebook;
class Errors{

    /**check if index exist in array or report it */
    protected function undefined_array_index(array $arr,$index,string $log=""){
        if(is_array($index)){
            while(isset($index[0])&&isset($arr[$index[0]]))
	            $arr=$arr[array_shift($index)];

            if(count($index))
                $this->error($log);
        }
        elseif(!isset($arr[$index])){
            $this->error($log);
        }
        return true;
    }
    protected function error(string $log){
        throw new \Exception($log);
    }

};

?>