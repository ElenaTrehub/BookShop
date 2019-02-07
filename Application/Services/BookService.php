<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 17.12.2018
 * Time: 17:21
 */

namespace Application\Services;

use Application\Utils\MySQL;

class BookService{

    public function GetBooks( $limit = 10 , $offset = 0 ){

        $stm = MySQL::$db->prepare(
            "
              SELECT * FROM books
              LIMIT :offset,:limit
            "
        );
        $stm->bindParam(':offset' , $offset , \PDO::PARAM_INT);
        $stm->bindParam(':limit' , $limit , \PDO::PARAM_INT);
        $stm->execute();

        $books = $stm->fetchAll(\PDO::FETCH_OBJ);

        return $books;

    }//GetBooks

    public function GetFullBooks( $limit = 10 , $offset = 0  ){

        $books = $this->GetBooks($limit , $offset);

        foreach ($books as &$book){

            $book->attributes = $this->GetBookById($book->bookID);


        }//foreach


        return $books;

    }//GetFullBooks

    public function GetBookById( $id ){

        $stm = MySQL::$db->prepare("SELECT * FROM books WHERE bookID = :id");
        $stm->bindParam(':id' , $id , \PDO::PARAM_INT);
        $stm->execute();

        $book = $stm->fetch(\PDO::FETCH_OBJ);

        if(!$book){

            return false;

        }//if

        $stm = MySQL::$db->prepare("SELECT bookImagePath FROM bookImages WHERE bookID = :id");
        $stm->bindParam(':id' , $id , \PDO::PARAM_INT);
        $stm->execute();

        $image = $stm->fetch(\PDO::FETCH_OBJ);

        if( $image ){
            $book->bookImagePath = $image->bookImagePath;
        }//if
        else{
            $book->bookImagePath = "images/books/default.jpg";
        }//else

        $book->authors = $this->GetBookAuthors( $book->bookID );
        $book->genres = $this->GetBookGenres( $book->bookID );


        return $book;


    }//GetBookById

    public function AddBook( $params = [] ){

        $stm = MySQL::$db->prepare("INSERT INTO books VALUES( DEFAULT  , :bookTitle , :bookISBN , :bookPages, :bookPrice, :bookAmount, :bookDescription)");
        $stm->bindParam(':bookTitle' , $params['bookTitle'] , \PDO::PARAM_STR);
        $stm->bindParam(':bookISBN' ,  $params['bookISBN'] , \PDO::PARAM_STR);
        $stm->bindParam(':bookPages' ,  $params['bookPages'] , \PDO::PARAM_INT);
        $stm->bindParam(':bookPrice' ,  $params['bookPrice'] , \PDO::PARAM_STR);
        $stm->bindParam(':bookAmount' ,  $params['bookAmount'] , \PDO::PARAM_INT);
        $stm->bindParam(':bookDescription' , $params['bookDescription'] , \PDO::PARAM_STR);

        $stm->execute();

        $bookID =  MySQL::$db->lastInsertId();

        if( $bookID == 0 ){

            $exception = new \stdClass();
            $exception->errorCode = MySQL::$db->errorCode ();
            $exception->errorInfo = MySQL::$db->errorInfo ();

            return $exception;

        }//if

        if( isset( $_FILES['bookImage'] ) ){


            $name =  $_FILES['bookImage']['name'];

            $name = time() . "_$name";

            if( !file_exists("images")){
                mkdir("images");
            }//if

            mkdir("images/{$bookID}");

            //$path = "/BookShopMVC/public/images/{$bookID}/{$name}";
            $path = "images/{$bookID}/{$name}";

            if( !move_uploaded_file($_FILES['bookImage']['tmp_name'] , $path) ){

                throw new \Exception('File upload error!');

            }//if

            $stm = MySQL::$db->prepare("INSERT INTO bookImages VALUES( DEFAULT  , :bookID , :bookImagePath)");
            $stm->bindParam(':bookID' , $bookID , \PDO::PARAM_INT );
            $stm->bindParam(':bookImagePath' , $path , \PDO::PARAM_STR );
            $result = $stm->execute();

            if( $result === false ){

                $exception = new \stdClass();
                $exception->errorCode = MySQL::$db->errorCode ();
                $exception->errorInfo = MySQL::$db->errorInfo ();

                return $exception;

            }//if

        }//if

        $authors = $params['authors'];
        $genres = $params['genres'];

        $stm = MySQL::$db->prepare("INSERT INTO bookauthors VALUES( DEFAULT  , :authorID , :bookID)");
        $stm->bindParam( ':bookID' , $bookID ,  \PDO::PARAM_INT);

        foreach ( $authors as $authorID ){

            $stm->bindParam( ':authorID' , $authorID ,  \PDO::PARAM_INT);

            $stm->execute();

        }//foreach

        $stm = MySQL::$db->prepare("INSERT INTO booksgenres VALUES( DEFAULT  , :authorID , :bookID)");
        $stm->bindParam( ':bookID' , $bookID ,  \PDO::PARAM_INT);

        foreach ( $genres as $genreID ){

            $stm->bindParam( ':authorID' , $genreID ,  \PDO::PARAM_INT);

            $stm->execute();

        }//foreach

        return $bookID;


    }//AddBook

    public function EditBookByID($bookTitle , $bookISBN , $bookPages , $bookPrice , $bookAmount, $bookID){

        $stm = MySQL::$db->prepare("UPDATE books SET bookTitle= :bookTitle, bookISBN= :bookISBN, bookPages= :bookPages, bookPrice= :bookPrice, bookAmount= :bookAmount WHERE bookID= :bookID");
        $stm->bindParam(':bookTitle' , $bookTitle , \PDO::PARAM_STR);
        $stm->bindParam(':bookISBN' , $bookISBN , \PDO::PARAM_INT);
        $stm->bindParam(':bookPages' , $bookPages , \PDO::PARAM_INT);
        $stm->bindParam(':bookPrice' , $bookPrice , \PDO::PARAM_INT);
        $stm->bindParam(':bookAmount' , $bookAmount , \PDO::PARAM_INT);
        $stm->bindParam(':bookID', $bookID, \PDO::PARAM_INT);

        $result = $stm->execute();

        return $result;

    } // EditBook

    public function DeleteBookById($id){

        $stm = MySQL::$db->prepare("DELETE FROM books WHERE bookID = :id");
        $stm->bindParam(':id' , $id , \PDO::PARAM_INT);
        $result = $stm->execute();

        return $result;

    } // DeleteBookById

    public function GetBookAuthors( $bookID ){

        $stm = MySQL::$db->prepare("SELECT * FROM bookauthors WHERE bookID = :id");
        $stm->bindParam(':id' , $bookID , \PDO::PARAM_INT);
        $stm->execute();

        $authorIds = $stm->fetchAll(\PDO::FETCH_OBJ);
        $authorIds  = array_map( function ( $author ){

            return $author->authorID;

        } , $authorIds );

         $authorIds = implode(',',$authorIds);

         $stm = MySQL::$db->prepare("SELECT * FROM authors WHERE authorID IN ($authorIds)");
         $stm->execute();

         return $stm->fetchAll(\PDO::FETCH_OBJ);

    }//GetBookAuthors

    public function GetBookGenres( $bookID ){

        $stm = MySQL::$db->prepare("SELECT * FROM booksgenres WHERE bookID = :id");
        $stm->bindParam(':id' , $bookID , \PDO::PARAM_INT);
        $stm->execute();

        $genresIds = $stm->fetchAll(\PDO::FETCH_OBJ);
        $genresIds  = array_map( function ( $genre ){

            return $genre->genreID;

        } , $genresIds );

        $genresIds = implode(',',$genresIds);

        $stm = MySQL::$db->prepare("SELECT * FROM genres WHERE genreID IN ($genresIds)");
        $stm->execute();

        return $stm->fetchAll(\PDO::FETCH_OBJ);


    }//GetBookAuthors

    public function AppendAuthorsToBook( $bookID , $authors ){



    }

    public function SearchBook($search){

        $stm = MySQL::$db->prepare("SELECT * FROM books WHERE bookTitle = :search");
        $stm->bindParam(':search' , $search , \PDO::PARAM_STR);
        $stm->execute();

        $book = $stm->fetchAll(\PDO::FETCH_OBJ);

        if(!$book){

            return false;

        } // if

//        $book->authors = $this->GetBookAuthors( $book->bookID );
//        $book->genres = $this->GetBookGenres( $book->bookID );

        return $book;

    } // SearchBook

}//BookService