function copytext(text){
    var websiteField = document.getElementById('clipboard');
    websiteField.value = text;
    websiteField.focus();
    websiteField.select();
    websiteField.setSelectionRange(0, websiteField.value.length);
    var copy = document.execCommand('copy');
    console.log(copy);
}