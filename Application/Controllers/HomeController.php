<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 14.12.2018
 * Time: 11:09
 */

namespace Application\Controllers;

use Application\Services\BookService;

use Application\Services\CartService;
use Application\Services\UserService;

class HomeController extends BaseController{

    public function indexAction(  ){

        $userService = new UserService();
        $bookService = new BookService();
        $cartService = new CartService();

        $cart = $cartService->getCart();
        $user = $userService->getCurrentUser();

        $template = $this->twig->load('public/home.twig');
        $books = $bookService->GetFullBooks();

        $cartService->prepareBookArray($books);

        //echo var_dump($books);


        echo $template->render( [
            'user' => $user,
            'books' => $books,
        ] );

    }//indexAction

    public function Action404(  ){

        try {

            $template = $this->twig->load('ErrorPages/404-not-found.twig');
            echo $template->render( );

        }//try
        catch (\Exception $ex) {

        }//catch

    }

}//HomeController