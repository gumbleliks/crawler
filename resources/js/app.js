import './bootstrap';


$('#customFile').on("change",function() {
    var i = $(this).prev('label').clone();
    var file = $('#customFile')[0].files[0].name;
    console.log(file);
    $(this).prev('label').text(file);

});
