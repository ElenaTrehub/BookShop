<?php

namespace Application\Services;

use Application\Utils\MySQL;
use Bcrypt\Bcrypt;

class AuthorizeService{

    public function LogIn( $login, $password, $rememberMe){

        $bcrypt = new Bcrypt();
        $rememberMe = filter_var($rememberMe , FILTER_VALIDATE_BOOLEAN);

        //ищем пользователя
        $stm = MySQL::$db->prepare( "SELECT userID, isAdmin, userLogin, userEmail, userPassword, verification FROM users WHERE userLogin = :login OR userEmail = :login" );
        $stm->bindParam(':login', $login, \PDO::PARAM_STR);
        $stm->execute();

        //возвращаем объект из базы данных
        $user = $stm->fetch(\PDO::FETCH_OBJ);

        //есди пользователь не найден
        if(!$user){
            return array(
                'code' => 401,
                'user' => $user
            );
        }//if

        $user->isAdmin = filter_var( $user->isAdmin , FILTER_VALIDATE_INT);

        //данные для сессии и cookie
        $userForSessionAndCookies = array(

            'userID' => $user->userID

        );

        //проверяем пароль пользователя
        $verifyPassword = $bcrypt->verify($password, $user->userPassword);


        //даём разрешение на авторизацию
        if($verifyPassword){

            //проверка на подтверждение своего email
            $isEmailVerified = $user->verification;

            //пользователь не подтвердил свой email
            if(!$isEmailVerified){

                $result = array(
                    'code' => 405,
                    'emailVerify' => $isEmailVerified
                );

                return $result;

            }//if


            //если "Запомнить меня" НЕ отмечена
            if(!$rememberMe){

                if($user->isAdmin !== 1){

                    //записываем пользователя в сессию
                    $_SESSION['session_user'] = serialize($userForSessionAndCookies);

                    unset($_COOKIE['cookie_user']);
                    setcookie("cookie_user", "", 1);

                }//if
                else{

                    //записываем пользователя в сессию
                    $_SESSION['admin'] = serialize($userForSessionAndCookies);

                    unset($_COOKIE['admin']);
                    setcookie("admin", "", 1);

                }//else


            }//if
            else{

                //если "Запомнить меня" отмечена
                $userSerializeResultForCookie = serialize($userForSessionAndCookies);

                if($user->isAdmin !== 1){

                    //сетим данные пользователя в cookie
                    setcookie(
                        'cookie_user' ,
                        $userSerializeResultForCookie ,
                        time()+60*60*24*60
                    );

                }//if
                else{

                    setcookie(
                        'admin' ,
                        $userSerializeResultForCookie ,
                        time()+60*30
                    );

                }//else

            }//else

           //авторизируем пользователя
           return array(
               'code' => 200
           );

        }//if
        else{

            //если пароли не совпадают
            return array(
                'code' => 401,
                'password' => $password
            );

        }//else

    }//LogIn

}//AuthorizeService