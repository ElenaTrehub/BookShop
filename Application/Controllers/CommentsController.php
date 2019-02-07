<?php

namespace Application\Controllers;

use Application\Services\CommentsService;
use Application\Services\BookService;


class CommentsController extends BaseController{

    public $currentUser = -1;

    public function commentListByBookAction($id){

        $commentService = new CommentsService();
        $bookService = new BookService();

        if( isset($_COOKIE["cookie_user"])){
            $CookieUser = unserialize($_COOKIE["cookie_user"]);
        }//if
        else if ( isset($_SESSION['session_user']) ){
            $CookieUser = unserialize($_SESSION['session_user']);
        }//else if
        if( isset($_COOKIE["admin"])){
            $CookieUser = unserialize($_COOKIE["admin"]);
        }//if
        else if ( isset($_SESSION['admin']) ){
            $CookieUser = unserialize($_SESSION['admin']);
        }//else if
        else {
            $CookieUser = null;
        }//else

        if(!$CookieUser){
            $template = $this->twig->load('ErrorPages/404-not-found.twig');

            echo $template->render();
            return;

        }//if

        $currentUser = $CookieUser['userID'];


        $book = $bookService->GetBookById($id);

        if(!$book){

            $template = $this->twig->load('ErrorPages/404-not-found.twig');

            echo $template->render();
            return;

        }//if

        $comments = $commentService->GetCommentsByBookId($id);

        $template = $this->twig->load('Comment/comment-list.twig');

        $commentWithUser = [];

        for($i=0;$i < count($comments); $i++ ){

            $dateStr = date("d-m-Y H:i:s", $comments[$i]->created);
            $commentWithUser[$i] = [
                'comment' => $comments[$i],
                'user' => $commentService->GetUser($comments[$i]->userID),
                'date' => $dateStr
            ];

        }//for

        echo $template->render(array(
                'comments' => $commentWithUser,
                'book' => $book,
                'currentUser' => $currentUser
            )
        );
    }//commentListAction
    public function commentListPublicByBookAction($id){

        $commentService = new CommentsService();
        $bookService = new BookService();

        if( isset($_COOKIE["cookie_user"])){
            $CookieUser = unserialize($_COOKIE["cookie_user"]);
        }//if
        else if ( isset($_SESSION['session_user']) ){
            $CookieUser = unserialize($_SESSION['session_user']);
        }//else if
        if( isset($_COOKIE["admin"])){
            $CookieUser = unserialize($_COOKIE["admin"]);
        }//if
        else if ( isset($_SESSION['admin']) ){
            $CookieUser = unserialize($_SESSION['admin']);
        }//else if
        else {
            $CookieUser = null;
        }//else

        if(!$CookieUser){
            $template = $this->twig->load('ErrorPages/404-not-found.twig');

            echo $template->render();
            return;

        }//if

        $currentUser = $CookieUser['userID'];


        $book = $bookService->GetBookById($id);

        if(!$book){

            $template = $this->twig->load('ErrorPages/404-not-found.twig');

            echo $template->render();
            return;

        }//if

        $comments = $commentService->GetCommentsByBookId($id);

        $template = $this->twig->load('public/Comments/comment-list.twig');
//
//        $commentWithUser = [];
//
//        for($i=0;$i < count($comments); $i++ ){
//
//            //$dateStr = date("d-m-Y H:i:s", (int)$comments[$i]->created);
//            $commentWithUser[$i] = [
//                'comment' => $comments[$i],
//                'user' => $commentService->GetUser($comments[$i]->userID),
//                //'date' => $dateStr
//            ];
//
//        }//for

        echo $template->render(array(
                'comments' => $comments,
                'book' => $book,
                'currentUser' => $currentUser
            )
        );
    }//commentListAction

    public function commentListAction(){

        $commentService = new CommentsService();
        $template = $this->twig->load('Comment/comment-list.twig');

        $comments = $commentService->GetCommentsList();
        $statuses = $commentService->GetStatuses();


        //echo var_dump($statuses);

        echo $template->render(array(
                'comments' => $comments,
                'statuses' => $statuses
            )
         );
    }//commentListAction

    public function commentModerationListAction($id){

        $commentService = new CommentsService();

        $comments = $commentService->GetCommentByStatusId($id);
        $statuses = $commentService->GetStatuses();

        $template = $this->twig->load('Comment/moderated-comment-list.twig');

        $commentWithUser = [];

        for($i=0;$i < count($comments); $i++ ){
            $book = $commentService->GetBookTitle($comments[$i]->bookID);
            $dateStr = date("d-m-Y H:i:s", $comments[$i]->created);
            $commentWithUser[$i] = [
                'comment' => $comments[$i],
                'user' => $commentService->GetUser($comments[$i]->userID),
                'date' => $dateStr,
                'book' => $book
            ];

        }//for

        echo $template->render(array(
                'comments' => $commentWithUser,
                'statuses'=> $statuses,
                'selectedId'=> $id
            )
         );
    }//commentListAction
    public function commentModerationMoreAction($id){

        $commentService = new CommentsService();

        $limit = $this->request->GetGetValue('limit');
        $offset = $this->request->GetGetValue('offset');

        $comments = $commentService->GetCommentByStatusId($id, $limit, $offset);

        $commentWithUser = [];

        for($i=0; $i < count($comments); $i++ ){
            $book = $commentService->GetBookTitle($comments[$i]->bookID);
            $dateStr = date("d-m-Y H:i:s", $comments[$i]->created);
            $commentWithUser[$i] = [
                'comment' => $comments[$i],
                'user' => $commentService->GetUser($comments[$i]->userID),
                'date' => $dateStr,
                'book' => $book
            ];

        }//for

        $this->json( 200 , array(
            'status' => 200,
            'comments' => $commentWithUser,

        ) );

    }//commentListAction

    public function commentMoreAction($id){

        $commentService = new CommentsService();

        $limit = $this->request->GetGetValue('limit');
        $offset = $this->request->GetGetValue('offset');

        $comments = $commentService->GetCommentsByBookId((int)$id, (int)$limit , (int)$offset);

        $book = $commentService->GetBookTitle($id);

        $commentWithUser = [];

        for($i=0;$i < count($comments); $i++ ){

            $dateStr = date("d-m-Y H:i:s", $comments[$i]->created);
            $commentWithUser[$i] = [
                'comment' => $comments[$i],
                'user' => $commentService->GetUser($comments[$i]->userID),
                'date' => $dateStr,

            ];

        }//for

        $this->json( 200 , array(
            'status' => 200,
            'comments' => $commentWithUser,
            'book' => $book,

        ) );

    }//commentListAction

    public function addCommentPageAction( $id ){

        $bookService = new BookService();

        if( isset($_COOKIE["cookie_user"])){
            $CookieUser = unserialize($_COOKIE["cookie_user"]);
        }//if
        else if ( isset($_SESSION['session_user']) ){
            $CookieUser = unserialize($_SESSION['session_user']);
        }//else if
        else {
            $CookieUser = null;
        }//else

        if(!$CookieUser){

            $template = $this->twig->load('ErrorPages/404-not-found.twig');

            echo $template->render();
            return;

        }//if

        $currentUser = $CookieUser['userID'];

        $book = $bookService->GetBookById($id);

        if(!$book){

            $template = $this->twig->load('ErrorPages/404-not-found.twig');

            echo $template->render();
            return;

        }//if

        $template = $this->twig->load('Comment/add-comment.twig');
        echo $template->render( array(
            'userID' => $currentUser,
            'bookID' => $id,
        ) );
        
    }

    public function publicAddCommentPageAction( $id ){

        $bookService = new BookService();

        $book = $bookService->GetBookById($id);

        $template = $this->twig->load('public/Comments/add-comment.twig');
        echo $template->render( array(
            'userID' => $this->currentUser,
            'bookID' => $id,
        ) );

    }

    public function addCommentAction(){

        $text = $this->request->GetPostValue('text');
        $bookId = $this->request->GetPostValue('bookId');

        $len = iconv_strlen ( $text );

        if( $len > 1500 ){

            $this->json( 400, array(

                'status' => '400',
                'message' => "Комментарий не должен превышать 1500 символов!",

            ) );

        }//if

        $bookService = new BookService();
        $commentsService = new CommentsService();

        if ( $len > 4 && $bookService->GetBookById($bookId) && $this->currentUser ) {

            $time = time();
            $result = $commentsService->AddComment( $text, $bookId, $this->currentUser['userID'], $time);

            if($result){

                $this->json( 200, array(
                    'status' => '200'
                ) );

            }//if


            $this->json( 400, array(
                'status' => '400'
            ) );


        } else {

            $this->json( 400, array(
                'status' => '400'
            ) );
        }

    }//addCommentAction

    public function deleteCommentAction($id){

        $commentService = new CommentsService();

        $commentService->DeleteCommentByID( $id );

        $this->json( 200 , array(
            'code' => 200,
            'commentID' => $id
        ) );


    }//deleteCommentAction

    public function updateCommentAction(   ){



        $commentText = $this->request->GetPutValue('text');
        $commentID = $this->request->GetPutValue('commentID');
        $statusID = $this->request->GetPutValue('statusID');

        $commentService = new CommentsService();

        if (iconv_strlen ( $commentText ) > 4
            ) {

            if (iconv_strlen($commentText) > 1500) {
                $commentText = substr($commentText, 0, 1495);
                $commentText .= "...";
            };
            $time = time();
            $result = $commentService->UpdateCommentByID($commentID, $commentText, $time , $statusID);

            $this->json(200, array(
                'code' => 200,
                'result' => $result,
                'text' => $commentText
            ));

        }//if
    }//updateCommentAction

    public function updateCommentStatusAction(   ){




        $commentID = $this->request->GetPutValue('commentID');
        $commentStatus = $this->request->GetPutValue('commentStatus');


        $commentService = new CommentsService();


            $time = time();
            $result = $commentService->UpdateCommentStatusByID((int)$commentID, (int)$commentStatus, $time);

            $this->json(200, array(
                'code' => 200,
                'result' => $result,

            ));

    }//updateCommentStatusAction

}//CommentsController