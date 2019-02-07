<?php


namespace Application\Controllers;

use Application\Services\AuthorizeService;

class AuthorizeController extends BaseController {

    //Загрузка страницы авторизации
    public function authorizeAction(){

        try{

            $template = $this->twig->load('Authorize/authorize.twig');
            echo $template->render();

        }//try
        catch (\Exception $ex) {

            echo "<pre>";
            print_r($ex);
            echo "<pre>";

            include '../Application/Views/Errors/InternalError.php';

        }//catch

    }//authorizeAction

    //запрос на проверку пользователя для авторизации
    public function LoginAction(){

        $authorizeService = new AuthorizeService();

        $login = $this->request->GetPostValue('login');
        $password = $this->request->GetPostValue('password');
        $isRememberMeChecked = $this->request->GetPostValue('rememberMeCheckbox'); //галочка "Запомнить меня"

        $result = $authorizeService->LogIn($login, $password, $isRememberMeChecked);

        $this->json( $result['code'] ,
                      array(
                            'code' => $result['code']
        ) );

    }//LoginAction

}//AuthorizeController