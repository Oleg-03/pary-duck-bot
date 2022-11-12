const input = document.querySelector("input");

document.querySelector(".autorization_button").addEventListener("click", function(){
    if(input.value != ""){
        $.ajax({
            url: 'http://localhost/web/autorization.php',
            method: 'post',
            dataType: 'html',
            data: {
                "autorization_code": input.value
            } 
           /* success: function(data){
                console.log(data);
            }*/
        });
    }
});
    