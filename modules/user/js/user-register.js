CMS.ui.attach.user_register = function(){
   $('#user-register-form').on('submit',function(){
       var form = $(this);
       var pass = form.find('#password');
       var pass1 = form.find('#password_2');
       if(pass.val() != pass1.val()){
           CMS.ui.modal('Указанные пароли не совпадают.');
           return false;
       }
   });
};