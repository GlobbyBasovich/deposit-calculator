function calc() {
    let requestData = JSON.stringify({
        "startDate": $("#startDate").val(), // дата открытия вклада
        "sum": $("#sum").val(), // сумма вклада
        "term": $("#term").val() * ($("#unit").val() == $("#unitMonth").text() ? 1 : 12), // срок вклада в месяцах
        "percent": $("#percent").val(), // процентная ставка, % годовых
        "sumAdd": $("#doSumAdd").is(':checked') ? $("#sumAdd").val() : 0 // сумма ежемесячного пополнения вклада
    });

    $.getJSON("calc.php", "data=" + requestData, function(responseData)
    {
        if ("error" in responseData)
            alert(responseData["error"]);
        else {
            $("#result_wrapper").removeClass("hidden_input");
            $("#result").text("₽ " + responseData["sum"].toFixed(2));
        }
    });
}

function toggleTermMax()
{
    if ($("#unit").val() == $("#unitMonth").text()) {
        $("#term").attr("max", "60");
    }
    else {
        $("#term").attr("max", "5");
    }
}

function toggleSumAdd()
{
    if ($("#doSumAdd").is(':checked')) {
        $("#sumAdd").attr("required", "");
    }
    else {
        $("#sumAdd").removeAttr("required");
    }
    $("#sumAdd_wrapper").toggleClass("hidden_input");
}

new AirDatepicker('#startDate');
$("#mainForm").validate({
    submitHandler: function(form) {
        calc();
    }
});