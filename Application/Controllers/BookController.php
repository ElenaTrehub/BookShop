<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.12.2018
 * Time: 16:25
 */

namespace Application\Controllers;

use Application\Services\AuthorService;
use Application\Services\BookService;
use Application\Services\GenresService;
use Application\Services\CommentsService;

class BookController extends BaseController{

    public function bookListAction(  ){

        $bookService = new BookService();

        $books = $bookService->GetBooks();

        $template = $this->twig->load( 'Book\book-list.twig');

        echo $template->render( array(
            'books' => $books
        ) );

    }//bookListAction

    public function getMoreBooks(){

        $bookService = new BookService();

        $limit = $this->request->GetGetValue('limit');

        $offset = $this->request->GetGetValue('offset');

        $books = $bookService->GetBooks( (int)$limit, (int)$offset );

        $this->json( 200 , array(
            'code' => 200,
            'books' => $books
        ) );

    }//bookListAction

    public function getBookByIdAction( $id ){

        $bookService = new BookService();

        $book = $bookService->GetBookById( $id );

        $template = $this->twig->load( 'Book/book.twig');

        echo $template->render( array(
            'book' => $book
        ) );

    }//getBooksByIdAction

    public function newBookAction(  ){

        $authorsService = new AuthorService();
        $genresService = new GenresService();

        $authors = $authorsService->GetAuthors(100);
        $genres = $genresService->GetGenres(100);

        $template = $this->twig->load( 'Book/new-book.twig');

        echo $template->render(array(
            'authors' => $authors,
            'genres'  => $genres
        ));

    }//newBookAction

    public function addBookAction( ){

        $bookTitle = $this->request->GetPostValue('bookTitle');
        $matches = array();

        $check = preg_match('/^[а-яa-z0-9\s]{3,50}$/iu',$bookTitle , $matches );

        if( !$check ){

            $this->json( 400 , array(
                'title_err' => $bookTitle
            ) );

            return;

        }//if

        $bookISBN = $this->request->GetPostValue('bookISBN');

        if(! filter_var($bookISBN , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^\d{9}[\d|X]$/"))
        )){

            $this->json( 400 , array(
                'ISBN_err' => $bookISBN
            ) );

            return;

        }//if

        $bookPages = $this->request->GetPostValue('bookPages');

        if(! filter_var($bookPages , FILTER_VALIDATE_REGEXP , array(
            "options" => array("regexp"=>"/\d{1,10}$/"))
        )){

            $this->json( 400 , array(
                'Pages_err' => $bookPages
            ) );

            return;

        }//if

        $bookPrice = $this->request->GetPostValue('bookPrice');

        if(! filter_var($bookPrice , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^\d{1,7}/"))
        )){

            $this->json( 400 , array(
                'Price_err' => $bookPrice
            ) );

            return;

        }//if

        $bookAmount = $this->request->GetPostValue('bookAmount');

        if(! filter_var($bookAmount , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^\d{1,5}/"))
        )){

            $this->json( 400 , array(
                'Amount_err' => $bookAmount
            ) );

            return;

        }//if

        $bookDescription = $this->request->GetPostValue('bookDescription');

        if(! filter_var($bookDescription , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^.{10,500}/"))
        )){

            $this->json( 400 , array(
                'Description_err' => $bookDescription
            ) );

            return;

        }//if

        $bookService = new BookService();

        $authors = json_decode($this->request->GetPostValue('authors'));
        $genres = json_decode($this->request->GetPostValue('genres'));

        try{


            $result = $bookService->AddBook( [
                'bookTitle' => $bookTitle,
                'bookISBN' => $bookISBN,
                'bookPages' => $bookPages ,
                'bookPrice' => $bookPrice,
                'bookAmount' => $bookAmount,
                'bookDescription' => $bookDescription,
                'authors' => $authors,
                'genres' => $genres,
            ]);

            $this->json( 200 , array(
                'code' => 200,
                'book' => $result
            ) );


        }//try
        catch( \Exception $ex ){

            $this->json( 500 , array(
                'code' => 500,
                'book' => $ex
            ) );

        }//catch

    }//addBookAction

    public function acceptEditBookAction($id){

        $bookTitle = $this->request->GetPostValue('bookTitle');

        if(! filter_var($bookTitle , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^[\w\s]{2,50}$/iu"))
        )){

            $this->json( 400 , array(
                'title_err' => $bookTitle,
                'POST' => $_POST
            ) );

            return;

        }//If

        $bookISBN = $this->request->GetPostValue('bookISBN');

        if(! filter_var($bookISBN , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^\d{9}[\d|X]$/"))
        )){

            $this->json( 400 , array(
                'ISBN_err' => $bookISBN
            ) );

            return;

        }//if

        $bookPages = $this->request->GetPostValue('bookPages');

        if(! filter_var($bookPages , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/\d{1,10}$/"))
        )){

            $this->json( 400 , array(
                'Pages_err' => $bookPages
            ) );

            return;

        }//if

        $bookPrice = $this->request->GetPostValue('bookPrice');

        if(! filter_var($bookPrice , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^\d+[\.,]{1}(\d{1,7})$/"))
        )){

            $this->json( 400 , array(
                'Price_err' => $bookPrice
            ) );

            return;

        }//if

        $bookAmount = $this->request->GetPostValue('bookAmount');

        if(! filter_var($bookAmount , FILTER_VALIDATE_REGEXP , array(
                "options" => array("regexp"=>"/^\d{1,5}/"))
        )){

            $this->json( 400 , array(
                'Amount_err' => $bookAmount
            ) );

            return;

        }//if

        $bookID = $id;

        $bookService = new BookService();

        $result = $bookService->EditBookByID($bookTitle , $bookISBN , $bookPages , $bookPrice , $bookAmount, $bookID);

        $this->json( 200 , array(
            'book' => $result,
            'code' => 200
        ) );

    } // acceptEditBookAction

    public function infoBookAction($id){

        $bookService = new BookService();
        $bookForInfo = $bookService->GetBookById($id);
        $commentService = new CommentsService();
        $amount = $commentService->GetAmountCommentsByBookId($id);

        $template = $this->twig->load( 'Book/info-book.twig');

        echo $template->render(array(
            'book' => $bookForInfo,
            'commentAmount' => $amount
        ));

    } // infoBookAction

    public function getPublicBookAction($id){

        $bookService = new BookService();
        $bookForInfo = $bookService->GetBookById($id);
        $commentService = new CommentsService();
        $amount = $commentService->GetAmountCommentsByBookId($id);

        $template = $this->twig->load( 'public/Book/book.twig');
        $limit = 3;
        $bookForInfo->comments = $commentService->GetCommentsByBookId($id, $limit);

        echo $template->render(array(
            'book' => $bookForInfo,
            'commentAmount' => $amount
        ));

    } // infoBookAction

    public function deleteBookAction($id){

        $bookService = new BookService();
        $result = $bookService->DeleteBookById($id);

        $this->json( 200 ,  array(
            'book' => $result,
            'code' => 200
        ));

    } // deleteBookAction

    public function editBookAction($id){

        $bookService = new BookService();
        $result = $bookService->GetBookById($id);

        $template = $this->twig->load( 'Book/edit-book.twig');

        echo $template->render(array(
            'book' => $result
        ));

    } // deleteBookAction

}//AuthorController