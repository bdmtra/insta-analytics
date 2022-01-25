global.number_format = function (number, decimals, dec_point = '.', thousands_point = ',') {

    if(number == null || !isFinite(number)) {
        throw new TypeError("number is not valid");
    }

    if(!decimals) {
        let len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }

    number = parseFloat(number).toFixed(decimals);

    number = number.replace('.', dec_point);

    let splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);

    return number;
}

window.onload=function(){
    let formAccountStore = document.querySelector("#form-account-store");
    if (formAccountStore !== null) {
        formAccountStore.addEventListener("submit", function (e) {
            document.querySelector("#form-account-store-help-text").innerHTML = 'Searching profile ...';
        });
    }
}
