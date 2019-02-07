<?php
/**
 * Created by PhpStorm.
 * User: elena
 * Date: 19.12.2018
 * Time: 19:41
 */

namespace Application\Controllers;

use Application\Services\PersonalPageService;

class PersonController extends BaseController{

    public function getPersonAction(){

        $template = $this->twig->load('public/Person/person.twig');

        try {

            $personalPageService = new PersonalPageService();


            if($this->currentUser !== null){
                //получаем данные о пользователе
                $user = $personalPageService->GetUserData(
                    [
                        'userID' => $this->currentUser['userID']
                    ]
                );
            }//if
            else{
                $user = null;
            }//else

            echo $template->render(
                array(
                    'userStorage' => $this->currentUser ? $this->currentUser['userID'] : null,
                    'user' => $user
                )
            );

        }//try
        catch (\Exception $ex){

            echo "<pre>";
            print_r($ex);
            echo "<pre>";

            include '../Application/Views/Errors/InternalError.php';

        }//catch

    }//getGenresAction

    public function EditPersonDataAction(){

        $template = $this->twig->load('public/Person/edit-person-data.twig');

        try{

            //данные о пользователе из cookie или сессии
            if( isset($_COOKIE["cookie_user"]) ){

                $userStorage = unserialize($_COOKIE["cookie_user"]);

            }//if
            else{

                $userStorage = unserialize($_SESSION["session_user"]);

            }//else

            $personalPageService = new PersonalPageService();

            //получаем данные о пользователе
            $user = $personalPageService->GetUserData( [ 'userID' => $userStorage['userID'] ] );

            echo $template->render( array( 'userStorage' => $userStorage, 'user' => $user ) );

        }//try
        catch (\Exception $ex){

            echo "<pre>";
            print_r($ex);
            echo "<pre>";

            include '../Application/Views/Errors/InternalError.php';

        }//catch

    }//EditPersonalDataAction

    public function ChangePasswordAction(){

        $template = $this->twig->load('public/Person/change-person-password.twig');

        try{

            if( isset($_COOKIE["cookie_user"]) ){

                $user = unserialize($_COOKIE["cookie_user"]);

            }//if
            else{

                $user = unserialize($_SESSION["session_user"]);

            }//else

            echo $template->render( array( 'user' => $user ) );

        }//try
        catch(\Exception $ex){

            echo "<pre>";
            print_r($ex);
            echo "<pre>";

            include '../Application/Views/Errors/InternalError.php';

        }//catch

    }//ChangePasswordAction

}//PersonController