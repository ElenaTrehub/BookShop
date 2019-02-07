<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.12.2018
 * Time: 11:19
 */

namespace Application\Utils;


class Storage{

    //$storage = new Storage();
    //foreach ($storage->langs as $lang) ...

    private $storage = [];

    public function __get($name){

        if( isset( $this->storage[$name] ) ){
            return $this->storage[$name];
        }//if

        return null;

    }//__get


    //$storage->langs = [ 'RU' , 'EN' ];

    public function __set($name, $value){

        $this->storage[ $name ] = $value;

    }//__set

    function getRawStorage(){
        return $this->storage;
    }

}//Storage