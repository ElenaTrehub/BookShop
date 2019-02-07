"use strict";

(function () {

    $('document').ready(function () {

        //кнопка "Сменить аватар"
        $('#changeAvatarButton').click( function() {

            console.log('Change Avatar');

            $('#hiddenAvatarBlock').show();

        });//#changeAvatarButton

        //сохранить новую фотографию
        $('#saveNewAvatar').click( async function () {

            //получаем файл
            let avatarFile = $('#avatarFile').prop('files');

            //проверяем наличие файла
            if(avatarFile.length !== 0){

                let newAvatarFile = avatarFile[0];

                //берём расширениеи полученного файла
                let extsn = newAvatarFile.name.substring(newAvatarFile.name.lastIndexOf('.'));

                //допустимые расширения для загрузки
                const extensions = [
                    '.jpg',
                    '.jpeg',
                    '.png',
                    '.bmp',
                ];

                extsn = extsn.toLowerCase();

                //проверяем расширение файла на допустимое
                if( extensions.indexOf( extsn ) ){

                    $('#errorInput').text('Тип файла некорректен')
                        .fadeIn(500)
                        .delay( 5000 )
                        .fadeOut( 500 );

                    return;

                }//if

                let avatarData = new FormData();
                avatarData.append('avatarFile', newAvatarFile);

                try{

                    let url = `${window.paths.AjaxServerUrl}${window.paths.SaveNewAvatar}`;

                    let newAvatarResponse = await $.ajax({

                        url: url,
                        method: 'POST',
                        contentType: false,
                        processData: false,
                        data: avatarData

                    });

                    if( +newAvatarResponse.code === 200 ){

                        $('#successMessage').fadeIn(200).delay(5000).fadeOut(1500);

                        $('#userAvatar').attr('src' , `${newAvatarResponse.path}`);
                        $('#hiddenAvatarBlock').hide();

                    }//if
                    else{

                        $('#errorMessage').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                    }//else

                }//try
                catch(ex){

                    $('#errorMessage').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                }//catch

            }//if

            $('#hiddenAvatarBlock').hide();

        });//#saveNewAvatar

        //подтверждение изменений личной информации
        $('#ConfirmChangesModalButton').click( async function () {

            //получаем новые значения персональных данных
            let newEmail = $('#newEmailInput').val();
            let newPhoneNumber = $('#newPhoneNumberInput').val();
            let newLastName = $('#newLastNameInput').val();
            let newFirstName = $('#newFirstNameInput').val();
            let newMiddleName = $('#newMiddleNameInput').val();

            //Проводим проверку полей на пустое значение
            if(newEmail.length === 0){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле Email не должно быть пустым.').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(newPhoneNumber.length === 0){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле телефона не должно быть пустым.').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(newLastName.length === 0){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле фамилии не должно быть пустым.').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(newFirstName.length === 0){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле имени не должно быть пустым.').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(newMiddleName.length === 0){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле отчества не должно быть пустым.').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            //проводим проверку на правильность воода новых персональных данных
            if(!window.ValidatorConst.USER_EMAIL_VALIDATOR.test(newEmail)){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле Email содержит не корректные символы!').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(!window.ValidatorConst.USER_PHONE_VALIDATOR .test(newPhoneNumber)){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле телефона содержит не корректные символы!').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(!window.ValidatorConst.USER_NAMES_VALIDATOR .test(newLastName)){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле фамилии содержит не корректные символы!').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(!window.ValidatorConst.USER_NAMES_VALIDATOR .test(newFirstName)){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле имени содержит не корректные символы!').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            if(!window.ValidatorConst.USER_NAMES_VALIDATOR .test(newMiddleName)){

                $('#exampleModalCenter').modal('hide');
                $('#errorMessage').text('Поле отчества содержит не корректные символы!').fadeIn(500).delay( 5000 ).fadeOut( 500 );

                return;

            }//if

            let url = `${window.paths.AjaxServerUrl}${window.paths.SaveNewPersonalData}`;

            try{

                let response = await $.ajax({

                    'url': url,
                    'method': 'PUT',
                    'data':{
                        newEmail: newEmail,
                        newPhoneNumber: newPhoneNumber,
                        newLastName: newLastName,
                        newFirstName: newFirstName,
                        newMiddleName:newMiddleName
                    },

                });

                console.log('data: ', response);

                if(+response.code === 200){

                    $('#exampleModalCenter').modal('hide');
                    $('#successMessage').text('Данные успешно обновлены!').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                    return;

                }//if

                $('#errorMessage')
                    .text("Пользователь с такими Email или телефоном уже есть!")
                    .fadeIn(750)
                    .delay(2500)
                    .fadeOut(750);


            }//try
            catch( ex ){

                console.log('Exception: ', ex);
                $('#exampleModalCenter').modal('hide');

                $('#errorMessage')
                    .text(ex.responseJSON.message)
                    .fadeIn(750)
                    .delay(2500)
                    .fadeOut(750);



            }//catch



        });//ConfirmChangesModalButton

        //изменение пароля пользователя
        $('#ConfirmPasswordChangesModalButton').click(function () {

            let oldPassword = $('#oldPasswordInput').val();
            let newPassword = $('#newPasswordInput').val();
            let confirmNewPassword = $('#confirmNewPassword').val();

            //проверки на пустоту полей паролей
            if(oldPassword.length === 0){

                $('#errorMessage').text('Поле старого пароля не может быть пустым').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            if(newPassword.length === 0){

                $('#errorMessage').text('Поле нового пароля не может быть пустым').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            if(confirmNewPassword.length === 0){

                $('#errorMessage').text('Поле подтверждения нового пароля не может быть пустым').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            if(!/^[a-z_?!^%()\d]{6,30}$/i.test(oldPassword)){

                $('#errorMessage').text('Старый пароль содержит не корректные симовлы').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            if(!/^[a-z_?!^%()\d]{6,30}$/i.test(newPassword)){

                $('#errorMessage').text('Новый пароль содержит не корректные симовлы').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            if(!/^[a-z_?!^%()\d]{6,30}$/i.test(confirmNewPassword)){

                $('#errorMessage').text('Старый пароль содержит не корректные символы').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            if(newPassword !== confirmNewPassword){

                $('#errorMessage').text('Новый пароль не совпадает!').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                $('#exampleModalCenter').modal('hide');

                return;

            }//if

            let url = `${window.paths.AjaxServerUrl}${window.paths.ChangePassword}`;

            $.ajax({

               'url': url,
               'method': 'PUT',
               'data':{
                   oldPassword: oldPassword,
                   newPassword: newPassword,
                   confirmNewPassword: confirmNewPassword
               },
               'success': (data)=>{

                   if(+data.code === 200){

                       $('#successMessage').text('Пароль успешно изменён!').fadeIn(500).delay( 5000 ).fadeOut( 500 );
                       $('#exampleModalCenter').modal('hide');

                   }//if

               },//success
               'statusCode':{

                   '500': ()=>{

                       $('#errorInput')
                           .text("Ошибка загрузки данных")
                           .fadeIn(750)
                           .delay(2500)
                           .fadeOut(750);

                       $('#exampleModalCenter').modal('hide');

                   },
                   '600':()=>{

                       $('#errorInput')
                           .text("Ошибка загрузки данных. Пришёл не корректный старый пароль")
                           .fadeIn(750)
                           .delay(2500)
                           .fadeOut(750);

                       $('#exampleModalCenter').modal('hide');

                   },
                   '601':()=>{

                       $('#errorInput')
                           .text("Ошибка загрузки данных. Пришёл не корректный новый пароль")
                           .fadeIn(750)
                           .delay(2500)
                           .fadeOut(750);

                       $('#exampleModalCenter').modal('hide');

                   },
                   '602':()=>{

                       $('#errorInput')
                           .text("Ошибка загрузки данных. Пришёл не корректный подтверждённый пароль")
                           .fadeIn(750)
                           .delay(2500)
                           .fadeOut(750);

                       $('#exampleModalCenter').modal('hide');

                   },
                   '603':()=>{

                       $('#errorInput')
                           .text("Ошибка загрузки данных. Новые пароли не совпадают!")
                           .fadeIn(750)
                           .delay(2500)
                           .fadeOut(750);

                       $('#exampleModalCenter').modal('hide');

                   },
                   '605':()=>{

                       $('#errorInput')
                           .text("Ошибка загрузки данных. Пользователь не найден!")
                           .fadeIn(750)
                           .delay(2500)
                           .fadeOut(750);

                       $('#exampleModalCenter').modal('hide');

                   },

               }
            });

        });//changePassword

    });//document.ready

})();



