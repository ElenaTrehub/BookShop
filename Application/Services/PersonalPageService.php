<?php
/**
 * Created by PhpStorm.
 * User: dreamcast
 * Date: 29.12.2018
 * Time: 2:01
 */

namespace Application\Services;


use Application\Controllers\BaseController;
use Application\Utils\MySQL;

use Bcrypt\Bcrypt;

class PersonalPageService  {

    //получение данных о пользователе
    public function GetUserData( $params = [] ){

        $userID = +$params['userID'];

        //получаем персональные данные о текущем пользователе
        $userStm = MySQL::$db->prepare("SELECT userLogin, userEmail, firstName, lastName, middleName, phoneNumber FROM users WHERE userID = :userID");
        $userStm->bindParam('userID', $userID, \PDO::PARAM_INT);
        $userStm->execute();

        $userData = $userStm->fetch(\PDO::FETCH_OBJ);

        //получаем путь к аватарке(фотографии) пользователя
        $userAvatarStm = MySQL::$db->prepare("SELECT userImagePath FROM useravatar WHERE userID = :userID");
        $userAvatarStm->bindParam('userID', $userID, \PDO::PARAM_INT);
        $userAvatarStm->execute();

        $userAvatar = $userAvatarStm->fetch(\PDO::FETCH_OBJ);

        $user = array(

            'userLogin' => $userData->userLogin,
            'userEmail' => $userData->userEmail,
            'userFirstName' => $userData->firstName,
            'userLastName' => $userData->lastName,
            'userMiddleName' => $userData->middleName,
            'userPhoneNumber' => $userData->phoneNumber

        );

        if($userAvatar){
            $user['userAvatar'] = $userAvatar->userImagePath;
        }//if

        return $user;

    }//GetUserData

    //сохранение аватара(фотографии) пользователя
    public function ChangeUserAvatar( $params = [] ){

        $userID = +$params['userID'];

        //получаем файл аватара(фоторграфии) пользователя
        if( isset( $_FILES['avatarFile'] ) ){

            //удаление старого файла аватара(фотографии пользователя)
            $userAvatarDirectory = "images/avatars/{$userID}/*";

            if(!file_exists("images/avatars/{$userID}")){

                mkdir("images/avatars/{$userID}");

            }//if

            $files = glob($userAvatarDirectory); // получение всех файлов в папке пользователя

            foreach($files as $file){ // итерируем файлы

                if(is_file($file)){
                    unlink($file); // удалить файл
                }//if

            }//foreach

            //получаем расширение полученного файла
            $fileExtension = strrchr($_FILES['avatarFile']['name'], ".");

            $time = time();

            //имя файла для аватара(фотографии) пользователя
            $fileName = "Avatar_{$time}{$fileExtension}";

            //полный путь к аватарке(фотографии) пользователя
            $userAvatarDirectoryPath = "images/avatars/{$userID}/{$fileName}";

            //проверяем наличие аватара(фотографии) пользователя в базе
            $checkUserAvatarStm = MySQL::$db->prepare("SELECT userImagePath FROM useravatar WHERE userID = :userID");
            $checkUserAvatarStm->bindParam('userID', $userID, \PDO::PARAM_INT);
            $checkUserAvatarStm->execute();

            $userAvatar = $checkUserAvatarStm->fetch(\PDO::FETCH_OBJ);

            //если у пользователя есть аватар
            if($userAvatar){//обновляем текущий аватар(фотографию)

                if( !move_uploaded_file($_FILES['avatarFile']['tmp_name'] , $userAvatarDirectoryPath) ){

                    throw new \Exception('File upload error!');

                }//if

                //обновляем путь в базе данных
                $stm = MySQL::$db->prepare("UPDATE useravatar SET  userImagePath = :newUserAvatar WHERE userID = :userID");
                $stm->bindParam('newUserAvatar', $userAvatarDirectoryPath, \PDO::PARAM_STR);
                $stm->bindParam('userID', $userID, \PDO::PARAM_INT);
                $result = $stm->execute();

                return [
                    'status' => $result,
                    'path' =>$userAvatarDirectoryPath
                ];

            }//if
            else{ //создаём новую директорию и сохраняем новый аватар(фотографию) пользователя

                //если дериктории аваторов не существует
                if( !file_exists("images/avatars") ){

                    //создаём директорию
                    mkdir("images/avatars");

                }//if

                //создаём папку с ID пользователя
                mkdir("images/avatars/{$userID}");

                if( !move_uploaded_file($_FILES['avatarFile']['tmp_name'] , $userAvatarDirectoryPath) ){

                    throw new \Exception('File upload error!');

                }//if

                //создаём запись в базе данных
                $stm = MySQL::$db->prepare("INSERT INTO useravatar VALUES ( DEFAULT , :userID , :userAvatarPath )");
                $stm->bindParam('userID', $userID , \PDO::PARAM_INT);
                $stm->bindParam('userAvatarPath', $userAvatarDirectoryPath , \PDO::PARAM_STR);
                $result = $stm->execute();

                //если создание записи не удалось
                if( $result === false ){

                    $exception = new \stdClass();
                    $exception->errorCode = MySQL::$db->errorCode ();
                    $exception->errorInfo = MySQL::$db->errorInfo ();

                    return $exception;

                }//if

            }//else

        }//if

    }//ChangeUserAvatar

    //обновление персональной информации пользователя
    public function UpdateUserPersonalData( $params = [] ){

        //получаем новые персональные данные
        $userID = +$params['userID'];
        $userEmail = $params['userEmail'];
        $userPhoneNumber = $params['userPhone'];
        $userLastName = $params['userLastName'];
        $userFirstName = $params['userFirstName'];
        $userMiddleName = $params['userMiddleName'];

        //обновляем запись в базе данных
        $stm = MySQL::$db->prepare("UPDATE users SET userEmail = :userEmail, firstName = :userFirstName, lastName = :userLastName, middleName = :userMiddleName, phoneNumber = :userPhoneNumber WHERE userID = :userID");
        $stm->bindParam('userEmail', $userEmail, \PDO::PARAM_STR);
        $stm->bindParam('userFirstName', $userFirstName, \PDO::PARAM_STR);
        $stm->bindParam('userLastName', $userLastName, \PDO::PARAM_STR);
        $stm->bindParam('userMiddleName', $userMiddleName, \PDO::PARAM_STR);
        $stm->bindParam('userPhoneNumber', $userPhoneNumber, \PDO::PARAM_STR);
        $stm->bindParam('userID', $userID, \PDO::PARAM_INT);

        $result = $stm->execute();

        if($result){
            return array( 'code' => 200 );
        }//if
        else {
            return array( 'code' => 400 );
        }//else

    }//UpdateUserPersonalData

    //обновление пароля пользователя
    public function UpdateUserPassword( $params = [] ){

        //получаем данные для обновления пароля
        $userID = intval($params['userID']);
        $oldPassword = $params['oldPassword'];
        $newPassword = $params['newPassword'];
        $confirmNewPassword = $params['confirmNewPassword'];

        $passwordPattern = '/^[a-z_?!^%()\d]{6,30}$/iu';

        //проверяем корректность новых паролей
        if( !preg_match( $passwordPattern, $oldPassword ) ){

            return array( 'code' => 600); //старый пароль не соответствует паттерну

        }//if

        if( !preg_match( $passwordPattern, $newPassword ) ){

            return array( 'code' => 601); //новый пароль не соответствует паттерну

        }//if

        if( !preg_match( $passwordPattern, $confirmNewPassword ) ){

            return array( 'code' => 602); //подтверждение нового пароля не соответствует паттерну

        }//if

        //если ВДРУГ новый пароль не совпадает с подтвеждёнием нового пароля
        if($newPassword !== $confirmNewPassword){

            return array( 'code' => 603);

        }//if

        //проверяем наличие пользователя в базе данных
        $userStm = MySQL::$db->prepare("SELECT * FROM users WHERE userID = :userID");
        $userStm->bindParam('userID', $userID, \PDO::PARAM_INT);
        $userResult = $userStm->execute();

        //если пользователь есть
        if($userResult){

            //шифруем и обновляем пароль пользователя
            $bcrypt = new Bcrypt();
            $bcrypt_version = '2y';

            $encodedNewPassword = $bcrypt->encrypt($newPassword, $bcrypt_version);

            $passwordStm = MySQL::$db->prepare("UPDATE users SET userPassword = :newPassword WHERE userID = :userID");
            $passwordStm->bindParam('newPassword', $encodedNewPassword, \PDO::PARAM_STR);
            $passwordStm->bindParam('userID', $userID, \PDO::PARAM_INT);
            $passwordResult = $passwordStm->execute();

            if($passwordResult){
                return array( 'code' => 200 );
            }//if
            else {
                return array( 'code' => 500 );
            }//else

        }//if
        else{

            return array('code' => 605); //пользователь в базе не найден!

        }//else

    }//UpdateUserPassword

}//PersonalPageService