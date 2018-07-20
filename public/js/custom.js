var currentDate = new Date();
var currentNepaliDate = calendarFunctions.getBsDateByAdDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
var formatedNepaliDate = calendarFunctions.bsDateFormat("%y-%m-%d", currentNepaliDate.bsYear, currentNepaliDate.bsMonth, currentNepaliDate.bsDate);


// $("#to-picker").val(formatedNepaliDate);
$(".to-picker").nepaliDatePicker({
    dateFormat: "%y-%m-%d",
    closeOnDateSelect: true,
    // minDate: formatedNepaliDate,
    // maxDate: formatedNepaliDate
});
$(".from-picker").nepaliDatePicker({
    dateFormat: "%y-%m-%d",
    closeOnDateSelect: true,
    // minDate: formatedNepaliDate,
    // maxDate: formatedNepaliDate
});
$(".from-booking").nepaliDatePicker({
    dateFormat: "%y-%m-%d",
    closeOnDateSelect: true,
    // minDate: formatedNepaliDate,
    maxDate: formatedNepaliDate
});
$(".statement-date").nepaliDatePicker({
    dateFormat: "%y-%m-%d",
    closeOnDateSelect: true,
    // minDate: formatedNepaliDate,
    // maxDate: formatedNepaliDate
});
function eventLog(event) {
    var datePickerData = event.datePickerData;
    var outputData = {
        "type": event.type,
        "message": event.message,
        "datePickerData": datePickerData
    };

    return JSON.stringify(outputData);
    // var output = '<p><code>▸ ' + JSON.stringify(outputData) + '</code></p>';
    // $('.output').append(output);
}
$(".statement-date").on("dateSelect", function (event) {
    eventLog(event);
    $('.statement-date').val(event.datePickerData.bsYear + '-' + event.datePickerData.bsMonth + '-' + event.datePickerData.bsDate);
    try {

        var converter = new DateConverter();
        converter.setNepaliDate(event.datePickerData.bsYear, event.datePickerData.bsMonth, event.datePickerData.bsDate)
        // alert(converter.getEnglishYear()+"-"+converter.getEnglishMonth()+"-"+converter.getEnglishDate())
        $('.statement-date-ad').val(converter.getEnglishYear() + "-" + converter.getEnglishMonth() + "-" + converter.getEnglishDate());
        // converter.setCurrentDate()
        // alert(converter.getNepaliYear()+"/"+converter.getNepaliMonth()+"/"+converter.getNepaliDate())
        // alert( "Weekly day: "+ converter.getDay() )
        // alert( converter.toNepaliString() )

    } catch (err) {
        alert(err.message);
        console.log(err.message);
    }

});
$(".to-picker").on("dateSelect", function (event) {
    eventLog(event);
    $('.to-picker').val(event.datePickerData.bsYear + '-' + event.datePickerData.bsMonth + '-' + event.datePickerData.bsDate);
    try {

        var converter = new DateConverter();
        converter.setNepaliDate(event.datePickerData.bsYear, event.datePickerData.bsMonth, event.datePickerData.bsDate)
        // alert(converter.getEnglishYear()+"-"+converter.getEnglishMonth()+"-"+converter.getEnglishDate())
        $('.to-picker-ad').val(converter.getEnglishYear() + "-" + converter.getEnglishMonth() + "-" + converter.getEnglishDate());
        // converter.setCurrentDate()
        // alert(converter.getNepaliYear()+"/"+converter.getNepaliMonth()+"/"+converter.getNepaliDate())
        // alert( "Weekly day: "+ converter.getDay() )
        // alert( converter.toNepaliString() )

    } catch (err) {
        alert(err.message);
        console.log(err.message);
    }

});
$(".from-picker").on("dateSelect", function (event) {
    eventLog(event);
    $('.from-picker').val(event.datePickerData.bsYear + '-' + event.datePickerData.bsMonth + '-' + event.datePickerData.bsDate);
    try {

        var converter = new DateConverter();
        converter.setNepaliDate(event.datePickerData.bsYear, event.datePickerData.bsMonth, event.datePickerData.bsDate)
        // alert(converter.getEnglishYear()+"-"+converter.getEnglishMonth()+"-"+converter.getEnglishDate())
        $('.from-picker-ad').val(converter.getEnglishYear() + "-" + converter.getEnglishMonth() + "-" + converter.getEnglishDate());
        // converter.setCurrentDate()
        // alert(converter.getNepaliYear()+"/"+converter.getNepaliMonth()+"/"+converter.getNepaliDate())
        // alert( "Weekly day: "+ converter.getDay() )
        // alert( converter.toNepaliString() )

    } catch (err) {
        alert(err.message);
        console.log(err.message);
    }
});
$(".from-booking").on("dateSelect", function (event) {
    eventLog(event);
    $(this).val(event.datePickerData.bsYear + '-' + event.datePickerData.bsMonth + '-' + event.datePickerData.bsDate);
    try {

        var converter = new DateConverter();
        converter.setNepaliDate(event.datePickerData.bsYear, event.datePickerData.bsMonth, event.datePickerData.bsDate)
        // alert(converter.getEnglishYear()+"-"+converter.getEnglishMonth()+"-"+converter.getEnglishDate())
        $('.from-picker-ad').val(converter.getEnglishYear() + "-" + converter.getEnglishMonth() + "-" + converter.getEnglishDate());
        // converter.setCurrentDate()
        // alert(converter.getNepaliYear()+"/"+converter.getNepaliMonth()+"/"+converter.getNepaliDate())
        // alert( "Weekly day: "+ converter.getDay() )
        // alert( converter.toNepaliString() )

    } catch (err) {
        alert(err.message);
        console.log(err.message);
    }
});
$('.choose_bs').change(function(){
   if( $(this).find('option:selected').text()=='BS'){
       $('.bs-date').show();
       $('.ad-date').hide();


   }else if( $(this).find('option:selected').text()=='AD'){
       $('.ad-date').show();
       $('.bs-date').hide();
   }
});
