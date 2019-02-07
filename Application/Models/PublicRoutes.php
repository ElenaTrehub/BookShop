<?php
/**
 * Created by PhpStorm.
 * User: teacher
 * Date: 10.01.2019
 * Time: 10:54
 */

return array(

    'get' => [

//        [
//            'path' => '/' ,
//            'action' => 'HomeController@indexAction',
//            'protected' => true
//        ],
        '/comments-new/(\d+)' => 'CommentsController@publicAddCommentPageAction',
//        '/comments-new/(\d+)' => 'HomeController@indexAction',
        '/comments/(\d+)' => 'CommentsController@commentListPublicByBookAction',
        '/' => 'HomeController@indexAction',
        '/home' => 'HomeController@indexAction',
        '/cart' => 'CartController@cartAction',
        '/authorize' => 'AuthorizeController@authorizeAction',
        '/registration'=>'UserController@registration',
        '/person' => 'PersonController@getPersonAction',
        '/book/(\d+)' => 'BookController@getPublicBookAction',
        '/orders' => 'OrdersController@UserDealInfoByIdAction',
        '/ordersByUser/(\d+)/(\d+)'=> 'OrdersController@UserDealInfoById',
        '/order/(\d+)' => 'OrdersController@userOrderDetailAction',
        '/ordersUserDetailsOffset/(\d+)/(\d+)/(\d+)' => 'OrdersController@userOrderDetail',
        '/edit-person-data' => 'PersonController@EditPersonDataAction',
        '/change-person-password' => 'PersonController@ChangePasswordAction',
        '/placeOrder' => 'OrderController@PlaceOrderAction',
    ],
    'post' => [
        '/addUser'=>'UserController@addUser',
        '/login' => 'AuthorizeController@LoginAction',
        '/add_comment' => 'CommentsController@addCommentAction',
        '/search' => 'SearchController@LoadSearchPage'
    ],
    'put' => [

    ],
    'delete' => [

    ]

);