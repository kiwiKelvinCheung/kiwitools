function copytext(text){
    if(text!='error'){
        var websiteField = document.getElementById('clipboard');
        websiteField.value = text;
        websiteField.focus();
        websiteField.select();
        websiteField.setSelectionRange(0, websiteField.value.length);
        var copy = document.execCommand('copy');
        swal({
            title: "網址已經複制",
            text: text,
            type:"success",
            //timer: 2000,
            showConfirmButton: true
        });
    }else{
        swal({
            title: "此貼己被使用",
            text: "請再找其他貼...sad",
            type:"error",
            //timer: 2000,
            showConfirmButton: true
        });
    }
}