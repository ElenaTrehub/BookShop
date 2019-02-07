<?php
/**
 * Created by PhpStorm.
 * User: YukaSan
 * Date: 23.12.2018
 * Time: 21:09
 */

namespace Application\Controllers;


class messageConst{

    public $verificationSubject = "Book-shop";
    public $verificationTemplate = null;

    public $header = "From: book.shop.api@gmail.com\r\nContent-type: text/html; charset=iso-8859-1\r\n";
    public function tuneTemplate($userName,$hesh){

        $this->verificationTemplate = "<h3>$userName</h3> </br> <a href='http://localhost:5012/BookShopMVC/public/verification/?token=$hesh'>Confirm</a>";
    }//
}//messageConst