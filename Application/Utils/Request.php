<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.12.2018
 * Time: 11:12
 */

namespace Application\Utils;


class Request{

    public function GetGetValue( $key ){

        //$_GET[ $key ]

        if( isset($_GET[$key]) ){
            return $_GET[$key];
        }//if

        return null;

    }//GetGetValue

    public function GetPostValue( $key ){

        if( isset($_POST[$key]) ){
            return $_POST[$key];
        }//if

        return null;

    }//GetPostValue

    public function GetPutValue( $key ){

        $params = [];

        //authorID=12&authorName=Vasya

        parse_str(
            file_get_contents("php://input") ,
            $params
        );

        if( isset($params[$key]) ){
            return $params[$key];
        }//if
        else {
            return null;
        }//else

    }//GetPutValue

}//Request