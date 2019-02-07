"use strict";

window.ValidatorConst = {
    USER_LOGIN_VALIDATOR: /^[a-z\d]{4,16}$/i,
    USER_PASSWORD_VALIDATOR: /^[a-z_?!^%()\d]{6,30}$/i,
    USER_EMAIL_VALIDATOR: /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i,
};

(function () {

    $('document').ready(function () {


        //кнопка "Войти" на странице авторизации
        $('#Login').click( function() {

            let loginOrEmail = $('#loginOrEmailInput').val();
            let password = $('#passwordInput').val();

           //проверяем установку "Запомнить меня"
           let isRememberMeChecked = $('#rememberMeCheckBox').is(':checked');

            //проверка на пустое в поле логина
            if(!loginOrEmail){

                $('#errorInput')
                    .text('Забыли ввести Логин или Email')
                    .fadeIn(500)
                    .delay(3000)
                    .fadeOut(2000);

                return;

            }//if

            //проверка на пустое в поле пароля
            if(!password){

                $('#errorInput')
                    .text('Забыли ввести свой пароль')
                    .fadeIn(500)
                    .delay(3000)
                    .fadeOut(2000);

                return;

            }//if

            //проверка поля логина/email на корректность ввода
            if( !window.ValidatorConst.USER_LOGIN_VALIDATOR.test(loginOrEmail) &&
                !window.ValidatorConst.USER_EMAIL_VALIDATOR.test(loginOrEmail) ){

                $('#errorInput')
                    .text('Логин/Email содержит не корректные символы.')
                    .fadeOut(500)
                    .delay(3000)
                    .fadeIn(2000);

                return;

            }//if

            let url = `${window.paths.AjaxServerUserUrl}${window.paths.Login}`;

            $.ajax({
                'url': url,
                'type': 'POST',
                'data': {
                    login: loginOrEmail,
                    password: password,
                    rememberMeCheckbox: isRememberMeChecked
                },//data
                'success': (data) => {

                    console.log('data' , data);

                    if(+data.code === 200){

                        location.href = `${window.paths.AjaxServerUrl}authors`;

                    }//if

                },//seccess
                statusCode: {
                    '401': ()=>{

                        $('#errorInput')
                            .html("Пользователь не найден!<br> Вы можете зарегестрироваться.")
                            .fadeIn(750)
                            .delay(2500)
                            .fadeOut(750);

                    },
                    '405': ()=>{

                        $('#errorInput')
                            .html("Ваш Email не подтверждён!<br> Проверьте свой Email.")
                            .fadeIn(750)
                            .delay(2500)
                            .fadeOut(750);

                    },
                }//statusCode

            });

        });

    });

})();