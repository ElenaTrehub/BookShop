<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 11.01.2019
 * Time: 11:18
 */

namespace Application\Services;


class CartService {

    public function getCart(  ){

        if( isset($_COOKIE['cart']) ){
            return json_decode($_COOKIE['cart']);
        }//if

        return [];


    }//getCart

    public function prepareBookArray(  $books  ){

        $cart = $this->getCart();

        if($cart){

            foreach ($cart as $cartItem) {

                foreach ($books as &$book) {

                    if( (int)$cartItem->bookID ===  (int)$book->bookID){
                        $book->isInCart = true;
                        break;
                    }//if

                }//foreach


            }//foreach
        }//if


    }//prepareBookArray

}//CartService