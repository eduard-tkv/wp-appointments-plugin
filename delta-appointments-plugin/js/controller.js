/* 
 * Main Javascript controller. 
 * validation.js and helpers.js are part of it.
 * 
 * #buttonAddCustomer
 *    - Adds a new record
 * #buttonSearch
 *    - Searches records
 * #makeEdit
 *    - Edits an existing record
 * #buttonSave
 *    - Saves changes to an existing record
 * #buttonSearchAgain
 *    - Creates a search again form after original search was performed
 * #displayInfo
 *    - Displays all related info i.e. address, payments, appointments
 * #apptPayForm
 *    - Creates a new appointment or payment form
 * #buttonMakeAppt
 *    - Collects appointment details from form fields
 * #buttonEnterPay
 *    - Collects payment details from form fields
 * #delRecord
 *    - Sends info for record deletion
 * #delAppt
 *    - Sends info for appointment deletion
 * #delPay
 *    - Sends info for payment deletion
 */


jQuery(document).ready(function($) 
{   
    // Hiding search results div. This div is located above the search form.
    // Once the search is complete then this div is displayed along with containing search results
    // and the search form underneath is still available for additional searches
    $("#searchResultsDiv").hide();

/* #39
 * Add Customer function (adding personal details i.e. name etc). Collects values from the New Customer Form and 
 * sends them to ed-da-delta-appointments-plugin.php::ed_da_delta_appointments_addcustomer_action_callback #110 for creating a new record
 * If a positive response is received the page will reload to reflect changes.
 */
    jQuery( document ).on( "click", "#buttonAddCustomer", function()
    {
        var data = {
            'action': 'ed_da_delta_appointments_addcustomer_action',
            'fname': $("#first_name").val(),
            'mname' : $("#middle_name").val(),
            'lname' : $("#last_name").val(),
            'email' : $("#email").val(),
            'phhome' : $("#phone_home").val(),
            'phcell' : $("#phone_cell").val(),
            'birthdate' : $("#birth_date").val(),
            'cnotes' : $("#customer_notes").val(),
            'province' : $("#province").val(),
            'city' : $("#city").val(),
            'streetnumber' : $("#street_number").val(),
            'aptnumber' : $("#apt_number").val(),
            'streetname' : $("#street_name").val(),
            'postalcode' : $("#postal_code").val()
         };
         
         // Checking if the date was selected i.e. if it's Day it means it wasn't selected and an empty string set instead
         var theDay = $("#theDay option:selected").text() == "Day" ? "" : $("#theDay option:selected").text();
         var theMonth = $("#theMonth option:selected").text() == "Month" ? "" : $("#theMonth option:selected").text();
         var theYear = $("#theYear option:selected").text() == "Year" ? "" : $("#theYear option:selected").text();
         data['country'] = $("#country option:selected").text() == "Country" ? "" : $("#country option:selected").text();
         
         // Concatenating birth date, if nothing selected it would be an empty string - almost an empty string
         data['birthdate'] = theDay + " " + theMonth + " " + theYear;
         
        // Creating a count object, it will be passed by reference to a validation function
        // validation function will set countErr['coun'] to 1 if validation fails.
        var countErr = {};
        countErr['count'] = 0;
        
        // Refer to validation.js for validation functions
        // arguments: 1 - var that needs to be validated, 2- id name in the HTML tag (for error msg display)
        // 3- Error message, 4- If mandatory (1 means mandatory or 0), 5- countErr object to count errors,
        // 6- type of validation (a - alphanumeric, n - numeric, an - alphanumeric, dn - dashes and numeric only, an - alpha and numeric only)
        validateMe(data['fname'], "#first_name", "First Name cannot be blank or contain non letters.", 1, countErr, "a");
        validateMe(data['lname'], "#last_name", "Last Name cannot be blank or contain non letters.", 1, countErr, "a");
        validateMe(data['mname'], "#middle_name", "Middle Name cannot contain non letters.", 0, countErr, "a");
        validateEmail(data['email'], "#email", "Invalid Email.", 0, countErr);
        validateMe(data['phhome'], "#phone_home", "Phone Home cannot be blank and must be numerical.", 1, countErr, "n");
        validateMe(data['phcell'], "#phone_cell", "Cell Phone must be numerical.", 0, countErr, "n");
        validateMe(data['cnotes'], "#customer_notes", "Notes can contain numbers and letters only.", 0, countErr, "an");
        validateMe(data['country'], "#country", "Country can contain letters only.", 0, countErr, "a");
        validateMe(data['province'], "#province", "Province cannot be blank or contain non letters.", 1, countErr, "a");
        validateMe(data['city'], "#city", "City cannot contain non letters.", 0, countErr, "a");
        validateMe(data['streetnumber'], "#street_number", "Street Number can contain numbers and dashes only.", 0, countErr, "dn");
        validateMe(data['aptnumber'], "#apt_number", "Apt Number can contain numbers and dashes only.", 0, countErr, "dn");
        validateMe(data['streetname'], "#street_name", "Street Name can contain letters and dashes only.", 0, countErr, "dan");
        validateMe(data['postalcode'], "#postal_code", "Postal Code can contain numbers and letters only.", 0, countErr, "an");
        
        // dataCheck contains first and last name, used to check if there is already a record with
        // such name, if it exists the user is prompted to confirm new record creation anyway
        // If it doesn't exist creates a new record without a prompt.
        var dataCheck = {
           'action':  'ed_da_delta_appointments_checkifexists_action', 
           'fname': $("#first_name").val(),
           'lname' : $("#last_name").val()
         };
  
         // If no errors found then execute an Ajax call, refer to helpers.js::
         if(!countErr['count']) { ajaxAddRecord(data, dataCheck); }
    }); 
// END Add Customer

/* #111
 * Search function. Invoked by buttonSearch button. 
 */
    jQuery( document ).on( "click", "#buttonSearch", function()
    {
        var data = {
            'action': 'ed_da_delta_appointments_search_action',
            'fname': jQuery("#search_first_name").val(),
            'mname' : jQuery("#search_middle_name").val(),
            'lname' : jQuery("#search_last_name").val(),
            'email' : jQuery("#search_email").val(),
            'phhome' : jQuery("#search_phone_home").val(),
            'phcell' : jQuery("#search_phone_cell").val(),
            'city' : jQuery("#search_city").val(),
            'province' : jQuery("#search_province").val(),
            'streetnumber' : jQuery("#search_street_number").val(),
            'aptnumber' : jQuery("#search_apt_number").val(),
            'streetname' : jQuery("#search_street_name").val(),
            'postalcode' : jQuery("#search_postal_code").val()
        };
        
         var theDay = $("#search_Day option:selected").text() == "Day" ? "" : $("#search_Day option:selected").text();
         var theMonth = $("#search_Month option:selected").text() == "Month" ? "" : $("#search_Month option:selected").text();
         var theYear = $("#search_Year option:selected").text() == "Year" ? "" : $("#search_Year option:selected").text();
         data['country'] = $("#search_country option:selected").text() == "Country" ? "" : $("#search_country option:selected").text();
         
         // Concatenating birth date, if nothing selected it would be an almost empty string
         data['birthdate'] = theDay + " " + theMonth + " " + theYear;
         
        //alert(data['birthdate']); alert(data['country']);
       
        // Sending to ed_da_delta_appointments_search_action in ed_da_delta_appointments.php
        jQuery.post(ajax_object.ajax_url, data, function(response) 
        {
            //alert('this is search response = '+response);
            var searchResultsBody = "";
            var searchArray = {};
            if( response == 0 ) { searchResultsBody = '<h5><code>No records have been found.</code></h5>'; }
            else { searchArray = JSON.parse(response); }
            
            //Displaying previously hidden #searchResultsDiv so we can populate it with search results
            jQuery("#searchResultsDiv").show();
            
            var i;
            for(i = 0; i < searchArray.length; i++) {
            searchResultsBody += '<tr><td id="f_name' + searchArray[i].id_cd + '">' + searchArray[i].first_name
                                + '</td><td>' + searchArray[i].middle_name 
                                + '</td><td id="l_name' + searchArray[i].id_cd + '">' + searchArray[i].last_name
                                + '</td><td>' + searchArray[i].email
                                + '</td><td>' + searchArray[i].phone_home
                                + '</td><td>' + searchArray[i].phone_cell
                                + '</td><td name ="' + searchArray[i].id_cd + '"><a name="getEditValues" id="makeEdit">Edit</a> | <a name="delRecord" id="delRecord">Delete</a> | <a name="makeAppt" id="apptPayForm">Make Appt</a> | <a name="enterPay" id="apptPayForm">Enter Payment</a> | <a name="displayInfo" id="displayInfo">Display All Info</a></td></tr>';
        }
            // Populating #searchResultsBody with search results
            jQuery('#searchResultsBody').html(searchResultsBody);
            document.getElementById("customerSearchForm").reset();
            
        });
    });//jQuery Search End
    
    
/* #172 - makeEdit
 * Retrieves values for editing a record, edit form will be populated with these values
 * Edit link/button appears in search results
 */
    jQuery( document ).on( "click", "#makeEdit", function()
    {
        //parentName is the record's ID from Database - already set when populating search results
        //thisName is the action that needs to be taken i.e. edit, delete etc
        var parentName = $( this ).parent().attr( 'name' );
        var thisName = $( this ).attr( 'name' );// value = returnEdit because it will only retrieve values from db for editing
        var data = {};
      
            data = {
            'action': 'ed_da_delta_appointments_fulledit_action',
            'id_cd': parentName,
            'action_name' : thisName
                };
        
        //alert(data['id_cd']);
        // editKeys and editValues are global vars
        jQuery.post(ajax_object.ajax_url, data, function(response){
                editKeys = [];
                editValues = [];
                var editObj = JSON.parse(response);
                var day, year, month;
                
                // Populate editValues and editKeys with keys and values from db response i.e. City:Toronto etc
                // editValues is Toronto, editKeys is City. Separating them for easy population later on
                for(key in editObj[0]) 
                { 
                    // Storing the date keys separately i.e. 1990-10-20 becomes 
                    // day = 20, month = October, year = 1990
                    // As it wouldn't be a text field, but 3 separate select option lists
                    if(key == "Birth_Date")
                    {
                        day = editObj[0]["Birth_Date"].substr(8,2);
                        year = editObj[0]["Birth_Date"].substr(0, 4)
                        month = editObj[0]["Birth_Date"].substr(5, 2)
                        editValues.push(day); editKeys.push("theDay");
                        editValues.push(month); editKeys.push("theMonth");
                        editValues.push(year); editKeys.push("theYear");
                        delete editObj[0]["Birth_Date"]; 
                    }   
                    else { editValues.push(editObj[0][key]); editKeys.push([key]); }
                }

                var editResultsBody = '<h4>Update Record Details</h4><form id="customerEditForm" class="form-horizontal" role="form">';
                var i;
                for(i = 1; i < editKeys.length; i++) 
                {
                    // If i==11 which is id then don't display it
                    // If it's day or month, skip it, select list will be populated by 'year' trigger below
                    if( i==11 || editKeys[i] == 'theDay' || editKeys[i] == 'theMonth') { editResultsBody += ""; }
                    else 
                    {
                        editValues[i] = editValues[i] == 0 || editValues[i] == null ? "" : editValues[i];
                        
                        // 
                        if(editKeys[i] == 'theYear')
                        {
                            if(editValues[i] != "")
                            {
                                editResultsBody += 
                                     '<div class="form-group form-inline"><label class="control-label col-sm-1" for="birth_date">Birth Date</label><div class="col-sm-5">' + 
                                       getSelectListExt('theDay', 'form-control', day) + 
                                       getSelectListExt('theMonth', 'form-control', month) + 
                                       getSelectListExt('theYear', 'form-control', year) + '</div></div>';

                            }
                            else
                            {
                                editResultsBody += 
                                       '<div class="form-group form-inline"><label class="control-label col-sm-1" for="birth_date">Birth Date</label><div class="col-sm-5">' +
                                       getSelectList('theDay', 'form-control') + 
                                       getSelectList('theMonth', 'form-control') + 
                                       getSelectListExt('theYear', 'form-control', 0) + '</div></div>';
                            }
                        }
                        else 
                        {    
                            editResultsBody += '<div class="form-group"><label class="control-label col-sm-1" for="edit_'+ 
                                        editKeys[i] +'">' + convertValues(editKeys[i].toString()) +
                                        ': </label><div class="col-sm-4" col-sm-6><input type="text" class="form-control" id="edit_'+ 
                                        editKeys[i] +'" placeholder="'+ editValues[i] + '"></div></div>';
                        }
                    }
                } 
                editResultsBody += '<div class="form-group"><div class="col-sm-offset-1 col-sm-11"><button type="button" id="buttonSave" class="btn btn-default">Save</button><button type="button" id="buttonCancel" onclick="reloadz()" class="btn btn-default">Cancel</button></div></div></form>';

                document.getElementById("makeSearch").innerHTML = editResultsBody;
                //console.log(editKeys);
                //console.log(editValues);
     
        });
        
    });
    

/* #270
 * Collects data from edit form, invoked by clicking "Save" button after existing values have been retrieved
 * by clicking "Edit" link that appears in search results
 * New values are collected from the edit form and sent to ed-da-delta-appointments-plugin.php
 */
    jQuery( document ).on( "click", "#buttonSave", function()
    {
        var inputValuesArr = [];
        var i;
        var idName;
        var day, year, month;
        var newValues = new Object();
        
        // inputValuesArr is an array, we are just populating values from the edit from
        // e.g. inputValuesArr[0] is 85 (id_cd) or inputValuesArr[3] is Sveta (first_name)
        for(i = 0; i < editValues.length; i++)
        {   
            // These two are "i" address table id and customer details table id_cd
            // editValues is a global array, values have been populated in previous step
            // when retrieving edit values
            if(i == 0 || i == 11) { inputValuesArr.push(editValues[i]); }
            else if(i == 7 || i == 8 || i == 9)
            {
                if(i==7) { day = $("#theDay option:selected" ).text(); }
                if(i==8) { month = $("#theMonth option:selected" ).text(); }
                if(i==9) { year = $("#theYear option:selected" ).text(); }
            }
        }
        
        var confirmSubmit = {};
        var confirmValue;
        
        // Retrieving values from the edit/update form. If the value is empty, retrieve the placeholder value.
        // The placeholder value is the value retrieved from the db
        // editPersDetailsId, newValuesKeysPers, editAddress are declared in helpers.js
        for(i=0; i<editPersDetailsId.length; i++)
        {
            newValues[newValuesKeysPers[i]] = $(editPersDetailsId[i]).val() == "" ? $(editPersDetailsId[i]).attr("placeholder") : $(editPersDetailsId[i]).val();
            confirmValue = editPersDetailsId[i].replace("#edit_","");
            confirmSubmit[confirmValue.replace("_"," ")] = newValues[newValuesKeysPers[i]];
        }
        
        for(i=0; i<editAddressId.length; i++)
        {
            newValues[newValuesKeysAddy[i]] = $(editAddressId[i]).val() == "" ? $(editAddressId[i]).attr("placeholder") : $(editAddressId[i]).val();
            confirmValue = editAddressId[i].replace("#edit_","");
            confirmSubmit[confirmValue.replace("_"," ")] = newValues[newValuesKeysAddy[i]];
        }
       
        // Declaring an object countErr and setting count property to 0. This object is passed to the validation function
        // by reference and it set to 1 if errors encountered.
        var countErr = {};
        countErr['count'] = 0;
        
        newValues.birth_date = day + " " + month + " " + year;
        confirmSubmit["Birth Date"] = newValues.birth_date;
        
        newValues.id_cd = editValues[0]; // id_cd is the primary id for customer details table
        newValues.id = editValues[11]; // id is the id for address table where id_cd is the foreign key
        newValues.action = 'ed_da_delta_appointments_fulledit_action';
        newValues.action_name = 'fullEdit';

        // Validating values, basic validation, mostly to make sure no illegal characters are entered
        validateMe(newValues['first_name'], "#edit_First_Name", "First Name cannot be blank or contain non letters.", 1, countErr, "a");
        validateMe(newValues['middle_name'], "#edit_Middle_Name", "Middle Name cannot contain non letters.", 0, countErr, "a");
        validateMe(newValues['last_name'], "#edit_Last_Name", "Last Name cannot be blank or contain non letters.", 1, countErr, "a");
        validateEmail(newValues['email'], "#edit_Email", "Invalid Email.", 0, countErr);
        validateMe(newValues['phone_home'], "#edit_Home_Phone", "Phone Home cannot be blank and must be numerical.", 1, countErr, "n");
        validateMe(newValues['phone_cell'], "#edit_Cell_Phone", "Cell Phone must be numerical.", 0, countErr, "n");
        validateMe(newValues['notes'], "#edit_Customer_Notes", "Notes can contain numbers and letters only.", 0, countErr, "an");
        
        validateMe(newValues['country'], "#edit_Country", "Country can contain letters only.", 0, countErr, "a");
        validateMe(newValues['province'], "#edit_Province", "Province cannot be blank or contain non letters.", 1, countErr, "a");
        validateMe(newValues['city'], "#edit_City", "City cannot contain non letters.", 0, countErr, "a");
        validateMe(newValues['street_number'], "#edit_Street_Number", "Street Number can contain numbers and dashes only.", 0, countErr, "dn");
        validateMe(newValues['apt_number'], "#edit_Apt_Number", "Apt Number can contain numbers and dashes only.", 0, countErr, "dn");
        validateMe(newValues['street_name'], "#edit_Street_Name", "Street Name can contain letters and dashes only.", 0, countErr, "dan");
        validateMe(newValues['postal_code'], "#edit_Postal_Code", "Postal Code can contain numbers and letters only.", 0, countErr, "an");
        
        var key;
        
        // If countErr[count] stays at 0 it means there are no errors.
        if(!countErr['count'])
        {
            // Confirm pop up requires prettification.
            confirmValue = "Please confirm submission:\n\n";
            for(key in confirmSubmit)
            {
                    confirmValue += key + ":\n.................................." + confirmSubmit[key] + "\n";
            }
            
            if(confirm(confirmValue))
            {
                jQuery.post(ajax_object.ajax_url, newValues, function(response)
                {   
                    document.getElementById("makeSearch").innerHTML = '<h5><mark>' + response + '. Click Search for a new search, or Refresh to see changes.</mark></h5>' +
                        '<button type="button" id="buttonSearchAgain" class="btn btn-default">Search Again</button>' +
                        '<button type="button" id="buttonRefresh" onclick="reloadz()" class="btn btn-default">Refresh</button>';

                });
            }
            
        }

    });//#buttonSave End
    
/* #376
 * Creates a Search Again form, a search form that appears underneath the first time search results
 * so the user can search repeatedly.
 */
    jQuery( document ).on( "click", "#buttonSearchAgain", function()
    {
        var searchAgainBody = '<div id="searchResultsDiv"><h4>Search Results</h4><p>The following records have been retrieved based on your request</p><div class="table-responsive"><table class="table table-hover table-bordered table-striped"><thead><tr><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Phone Home</th><th>Phone Cell</th><th>Address</th><th>Options</th></tr></thead><tbody id="searchResultsBody"></tbody></table></div></div><!--End searchResultDiv--><h4>Search by one or more criteria</h4><h6>Edit, delete, make appointments, enter payments and view all infomation from within search results</h6><form id="customerSearchForm" class="form-horizontal" role="form">';

        var searchAgainBodyArr = [];
        searchAgainBodyArr.push('first_name');
        searchAgainBodyArr.push('middle_name');
        searchAgainBodyArr.push('last_name');
        searchAgainBodyArr.push('email');
        searchAgainBodyArr.push('phone_home');
        searchAgainBodyArr.push('phone_cell');
        searchAgainBodyArr.push('country');
        searchAgainBodyArr.push('city');
        searchAgainBodyArr.push('street_number');
        searchAgainBodyArr.push('apt_number');
        searchAgainBodyArr.push('street_name');
        searchAgainBodyArr.push('postal_code');
        
        var i = 0;

        for(i = 0; i <searchAgainBodyArr.length; i++)
        {
            searchAgainBody += '<div class="form-group"><label class="control-label col-sm-1" for="search_';
            searchAgainBody += searchAgainBodyArr[i];
            searchAgainBody += '">';
            searchAgainBody += convertValues(searchAgainBodyArr[i]);
            searchAgainBody += ':</label><div class="col-sm-4" col-sm-6><input type="text" class="form-control" id="search_';
            searchAgainBody += searchAgainBodyArr[i];
            searchAgainBody += '" placeholder="Enter ';
            searchAgainBody += convertValues(searchAgainBodyArr[i]);
            searchAgainBody += '"></div></div>';
        }
        
        searchAgainBody += '<div class="form-group"><div class="col-sm-offset-1 col-sm-11"><button type="button" id="buttonSearch" class="btn btn-default">Search</button></div></div></form></div><!--End of "makeSearch" div--><!--Two final divs--></div>';
        
        
        jQuery("#makeSearch").html(searchAgainBody);
        jQuery("#searchResultsDiv").hide();
    });
    
    
/* #421
 * Display all info pertaining to a certain record, display Delete (deletes the record), Make Appointment, Enter Payment links
 * the 'display all info' link/button appears in search results
*/
    jQuery( document ).on( "click", "#displayInfo", function()
    {  
        var parentName = $( this ).parent().attr( 'name' );//id_cd - primary id, has been already set in search results, not displayed
        
        //Value whether it is to display info or perform a different action
        var thisName = $( this ).attr( 'name' );
        var data = {};
        
        if( thisName == 'displayInfo' )
        {
                data = {
                'action': 'ed_da_delta_appointments_displayinfo_action',
                'id_cd': parentName,
                'action_name' : thisName
                    };
        }
        
        //console.log(data);

        //The function is in helpers.js, used to display all info including appointments and payments
        displayInfo(data, thisName);
        
    });
    
/* #449
 * Creates a new appointment or new payment form and populates.
 * 'Make Appointment' or 'Enter Payment' links/buttons appear in search results
 */
    jQuery( document ).on( "click", "#apptPayForm", function()
    {
        // Make Appt or Enter Payment - depending on which link/button the user clicked
        var apptOrPay = $(this).attr('name');
        var i;
        // id_cd is the primary id for customer table, already set when performing search
        var id_cd = $( this ).parent().attr('name');
        
        // Setting distinct first and last name HTML id tags (by adding id_cd) so we can display
        // the correct name if there are more than one search result rows
        var f_nameId = '#f_name' + id_cd;
        var l_nameId = '#l_name' + id_cd;
        var f_name = $(f_nameId).text();
        var l_name = $(l_nameId).text();
        var apptAndPayForm = "";
        var apptAndPayFormArr = [];
        var labelDate = "";
        
        if(apptOrPay == "makeAppt")
        {
            apptAndPayForm = '<h4>Make an Appointment</h4><h6>Enter appointment details</h6><form id="customerSearchForm" class="form-horizontal" role="form">';
            apptAndPayFormArr.push('purpose');
            apptAndPayFormArr.push('venue');
            apptAndPayFormArr.push('notes');
            labelDate = "Appointment Date:";
        }
        else if (apptOrPay == "enterPay")
        {
            apptAndPayForm = '<h4>Enter a Payment</h4><h6>Enter payment details</h6><form id="customerSearchForm" class="form-horizontal" role="form">';
            apptAndPayFormArr.push('purpose');
            apptAndPayFormArr.push('amount');
            apptAndPayFormArr.push('notes');
            labelDate = "Payment Date:";
        }

        apptAndPayForm += '<div class="form-group"><label class="control-label col-sm-1" for="first_name">First Name:</label><div class="col-sm-4" col-sm-6><label class="control-label text-muted" id="makeappt_first_name">' + f_name +'</label></div></div>';
        
        apptAndPayForm += '<div class="form-group"><label class="control-label col-sm-1" for="last_name">Last Name:</label><div class="col-sm-4" col-sm-6><label class="control-label text-muted" id="makeappt_first_name">' + l_name +'</label></div></div>';

        for(i = 0; i <apptAndPayFormArr.length; i++)
        {
            apptAndPayForm += '<div class="form-group"><label class="control-label col-sm-1" for="makeappt_';
            apptAndPayForm += apptAndPayFormArr[i];
            apptAndPayForm += '">';
            apptAndPayForm += convertValues(apptAndPayFormArr[i]);
            apptAndPayForm += ':</label><div class="col-sm-5"><input type="text" class="form-control" id="makeappt_';
            apptAndPayForm += apptAndPayFormArr[i];
            apptAndPayForm += '" placeholder="Enter ';
            apptAndPayForm += convertValues(apptAndPayFormArr[i]);
            apptAndPayForm += '"></div></div>';
        }

        //Refer to helper.js for getSelectList funcitons
        
        apptAndPayForm += '<label class="control-label col-sm-1" for="appt_date">' + labelDate +  '</label><div class="col-sm-5"><div class="form-group form-inline">' + getSelectList('theDay', 'form-control') + getSelectList('theMonth', 'form-control') + getSelectList('theYear', 'form-control');
        
        if(apptOrPay == "makeAppt")
        {
            apptAndPayForm += ' <strong>Time:</strong> ' + getSelectList('theHour', 'form-control') + getSelectList('theMin', 'form-control') + getSelectList('theHalfDay', 'form-control') + '</div></div><div class="form-group"><div class="col-sm-offset-1 col-sm-11"><button type="button" id="buttonMakeAppt" name="' + id_cd + '" class="btn btn-default">Make Appointment</button></div></div></form></div><!--End of "makeSearch" div--><div class="col-sm-offset-1"><span id="errMsg" class="text-danger"></span></div>';
        }
        else if (apptOrPay == "enterPay")
        {
            apptAndPayForm += '</div></div><div class="form-group"><div class="col-sm-offset-1 col-sm-11"><button type="button" id="buttonEnterPay" name="' + id_cd + '" class="btn btn-default">Enter Payment</button></div></div></form></div><!--End of "makeSearch" div--><div class="col-sm-offset-1"><span id="errMsg" class="text-danger"></span></div>';
        }
        
        // Displaying the form with populated values
        jQuery("#makeSearch").html(apptAndPayForm);
        //alert(apptAndPayForm);
        
        var data = {
            'action': 'ed_da_delta_appointments_enterApptPay_action',
            'id_cd': id_cd,
            'action_name': 'retrieveAllApptTimes'
        };
        
        // Retrieving all appoinments times for a certain record to check if there are conflicting
        // times when person tries to create a new appointment in #542 (#buttonMakeAppt)
        jQuery.post(ajax_object.ajax_url, data, function(response) 
        { 
            allApptTimes = JSON.parse(response);
            //console.log(allApptTimes);
        });
        
    });

/* #538
 * "Make Appointment" button on the Make Appointment form created and populated with first and last names.
 * Collects appointment info vield values for further insertion into the db
 */
    jQuery( document ).on( "click", "#buttonMakeAppt", function()
    {
        var i = 0;
        var theFlag = 1;
        var goFlag = 0;
        var length = 0;
        var apptData = {};
        
        apptData.theYear = $( "#theYear option:selected" ).text();
        apptData.theMonth = $( "#theMonth option:selected" ).text();
        apptData.theDay = $( "#theDay option:selected" ).text();
        apptData.theHour = $( "#theHour option:selected" ).text();
        apptData.theMin = $( "#theMin option:selected" ).text();
        apptData.theHalfDay = $( "#theHalfDay option:selected" ).text();

        //Checking to see if year, month and time etc were selected, otherwise it will be highlighted and flag set to 0
        $.each(apptData, function( k, v ) {
            //i.e. if Year == Year, which means nothing was selected
            if( k.substr(3) != v ) 
            { 
                apptData[k] = v; document.getElementById(k).style.color = "#000000"; $("#errMsg").text(""); 
            }
            else 
            { 
                document.getElementById(k).style.color = "#D11919";  $("#errMsg").text("Please correct highlited errors"); 
                theFlag = 0;
            }
                });
        
        apptData.action = "ed_da_delta_appointments_apptAndPay_action";
        apptData.venue = $( "#makeappt_venue" ).val();
        apptData.purpose = $( "#makeappt_purpose" ).val();
        apptData.appt_notes = $( "#makeappt_notes" ).val();
        apptData.action_name = "makeAppt";
        apptData.id_cd = $(this).attr('name');
        apptData.date = $( "#theDay option:selected" ).text() + " " + $( "#theMonth option:selected" ).text() + " " + $( "#theYear option:selected" ).text();
        apptData.time = $( "#theHour option:selected" ).text() + ":" + $( "#theMin option:selected" ).text() + " " + $( "#theHalfDay option:selected" ).text();
        
        //Checking if allApptTimes is empty, if its empty then there are no previous appointments for that time, so we can skip the check
        //otherwise we check user input with previous appointments to avoid duplicate appts on the same day
        if(!isObjEmpty(allApptTimes))
        {
            for (i in allApptTimes) {
                if (allApptTimes.hasOwnProperty(i)) {
                    length++;
                }
             }

             //allApptTimes contains dates and times for this record, it is global and was generated in the previous step
             //when displaying make appointment form. allApptTimes = [{time: "04:40 PM", date: "02 January 2017"}, {time: "03:40 PM", date: "10 January 2013"}];
             //checking here to see that there is no duplicate appt for the same time on that day
            $.each(allApptTimes, function( k, v ) {
            //k is time and date, v is 03 January 2015 and 05:40 PM
                for(i=0; i< length; i++)
                {
                    if( apptData.time == allApptTimes[i].time && apptData.date == allApptTimes[i].date ) { $("#errMsg").text("Appointment for this time exists, please choose different time."); theFlag = 0; }
                }
            });
        }
        
        //It may show allApptTimes as not declared, but it is global, all global vars are in helpers.js
        //alert('this is allappttime[0].time and date = ' + allApptTimes[0].time + " " + allApptTimes[0].date)
        
        if (theFlag == 1)
        {
            jQuery.post(ajax_object.ajax_url, apptData, function(response) 
            {
                if ( response == 1 ) 
                { 
                    alert("A new appointment has been created. The page will refresh now to reflect changes. Check Appointments tab"); 
                    location.reload();
                }
                else { alert("There was an error"); }

            });
        } else { alert("Please select a different time or delete the existing appointment."); }
    });



/* #622
 * Button Enter Payment on the create appointment form after you click Display All Info
 * Performs actual payment info creation
 * Unlike Make Appointment doesn't check if there are other payments on the same day as there
 * could be multiple payments for various purposes
 */
    jQuery( document ).on( "click", "#buttonEnterPay", function()
    {
        //console.log(allApptTimes);
        //console.log(allApptTimes["time"]);
        //alert('inside make appt');
        var i = 0;
        var theFlag = 1;
        var goFlag = 0;
        var length = 0;
        var payData = {};
        
        payData.theYear = $( "#theYear option:selected" ).text();
        payData.theMonth = $( "#theMonth option:selected" ).text();
        payData.theDay = $( "#theDay option:selected" ).text();

        //alert("theYear".substr(3));

        //Checking to see if year, month and time etc were selected, otherwise it will be highlighted and flag set to 0
        $.each(payData, function( k, v ) {
            //i.e. if Year == Year, which means nothing was selected
            if( k.substr(3) != v ) 
            { 
                payData[k] = v; document.getElementById(k).style.color = "#000000"; $("#errMsg").text(""); 
                //theFlag = 1;
                //alert(apptData.theYear + v);
            }
            else 
            { 
                document.getElementById(k).style.color = "#D11919";  $("#errMsg").text("Please correct highlited errors"); 
                theFlag = 0;
            }
                });

        payData.action = "ed_da_delta_appointments_apptAndPay_action";
        payData.amount = $( "#makeappt_amount" ).val();
        payData.purpose = $( "#makeappt_purpose" ).val();
        payData.notes = $( "#makeappt_notes" ).val();
        payData.action_name = "enterPay";
        payData.id_cd = $(this).attr('name');
        payData.date = $( "#theDay option:selected" ).text() + " " + $( "#theMonth option:selected" ).text() + " " + $( "#theYear option:selected" ).text();

        if (theFlag == 1)
        {
            jQuery.post(ajax_object.ajax_url, payData, function(response) 
            {
                //alert(response);
                if ( response == 1 ) 
                { 
                    alert("A new payment record has been created. The page will refresh now to reflect changes. Check Payments tab"); 
                    location.reload();
                }
                else { alert("There was an error"); }

            });
        } else { alert("Please correct errors."); }
    });

/* #685 - delRecord
 * Deletes a record and all associated data i.e. appointments, payments
 */    
    jQuery(document).on("click", "#delRecord", function(){
        
        // id_cd i.e. id for customer details table, which is a foreign key for all other tbls
        var parentName = $( this ).parent().attr( 'name' );
        var thisName = $( this ).attr( 'name' );// value = returnEdit because it will only retrieve values from db for editing
        var data;
        
        if(confirm('You are about to delete this record. Are you sure?'))
            {
                data = {
                'action': 'ed_da_delta_appointments_fulledit_action',
                'id_cd': parentName,
                'action_name' : thisName
                    };
                
                // helpers.js #
                ajaxDelRecord(data);
            }
            else 
            {
                alert("Action was cancelled");
            }
    
    });

/* #713
 * Delete an Appointment, no editing, if they want to edit, they need to delete and make an appt
*/
    jQuery( document ).on( "click", "#delAppt", function()
    {
        //action can be delAppt1 or delAppt, if delAppt1 it means it came from views.php and the user is looking at
        //View Appointments tab, so the page needs to be refreshed to reflect changes
        //if action is delAppt it means it came from search results with display all info and just the data inside div needs
        //to be updated without refreshing the page
        var actionName = $(this).attr("name");
        var id_cd = $(this).parent().attr("name");
        var time = $(this).parent().attr("abbr");
        var date = $(this).parent().attr("axis");

        //alert('actionname: '+ action + ' and idcd: ' +id_cd + ' date is: ' + date + ' time is: ' + time);
        if(confirm("The appointment will be deleted."))
        {
        var data = {
            'action' : 'ed_da_delta_appointments_enterApptPay_action',
            'action_name' : actionName,
            'id_cd' : id_cd,
            'time': time,
            'date': date
            };
            
        jQuery.post(ajax_object.ajax_url, data, function(response) 
        {
            if(response == false) { alert('No appointments deleted or there was an error.'); }
            else 
            { 
                alert('Appointment has been deleted, the page will refresh now to update changes'); 
                
                data = {
                'action': 'ed_da_delta_appointments_displayinfo_action',
                'id_cd': id_cd,
                'action_name' : 'displayInfo'
                    };
                    
                    
                        displayInfo(data, actionName);
                    
            }
           
        });
        }else { alert("Action has been cancelled"); }
        
    });


/* #762
 * Delete a payment record, invoked by clicking on Delete link after performing a search and
 * displaying all info (Display All Info link) or by clicking Delete link from within the Payments tab.
 */
    jQuery( document ).on( "click", "#delPay", function()
    {
        //action can be delPayView which means it's a link in View Payments tab and the page needs to be reloaded
        //View Appointments tab, so the page needs to be refreshed to reflect changes
        //
        //if action is delPaySearch it means it came from search results with display all info and just the data inside div needs
        //to be updated without refreshing the page i.e. by calling displayInfo() in helpers.
        var actionName = $(this).attr("name");
        var id_cd = $(this).parent().attr("name");
        var payAmount = $(this).parent().attr("axis");
        var payDate = $(this).parent().attr("abbr");

        //alert('action: '+ actionName + ' and idcd: ' +id_cd + ' date is: ' + payDate + ' amount is: ' + payAmount)

        if(confirm("The payment record will be deleted."))
        {
            var data = {
                'action' : 'ed_da_delta_appointments_delPay_action',
                'action_name' : actionName,
                'id_cd' : id_cd,
                'payDate': payDate,
                'payAmount': payAmount
                };
                
            jQuery.post(ajax_object.ajax_url, data, function(response) 
            {
                if(response == false) { alert('No payment records were deleted or there was an error.'); }
                else 
                { 
                    alert('The payment record has been deleted, the page will refresh now to update changes'); 

                    data = {
                    'action': 'ed_da_delta_appointments_displayinfo_action',
                    'id_cd': id_cd,
                    'action_name' : 'displayInfo'
                        };


                    displayInfo(data, actionName);
                    
            }
           
        });
        }
        else { "Action has been cancelled." }
        
    }); // End delPay

});//jQuery Main End