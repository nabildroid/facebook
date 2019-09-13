<?php
namespace Facebook;
class Errors{

    /**check if index exist in array or report it */
    protected function handle_undefined_array_index(array $arr,$index,string $log=""){
        if(is_array($index)){
            while(isset($arr[$index[0]]))
                array_shift($index);
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