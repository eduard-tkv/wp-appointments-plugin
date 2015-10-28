/*
 * Helpers.js 
 * It is part of delta-appointments-plugin.js
 */    
    
    // Declaring global vars. For example edit function can only be called after search execution
    // and will be populated with values that can be checked against later on. 
    // While performing search a global var is assigned some data, and this data for example an id
    // can be used to retrieve data for edit form.
    var editKeys = [];
    var editValues = [];
    var allApptTimes = {};
     
    // editPersDetailsID, editAddressId are used when updating/editing a record
    // They populate an update form with previously retrieved values
    // buttonSave#284
    var editPersDetailsId = [];
    var editAddressId = [];
    var newValuesKeysPers = [];
    var newValuesKeysAddy = [];
    
    editPersDetailsId[0] = "#edit_First_Name";
    editPersDetailsId[1] = "#edit_Middle_Name";
    editPersDetailsId[2] = "#edit_Last_Name";
    editPersDetailsId[3] = "#edit_Email";
    editPersDetailsId[4] = "#edit_Home_Phone";
    editPersDetailsId[5] = "#edit_Cell_Phone";
    editPersDetailsId[6] = "#edit_Customer_Notes";
    
    editAddressId[0] = "#edit_Country";
    editAddressId[1] = "#edit_Province";
    editAddressId[2] = "#edit_City";
    editAddressId[3] = "#edit_Street_Name";
    editAddressId[4] = "#edit_Street_Number";
    editAddressId[5] = "#edit_Apt_Number";
    editAddressId[6] = "#edit_Postal_Code";
    
    newValuesKeysPers[0] = 'first_name';
    newValuesKeysPers[1] = 'middle_name';
    newValuesKeysPers[2] = 'last_name';
    newValuesKeysPers[3] = 'email';
    newValuesKeysPers[4] = 'phone_home';
    newValuesKeysPers[5] = 'phone_cell';
    newValuesKeysPers[6] = 'notes';
    
    newValuesKeysAddy[0] = 'country';
    newValuesKeysAddy[1] = 'province';
    newValuesKeysAddy[2] = 'city';
    newValuesKeysAddy[3] = 'street_name';
    newValuesKeysAddy[4] = 'street_number';
    newValuesKeysAddy[5] = 'apt_number';
    newValuesKeysAddy[6] = 'postal_code';



function getSelectList(id, theClass)
{
    var theYear = "";
    var theMonth = "";
    var theDay = "";
    var theHour = "";
    var theMin = "";
    var theHalfDay = "";
    
    switch(id)
    {
        case "theDay":
            theDay = '<select class="'+ theClass + '" id="' + id + '">';
            theDay += '<option>Day</option>';
            var i;
            for (i=1; i<32; i++)
            {
                if ( i<= 9 ) { theDay += '<option>' + "0" + i + '</option>'; }
                else { theDay += '<option>' + i + '</option>'; }
            }
            theDay += '</select>';
            return theDay;
        break;
        
        case "theYear":
            theYear = '<select class="'+ theClass + '" id="' + id + '">';
            theYear += '<option>Year</option>';
            var i;
            for (i=13; i<24; i++)
            {
                theYear += '<option>20' + i + '</option>';
            }
            theYear += '</select>';
            return theYear;
        break;
        
        case "theMonth":
            theMonth = '<select class="'+ theClass + '" id="' + id + '">';
            
            theMonth += '<option>Month</option>';
            theMonth += '<option>January</option>';
            theMonth += '<option>February</option>';
            theMonth += '<option>March</option>';
            theMonth += '<option>April</option>';
            theMonth += '<option>May</option>';
            theMonth += '<option>June</option>';
            theMonth += '<option>July</option>';
            theMonth += '<option>August</option>';
            theMonth += '<option>September</option>';
            theMonth += '<option>October</option>';
            theMonth += '<option>November</option>';
            theMonth += '<option>December</option>';
            
            theMonth += '</select>';
            return theMonth;
        break;
        
        case "theHour":
            theHour = '<select class="'+ theClass + '" id="' + id + '">';
            
            theHour += '<option>Hour</option>';
            var i;
            for (i=1; i<13; i++)
            {
                if( i<=9 ) { theHour += '<option>' + "0" + i + '</option>'; }
                else { theHour += '<option>' + i + '</option>'; }
            }
            theHour += '</select>';
            return theHour;
        break;
        
        case "theMin":
            theMin = '<select class="'+ theClass + '" id="' + id + '">';
            
            theMin += '<option>Min</option>';
            theMin += '<option>00</option>';
            theMin += '<option>05</option>';
            var i;
            for (i=10; i<=60; i+=5)
            {
                theMin += '<option>' + i + '</option>';
            }
            theMin += '</select>';
            return theMin;
        break;
        
        case "theHalfDay":
            theHalfDay = '<select class="'+ theClass + '" id="' + id + '">';
            
            theHalfDay += '<option>HalfDay</option>';
            theHalfDay += '<option>AM</option>';
            theHalfDay += '<option>PM</option>';
            theHalfDay += '</select>';
            return theHalfDay;
        break;
    }  
      
}

/*
 * Will return a select option list with selected value pre-selected
 * Used in edit form, to populate birth date with values retrieved from db
 */
function getSelectListExt(id, theClass, selected)
{
    var theYear = "";
    var theMonth = "";
    var theDay = "";
    var months = ["January", "February", "March", "April", "May", "June","July","August","September","October","November","December"];
    var i;
//MAKE IT WORK WITH #212 APPOINTMENTS.JS    
    switch(id)
    {
        case "theDay":
            theDay = '<select class="form-control" id="' + id + '">';
            theDay += '<option>Day</option>';
            for (i=1; i<10; i++)
            {
                if ( ("0" + i) == selected ) { theDay += '<option selected>' + "0" + i + '</option>'; }
                else { theDay += '<option>0' + i + '</option>'; }
            }
            for (i=10; i<32; i++)
            {
                if ( i == selected ) { theDay += '<option selected>' + i + '</option>'; }
                else { theDay += '<option>' + i + '</option>'; }
            }
            theDay += '</select>';
            return theDay;
        break;
        
        case "theYear":
            theYear = '<select class="form-control" id="' + id + '">';
            theYear += '<option>Year</option>';
            for (i=1900; i<2020; i++)
            {
                if (i == selected) { theYear += '<option selected>' + i + '</option>'; }
                else { theYear += '<option>' + i + '</option>'; }
            }
            theYear += '</select>';
            return theYear;
        break;
        
        case "theMonth":
            theMonth = '<select class="form-control" id="' + id + '">';
            
            for(i = 0; i<months.length; i++)
            {
                if(months[i] == selected) { theMonth += '<option selected>' + months[i] + '</option>'; }
                else { theMonth += '<option>' + months[i] + '</option>'; }
            }
            theMonth += '</select>';
            return theMonth;
        break;
    }
}

function isItValid(argumentz)
{
    var argId = "the" + argumentz; //i.e. theYear, which is id for respective tag, so that we can change color back to original

    switch(argumentz)
    {
        case "Year": return false;
        break;
        
        case "Month": return false;
        break;
        
        case "Day": return false;
        break;
        
        case "Hour": return false;
        break;
        
        case "Min": return false;
        break;
        
        case "Choose": return false;
        break;
        
        default: document.getElementById(argId).style.color = "#000000";
                 return true;
    }
    
}

function formError(argumentz)
{
    document.getElementById(argumentz).style.color = "#D11919";
}

function convertValues(editValue)
{
    n = editValue.indexOf("_");
    
    editValue = editValue.replace("_", " ");
    
      return editValue.replace(/\w\S*/g, function(txt) {
       return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });    
}

function reloadz()
{
     location.reload();
}

/*
 * AJAX part of jQuery DisplayInfo function, it is used by editApptPay function too
 * 
 * It display all information found for a particular record i.e. personal details, appointments and payments
 * 
 * If action is delAppt1 it means it came from View Appointments tab and 
 * the page will be reloaded to reflect changes after an appointment has been created
 * 
 * If action is delAppt, it means it came from Display All Info screen and
 * inside the divs needs to be updated to reflect changes, no page reload
*/
function displayInfo(data, actionName)
{
    if( actionName == "delApptView" || actionName == "delPayView" ) { location.reload(); }
    
    jQuery.post(ajax_object.ajax_url, data, function(response) 
    { 
        var i;
        var appointmentDetailsTable;
        var paymentDetailsTable;
        var customerDetailsTable = "";
        var displayInfoArray = {};
        
        //alert(response);
        
            if( response == 0 ) { customerDetailsTable = '<h5><code>No records have been found.</code></h5>'; }
            else { displayInfoArray = JSON.parse(response); }
            
            //alert("id_cd that should be in td" + displayInfoArray[0].id_cd);
            
            var displayInfoBody = '<div id="searchResultsDiv"><h4>Display All Info</h4><p>Personal Details</p><div class="table-responsive"><table class="table table-hover table-bordered table-striped"><thead><tr><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Phone Home</th><th>Phone Cell</th><th>Address</th><th>Options</th></tr></thead><tbody id="displayInfoCustomer">';

            customerDetailsTable += '<tr><td id="f_name">' + displayInfoArray[0].First_Name
                                + '</td><td>' + displayInfoArray[0].Middle_Name 
                                + '</td><td id="l_name">' + displayInfoArray[0].Last_Name
                                + '</td><td>' + displayInfoArray[0].Home_Phone
                                + '</td><td>' + displayInfoArray[0].Cell_Phone
                                + '</td><td>' + displayInfoArray[0].Address
                                + '</td><td name ="' + displayInfoArray[0].id_cd + '"><a name="getEditValues" id="makeEdit">Edit</a> | <a name="delRecord" id="makeEdit">Delete</a> | <a name="makeAppt" id="apptPayForm">Make Appt</a> | <a name="enterPay" id="apptPayForm">Enter Payment</a> | <a name="displayInfo" id="displayInfo">Display All Info</a></td></tr>';
            
        //If Appointment Date exist it means there are appointment and the relevant table will be generated
        if ( typeof displayInfoArray[1][0] === 'undefined' || displayInfoArray[1][0] === null)
        {
            appointmentDetailsTable = "";
        }    
        else 
        {
            appointmentDetailsTable = '<p>Appointment Details</p><div class="table-responsive"><table class="table table-hover table-bordered table-striped"><thead><tr><th>Date</th><th>Time</th><th>Purpose</th><th>Venue</th><th>Notes</th><th>Options</th></tr></thead><tbody id="displayInfoCustomer">';
            for(i = 0; i < objLength(displayInfoArray[1]); i++) {
            appointmentDetailsTable += '<tr><td>' + displayInfoArray[1][i].Appointment_Date
                                + '</td><td>' + displayInfoArray[1][i].Appointment_Time
                                + '</td><td>' + displayInfoArray[1][i].Appointment_Purpose
                                + '</td><td>' + displayInfoArray[1][i].Appointment_Venue
                                + '</td><td>' + displayInfoArray[1][i].Appointment_Notes
                                + '</td><td name ="' + displayInfoArray[1][i].taid_cd + '" abbr="' + displayInfoArray[1][i].Appointment_Time + '" axis="' + displayInfoArray[1][i].Appointment_Date + '"><a name="delAppt" id="delAppt">Delete</a></td></tr>';
        }
        }
        
        //If Payment Date exist it means there are appointment and the relevant table will be generated
        if ( typeof displayInfoArray[2][0] === 'undefined' || displayInfoArray[2][0] === null)
        {
            paymentDetailsTable = "";
        }    
        else 
        {
            paymentDetailsTable = '<p>Payment Details</p><div class="table-responsive"><table class="table table-hover table-bordered table-striped"><thead><tr><th>Date</th><th>Amount</th><th>Purpose</th><th>Notes</th><th>Options</th></tr></thead><tbody id="displayInfoCustomer">';
            for(i = 0; i < objLength(displayInfoArray[2]); i++) {
            paymentDetailsTable += '<tr><td>' + displayInfoArray[2][i].Payment_Date
                                + '</td><td>' + displayInfoArray[2][i].Payment_Amount
                                + '</td><td>' + displayInfoArray[2][i].Payment_Purpose
                                + '</td><td>' + displayInfoArray[2][i].Payment_Notes
                                + '</td><td name ="' + displayInfoArray[2][i].tpid_cd + '" abbr="' 
                                + displayInfoArray[2][i].Payment_Date + '" axis="' 
                                + displayInfoArray[2][i].Payment_Amount + '"><a name="delPaySearch" id="delPay">Delete</a></td></tr>';
        }
        }
            
            //Generating Search Again view after ShowAllINfo function
            displayInfoBody += customerDetailsTable;
            displayInfoBody += '</tbody></table></div>';
            
            displayInfoBody += appointmentDetailsTable;
            displayInfoBody += '</tbody></table></div>';
            
            displayInfoBody += paymentDetailsTable;
            displayInfoBody += '</tbody></table></div></div><!--End searchResultDiv-->';
            
            displayInfoBody += '<button type="button" id="buttonSearchAgain" class="btn btn-default">Search Again</button>';
            jQuery('#makeSearch').html(displayInfoBody);
            document.getElementById("customerSearchForm").reset();
            
        });
}


function ajaxAddRecord(data, dataCheck)
{
    var confirmCreate;
        
        jQuery.post(ajax_object.ajax_url, dataCheck, function(response) 
        {
            //alert(data['mname']);
            //alert(response);
            
            if(response == 1) { confirmCreate = confirm("A record with the same name: " + dataCheck['fname'] + " " + dataCheck['lname'] + " already exists. Are you sure you want to create a new record?"); }
            if (response == 0 || confirmCreate == true)
            {
                jQuery.post(ajax_object.ajax_url, data, function(response) 
                {
                    //alert(response);
                    if(response)
                    {
                        document.getElementById("customerAddForm").reset();
                        document.getElementById("newCustomer").innerHTML = response;
                    }
                    else { alert('Add new record failed. Double check all fields. Try again or contact administrator.'); }

                });
            }
            
        });
}

/* #277
 * Sends an ajax request to ed-da-delta-appointments.php #263 to delete the record and associatd tables
 */
function ajaxDelRecord(data)
{
    jQuery.post(ajax_object.ajax_url, data, function(response){

    if (response == 'error') { alert("There was an error. Please try again"); }
    else if (response == 0 ) { alert("No records have been deleted"); }
    else if (response >= 1 ) 
    { 
        alert("The record has been deleted. The page will refresh now"); location.reload(); 
    }
    });
    
}

function returnDots(str, value)
{
    var dots = value - str.length ;
    var i;

    if(str.length < 6) { return "\t\t\t"; }
    else if (str.length > 5 && str.length < 14) { return "\t\t"; }
    else if (str.length >=14) {return "\t";}
}


function isObjEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }

    return true;
}

function objLength(obj)
{
    var length=0, prop;
            
    for (prop in obj)
    {
        if(obj.hasOwnProperty(prop)) length++;
    }

    return length;
}