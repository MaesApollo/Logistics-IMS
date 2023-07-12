$(function () {
  $('#add_department').validate({
     highlight: function (input) {
         console.log(input);
         $(input).parents('.form-line').addClass('error');
         $(".register-msg").text("");
     },
     unhighlight: function (input) {
         $(input).parents('.form-line').removeClass('error');
     },
     errorPlacement: function (error, element) {
         $(element).parents('.input-group').append(error);
         errorClass: "invalid";
     },
     submitHandler: function (form) {
         AddDeparment();
     }
 });
});
