<?php

namespace Application\Services;

use Application\Utils\MySQL;

class OrderService{

    public function UserDealInfoById($userId,$limit, $offset){//my


        $orders = MySQL::$db->prepare("
                SELECT o.*,os.* FROM orders o
                INNER JOIN orderstatus os
                  ON o.orderStatus = os.statusID 
                  
                WHERE userID = :userId 
                ORDER BY orderDatetime DESC 
                LIMIT :offset,:limit
        ");

        $orders->bindParam(':userId', $userId,\PDO::PARAM_INT);
        $orders->bindParam(':offset' , $offset , \PDO::PARAM_INT);
        $orders->bindParam(':limit' , $limit , \PDO::PARAM_INT);
        $orders->execute();

        $result = $orders->fetchAll(\PDO::FETCH_OBJ);

        if($result){

//            for($i=0;$i<count($result);$i++){
//
//                $id = $result[$i]->orderStatus;
//                $statueTitle =  $orders = MySQL::$db->prepare("SELECT statusTitle FROM `orderstatus` WHERE statusID =$id ");
//                $statueTitle->execute();
//                $statueTitleResult = $orders->fetch(\PDO::FETCH_OBJ);
//                $result[$i]->orderStatus = $statueTitleResult;
//
//            }//for

            return $result;

        }//if
        return null;

    }//UserDealInfoById

    public function getDealDetail($id, $limit, $offset){//My

        $detail = MySQL::$db->prepare("SELECT * FROM orderdetails WHERE orderID = :orderID LIMIT :offset,:limit");
        $detail->bindParam(':orderID', $id,\PDO::PARAM_INT);
        $detail->bindParam(':offset' , $offset , \PDO::PARAM_INT);
        $detail->bindParam(':limit' , $limit , \PDO::PARAM_INT);
        $detail->execute();

        $result = $detail->fetchAll(\PDO::FETCH_OBJ);

        return $result;

    }//getDealDetail

    public function getTotalSumByDealId($orderID){

        $stm = MySQL::$db->prepare("SELECT SUM(bookPrice * bookAmount) as `total` FROM orderdetails WHERE orderID = :orderID");
        $stm->bindParam(':orderID' , $orderID , \PDO::PARAM_INT);

        $stm->execute();

        return $stm->fetch(\PDO::FETCH_OBJ);
    }//getTotalSumByDealId

    public function GetOrders( $limit = 10 , $offset = 0 ){

        $stm = MySQL::$db->prepare("SELECT orderID, userID, orderDatetime, orderStatus
                                   FROM orders ORDER BY orderDatetime DESC LIMIT :offset, :limit");

        $stm->bindParam(':offset' , $offset , \PDO::PARAM_INT);
        $stm->bindParam(':limit' , $limit , \PDO::PARAM_INT);
        $stm->execute();

        return $stm->fetchAll(\PDO::FETCH_OBJ);

    }//GetOrders

    public function AddOrder( $userID, $orderStatus, $adress ){

        $stm = MySQL::$db->prepare("INSERT INTO orders(userID, orderDatetime, orderStatus, adressOrder) 
                                    VALUES(  :userID , NOW(), :orderStatusID, :adress)");
        $stm->bindParam(':userID' , $userID , \PDO::PARAM_INT);
        $stm->bindParam(':orderStatusID' , $orderStatus , \PDO::PARAM_INT);
        $stm->bindParam(':adress' , $adress , \PDO::PARAM_STR);
        $stm->execute();

        return  MySQL::$db->lastInsertId();

    }//AddOrder

    public function GetTitleStatusOrderByID($statusId){

        $stm = MySQL::$db->prepare("SELECT  statusTitle
                                   FROM orderstatus WHERE statusID = :statusId");
        $stm->bindParam(':statusId' , $statusId , \PDO::PARAM_INT);
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_OBJ);
    }//GetTitleStatusByID

    public function GetOrderStatuses(){

        $stm = MySQL::$db->prepare("SELECT statusID, statusTitle
                                 FROM orderstatus");
        $stm->execute();

        return $stm->fetchAll(\PDO::FETCH_OBJ);

    }//GetOrderStatuses

    public function GetOrderByID( $id ){

        $stm = MySQL::$db->prepare("SELECT orderID, userID, orderDatetime, orderStatus, adressOrder 
                                    FROM orders WHERE orderID = :id");
        $stm->bindParam(':id' , $id , \PDO::PARAM_INT);
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_OBJ);

    }//GetOrderByID

    public function AddOrdersDetails( $orderID , $bookID, $bookPrice, $bookAmount){

        $stm = MySQL::$db->prepare("INSERT INTO orderdetails (orderID, bookID, bookPrice, bookAmount) 
                                    VALUES( :orderID , :bookID, :bookPrice, :bookAmount)");
        $stm->bindParam(':orderID' , $orderID , \PDO::PARAM_INT);
        $stm->bindParam(':bookID' , $bookID , \PDO::PARAM_INT);
        $stm->bindParam(':bookPrice' , $bookPrice , \PDO::PARAM_STR);
        $stm->bindParam(':bookAmount' , $bookAmount , \PDO::PARAM_INT);
        $stm->execute();

        return  MySQL::$db->lastInsertId();

    }//AddOrdersDetails

    public function GetCountBookInOrderByID($orderID){

        $stm = MySQL::$db->prepare("SELECT COUNT(orderID) FROM orderdetails WHERE orderID = :orderID");
        $stm->bindParam(':orderID' , $orderID , \PDO::PARAM_INT);

        $stm->execute();

        return $stm->fetch();

    }//GetCountBookInOrderByID

    public function GetOrdersDetailsByOrderId( $orderID){

        $stm = MySQL::$db->prepare("SELECT bookID,  bookPrice, bookAmount 
                                    FROM orderdetails 
                                    WHERE orderID = :orderID");
        $stm->bindParam(':orderID' , $orderID , \PDO::PARAM_INT);
        $stm->execute();

        return $stm->fetchAll(\PDO::FETCH_OBJ);

    }//GetOrdersDetails

    public function UpdateStatusOrder($orderID, $statusID){

        $stm = MySQL::$db->prepare("UPDATE orders
                                    SET orderStatus = :statusID
                                    WHERE orderID = :orderID;");
        $stm->bindParam(':orderID' , $orderID , \PDO::PARAM_INT);
        $stm->bindParam(':statusID' , $statusID , \PDO::PARAM_INT);
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_OBJ);
    }//UpdateStatusOrder

}//OrderService
