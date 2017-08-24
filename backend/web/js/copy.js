function copytext(text){
    var websiteField = document.getElementById('clipboard');
    websiteField.value = text;
    websiteField.focus();
    websiteField.select();
    websiteField.setSelectionRange(0, websiteField.value.length);
    var copy = document.execCommand('copy');
    swal({
        title: "網址已經複制",
        text: text,
        timer: 2000,
        showConfirmButton: true
    });
}