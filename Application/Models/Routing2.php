<?php


return array(
    'get' => [
        '/home' => 'HomeController@indexAction',
        '/' => 'HomeController@indexAction',
        '/authors' => 'AuthorController@authorListAction',
        '/author/(\d+)' => 'AuthorController@getAuthorAction',
        '/orderdetails' => 'OrderDetailsController@orderDetailsListAction',
        '/add-orderdetails' => 'OrderDetailsController@addOrderDetailsAction',
    ],
    'post' => [
        '/book' => 'BookController@createBookAction',
        '/author' => 'AuthorController@addAuthorAction',
        '/add-orderdetails' => 'OrderDetailsController@addOrderDetailsDataAction',
    ],
    'delete' => [
        '/author/(\d+)' => 'AuthorController@deleteAuthorAction',
        '/delete-order-details/(\d+)' => 'OrderDetailsController@deleteOrderDetailsAction',
    ]
);

