<?php
/*
Plugin Name: Delta Appointments
Plugin URI: http://www.deltadigital.ca/delta-appointments
Description: Manage your appointments or use it as an address book
Version: 1.0
Author: Eduard T
Author URI: http://www.deltadigital.ca
License: GPL
*/
defined('ABSPATH') or die ('Cannot access pages directly.');

include_once 'views/views.php';
include_once 'views/helpers-views.php';
include_once 'models/models.php';

register_activation_hook( __FILE__, 'ed_da_delta_appointments_plugin_install' );
register_deactivation_hook( __FILE__, 'ed_da_delta_appointments_plugin_deactivate' );

// Adding a menu item on admin page
add_action( 'admin_menu', 'ed_da_delta_appointments_plugin_menu' );
// This is for email notifications for upcoming appointments, not working yet Oct 05, 2015
add_action('emailNotifications', 'appointmentsNotify');

function ed_da_delta_appointments_plugin_menu()
{
    add_menu_page( 'Delta Appointments', 'Delta Appointments', 'manage_options', 'ed-da-delta-appointments-plugin', 'ed_da_delta_appointments_plugin_html', plugins_url( 'delta-appointments/images/delta-appts-icon.png', 6.4 ));
}

// Upon installation it will create database tables and populate with some sample data
function ed_da_delta_appointments_plugin_install() 
{
    // Setting plugin version
    global $ed_da_delta_appoinments_plugin_version;
    $ed_da_delta_appoinments_plugin_version = '1.0';
    add_option( 'ed_da_delta_appoinments_plugin_version', $ed_da_delta_appoinments_plugin_version );
    
    //create all tables with initial data
    $createTables = new ManipulateTables;
    $createTables->tabs();
    $createTables->createCustomerDetailsTable();
    $createTables->createAppointmentsTable();
    $createTables->createCustomerAddressTable();
    $createTables->createPaymentsTable();
}

// Creating HTML views
function ed_da_delta_appointments_plugin_html()
{
    //Create all tab views
    $createTabViews = new DeltaAppointmentsViews;
    $createTabViews->createAllTabs();
}

// Will delete tables from db upon deactivation
function ed_da_delta_appointments_plugin_deactivate()
{
    $createTables = new ManipulateTables;
    $createTables->dropTabsTable();
    $createTables->dropAppointmentsTable();
    $createTables->dropCustomerAddressTable();
    $createTables->dropPaymentsTable();
    $createTables->dropCustomerDetailsTable();
    delete_option( 'ed_da_delta_appoinments_plugin_version' );
    wp_clear_scheduled_hook('emailNotifications');
}

// Adding all javascript and css files
add_action( 'admin_enqueue_scripts', 'ed_da_delta_appointments_plugin_wp_admin_style' );
function ed_da_delta_appointments_plugin_wp_admin_style() {
        wp_enqueue_script( 'delta_appointments_plugin_bootstrap_js', plugins_url().'/delta-appointments/js/bootstrap.js' );
        wp_enqueue_script( 'delta_appointments_plugin_js', plugins_url().'/delta-appointments/js/controller.js' );
        wp_enqueue_script( 'delta_appointments_helpers_js', plugins_url().'/delta-appointments/js/helpers.js' );
        wp_enqueue_script( 'delta_appointments_validation_js', plugins_url().'/delta-appointments/js/validation.js' );
        
        wp_register_style( 'delta_appointments_plugin_bootstrap_css', plugins_url().'/delta-appointments/css/bootstrap-wrapper.css', false, '1.0.0' );
        wp_enqueue_style( 'delta_appointments_plugin_bootstrap_css');
        wp_localize_script( 'delta_appointments_plugin_js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
}

/*#81
 * Converts the first char to upper case and the rest to lower case
 * Works for most Unicode chars too
 */
function nameLowUp($theName)
{
    $fc = mb_strtoupper(mb_substr($theName, 0, 1));
    return $fc.mb_substr($theName, 1);
}

/* #91
 * Remove all non Unicode alphabetic, alphanumeric, or numeric chars based on hint
 * hint can be "a" - returns alphabetic Unicode chars removes everything else
 * "an" - returns alphanumeric, all alphabetic Unicode chars + numbers removes everything else
 * "n" - returns numeric chars only remove everything else
 * "notes" - trims whitespace, but preserves them and numbers inside the string also used
 * for dates as it is 23 January 1990
 */
function sanitizeInput($theVar, $hint)
{   
    //Using Filter_flag_strip_low, if you use filter_flag_strip_high it will remove non English chars too
    if ($hint == 'e') { return filter_var($theVar, FILTER_SANITIZE_EMAIL); }
    else { $theVar = filter_var($theVar, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW); }
    
    switch($hint)
    {
        case "a": return preg_replace("/[^\pL]/u", "", $theVar);
        break;
    
        case "n": return preg_replace("/[^\d]/u", "", $theVar);
        break;
    
        case "an": return preg_replace("/[^\d\pL]/u", "", $theVar);
        break;
    
        case "notes": return trim(preg_replace("/[^\d\040\pL]/u", "", $theVar));
    }
    // matches all unicode + whitespace: "/[^\040\pL]/u"
}

/* #121
 * Add new customer/record. Receives data from controller.js #16
 * and calls insertCustomerAddressTable method in models.php #223
 * to insert a new row in customerDetails table
 */
add_action( 'wp_ajax_ed_da_delta_appointments_addcustomer_action', 'ed_da_delta_appointments_addcustomer_action_callback' );
function ed_da_delta_appointments_addcustomer_action_callback() {
	global $wpdb;
    
    $customerDetailsTable = array();
    
    //Calling sanitizeInput#89
    $customerDetailsTable['first_name'] = isset($_POST['fname']) ? sanitizeInput($_POST['fname'], "a") : "";
    $customerDetailsTable['middle_name'] = isset($_POST['mname']) ? sanitizeInput($_POST['mname'], "a") : "";
    $customerDetailsTable['last_name'] = isset($_POST['lname']) ? sanitizeInput($_POST['lname'], "a") : "";
    $customerDetailsTable['email'] = isset($_POST['email']) ? sanitizeInput($_POST['email'], "e") : "";
    $customerDetailsTable['phone_home'] = isset($_POST['phhome']) ? sanitizeInput($_POST['phhome'], "n") : "";
    $customerDetailsTable['phone_cell'] = isset($_POST['phcell']) ? sanitizeInput($_POST['phcell'], "n") : "";
    $customerDetailsTable['birth_date'] = isset($_POST['birthdate']) ? sanitizeInput($_POST['birthdate'], "notes") : "";
    $customerDetailsTable['notes'] = isset($_POST['cnotes']) ? sanitizeInput($_POST['cnotes'], "notes") : "";
    
    $customerDetailsTable['birth_date'] = convertDate($customerDetailsTable['birth_date'], false);
    
    //Calling nameLowUp #79 to convert the first char to upper and the rest to lowercase
    $customerDetailsTable['first_name'] = nameLowUp($customerDetailsTable['first_name']);
    $customerDetailsTable['middle_name'] = nameLowUp($customerDetailsTable['middle_name']);
    $customerDetailsTable['last_name'] = nameLowUp($customerDetailsTable['last_name']);
    
    $models = new ManipulateTables;
    
    // Inserting into Customer Details table. The return value is the id_cd
    // so we can use it as the foreign key to insert data into the address table
    $returnCustomerDetailsId = $models->insertCustomerDetailsTable($customerDetailsTable);    
    
    // If insert into Customer Details table failed we echo Error response
    // else we send customer id (id_cd as a foreign key in other table) to Customer Address
    // table for insertion models.php::insertCustomerAddressTable #258    
    if ($returnCustomerDetailsId == -1) 
    {
        echo -1;
    }
    else 
    { 
        $customerAddress = array();
        $customerAddress['country'] = isset($_POST['country']) ? sanitizeInput($_POST['country'], "notes") : "";
        $customerAddress['city'] = isset($_POST['city']) ? sanitizeInput($_POST['city'], "notes") : "";
        $customerAddress['street_number'] = isset($_POST['streetnumber']) ? sanitizeInput($_POST['streetnumber'], "n") : "";
        $customerAddress['province'] = isset($_POST['province']) ? sanitizeInput($_POST['province'], "notes") : "";
        $customerAddress['apt_number'] = isset($_POST['aptnumber']) ? sanitizeInput($_POST['aptnumber'], "n") : "";
        $customerAddress['street_name'] = isset($_POST['streetname']) ? sanitizeInput($_POST['streetname'], "notes") : "";
        $customerAddress['postal_code'] = isset($_POST['postalcode']) ? sanitizeInput($_POST['postalcode'], "notes") : "";
        $customerAddress['id_cd'] = $returnCustomerDetailsId;
 
        $returnMessage = "";

        if($models->insertCustomerAddressTable($customerAddress)) 
        {
            $returnMessage = "<h5><mark>A record for ".$customerDetailsTable['first_name']." ".$customerDetailsTable['last_name'].
                             " has been created. Refresh to see the changes.</mark></h5>
                             <button type='button' class='btn btn-default' onclick=reloadz()>Refresh</button>";
            
            // Refer to views.php#32 about table_tabs
            // Basically sets which tab should loaded next time i.e.
            // If records were updated then record tab loads after page refresh
            $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
            $wpdb->update( $table_tabs, 
                            array(
                                'tabs' => 'recordsTab'
                            ), 
                            array( 'id' => 1 ));
            
            echo $returnMessage;
        }
        else
        {
            echo -1;
        }
    }
    
	wp_die();
}

/* #203
 * Checks if the record already exists. Checking only first and last name and if it exists the user is given a choice
 * to create a record anyway or abort
 */ 
add_action( 'wp_ajax_ed_da_delta_appointments_checkifexists_action', 'ed_da_delta_appointments_checkifexists_action_callback' );
function ed_da_delta_appointments_checkifexists_action_callback() {
	
    $personalData = array();
    
    $personalData['first_name'] = isset($_POST['fname']) ? sanitizeInput($_POST['fname'], "a") : "";
    $personalData['last_name'] = isset($_POST['lname']) ? sanitizeInput($_POST['lname'], "a") : "";
    
    $getRecord = new ManipulateTables;
    
    if(count($getRecord->getFirstLastName($personalData['first_name'], $personalData['last_name']))) { echo 1; }
    else { echo 0; }
    wp_die();
}

/* #222
 * Search function. Receives search parameters from controller.js
 * and then calls makeSearch in models.php
 */
add_action( 'wp_ajax_ed_da_delta_appointments_search_action', 'ed_da_delta_appointments_search_action_callback' );
function ed_da_delta_appointments_search_action_callback() {
	global $wpdb;
    
    $customerTable = array();
    $customerAddress = array();
    $customerTable['id_cd'] = "";
    $customerTable['first_name'] = isset($_POST['fname']) ? sanitizeInput($_POST['fname'], "a") : "";
    $customerTable['middle_name'] = isset($_POST['mname']) ? sanitizeInput($_POST['mname'], "a") : "";
    $customerTable['last_name'] = isset($_POST['lname']) ? sanitizeInput($_POST['lname'], "a") : "";
    $customerTable['email'] = isset($_POST['email']) ? sanitizeInput($_POST['email'], "e") : "";
    $customerTable['phone_home'] = isset($_POST['phhome']) ? sanitizeInput($_POST['phhome'], "n") : "";
    $customerTable['phone_cell'] = isset($_POST['phcell']) ? sanitizeInput($_POST['phcell'], "n") : "";
    $customerTable['birth_date'] = isset($_POST['birthdate']) ? sanitizeInput($_POST['birthdate'], "notes") : "";
    
    $customerTable['birth_date'] = convertDate($customerTable['birth_date'], false);
 
    $customerAddress['country'] = isset($_POST['country']) ? sanitizeInput($_POST['country'], "notes") : "";
    $customerAddress['city'] = isset($_POST['city']) ? sanitizeInput($_POST['city'], "notes") : "";
    $customerAddress['province'] = isset($_POST['province']) ? sanitizeInput($_POST['province'], "notes") : "";
    $customerAddress['street_number'] = isset($_POST['streetnumber']) ? sanitizeInput($_POST['streetnumber'], "n") : "";
    $customerAddress['apt_number'] = isset($_POST['aptnumber']) ? sanitizeInput($_POST['aptnumber'], "n") : "";
    $customerAddress['street_name'] = isset($_POST['streetname']) ? sanitizeInput($_POST['streetname'], "notes") : "";
    $customerAddress['postal_code'] = isset($_POST['postalcode']) ? sanitizeInput($_POST['postalcode'], "notes") : "";
    
    //Calling nameLowUp #79 to convert the first char to upper and the rest to lowercase
    $customerTable['first_name'] = nameLowUp($customerTable['first_name']);
    $customerTable['middle_name'] = nameLowUp($customerTable['middle_name']);
    $customerTable['last_name'] = nameLowUp($customerTable['last_name']);
    
    $searchModelsObject = new ManipulateTables();
    $returnedValue = $searchModelsObject->makeSearch($customerTable, $customerAddress);
    
    // Refer to views.php#32 about table_tabs
    $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
    $wpdb->update( $table_tabs, 
                    array( 
                        'tabs' => 'searchTab'
                    ), 
                    array( 'id' => 1 ));
    
    if(empty($returnedValue))
    {
        echo 0;
    }
    else
    {
        echo json_encode($returnedValue);
    }
  
   wp_die();
}

/* #279
 * Fulledit Action. Either retrieves values from the db for editing/updating or
 * saves new values from edit/update form into a db table.
 * controller.js::makeEdit - for retrieving values 
 * and controller.js::buttonSave - for saving new/updated values
 */
add_action( 'wp_ajax_ed_da_delta_appointments_fulledit_action', 'ed_da_delta_appointments_fulledit_action_callback' );
function ed_da_delta_appointments_fulledit_action_callback()
{
    global $wpdb;
    
    $workWithRecordModels = new ManipulateTables();
    
    $updateTables = array();
    $customerDetails = array();
    $customerAddress = array();
    
    $updateTables['id_cd'] = isset($_POST['id_cd']) ? sanitizeInput($_POST['id_cd'], "n") : "";
    $updateTables['action_name'] = isset($_POST['action_name']) ? sanitizeInput($_POST['action_name'], "notes") : "";
    
    // Calling workOnCustomerWithAddress#350 in models.php to populate an edit form in controller.js with existing values
    if ($updateTables['action_name'] == 'getEditValues') 
    {
        $returnedCustomerWithAddress = $workWithRecordModels->workOnCustomerWithAddress($updateTables['id_cd'], $updateTables['id_cd'], $updateTables['action_name']);
        echo json_encode($returnedCustomerWithAddress);
    }
    else if ($updateTables['action_name'] == 'fullEdit')
    {        
        // If the action_name is fullEdit then we get values from controller.js from the edit form
        // and then call workOnCustomerWithAddress to update tables
        //Customer Details fields
        //if( isset($_POST['id_cd']) ) { $customerDetails['id_cd'] = $_POST['id_cd']; }
        $customerDetails['id_cd'] = isset($_POST['id_cd']) ? sanitizeInput($_POST['id_cd'], "n") : "";
        $customerDetails['first_name'] = isset($_POST['first_name']) ? sanitizeInput($_POST['first_name'], "a") : "";
        $customerDetails['middle_name'] = isset($_POST['middle_name']) ? sanitizeInput($_POST['middle_name'], "a") : "";
        $customerDetails['last_name'] = isset($_POST['last_name']) ? sanitizeInput($_POST['last_name'], "a") : "";
        $customerDetails['email'] = isset($_POST['email']) ? sanitizeInput($_POST['email'], "e") : "";
        $customerDetails['phone_home'] = isset($_POST['phone_home']) ? sanitizeInput($_POST['phone_home'], "n") : "";
        $customerDetails['phone_cell'] = isset($_POST['phone_cell']) ? sanitizeInput($_POST['phone_cell'], "n") : "";
        $customerDetails['birth_date'] = isset($_POST['birth_date']) ? sanitizeInput($_POST['birth_date'], "notes") : "";
        $customerDetails['notes'] = isset($_POST['notes']) ? sanitizeInput($_POST['notes'], "notes") : "";

        $customerDetails['birth_date'] = convertDate($customerDetails['birth_date'], false);
        
        //Calling nameLowUp #79 to convert the first char to upper and the rest to lowercase
        $customerDetails['first_name'] = nameLowUp($customerDetails['first_name']);
        $customerDetails['middle_name'] = nameLowUp($customerDetails['middle_name']);
        $customerDetails['last_name'] = nameLowUp($customerDetails['last_name']);
        
        //Customer Address fields
        //if( isset($_POST['id']) ) { $customerAddress['id'] = $_POST['id']; }
        $customerAddress['id'] = isset($_POST['id']) ? sanitizeInput($_POST['id'], "n") : "";
        $customerAddress['country'] = isset($_POST['country']) ? sanitizeInput($_POST['country'], "notes") : "";
        $customerAddress['city'] = isset($_POST['city']) ? sanitizeInput($_POST['city'], "notes") : "";
        $customerAddress['province'] = isset($_POST['province']) ? sanitizeInput($_POST['province'], "notes") : "";
        $customerAddress['street_number'] = isset($_POST['street_number']) ? sanitizeInput($_POST['street_number'], "n") : "";
        $customerAddress['apt_number'] = isset($_POST['apt_number']) ? sanitizeInput($_POST['apt_number'], "n") : "";
        $customerAddress['street_name'] = isset($_POST['street_name']) ? sanitizeInput($_POST['street_name'], "notes") : "";
        $customerAddress['postal_code'] = isset($_POST['postal_code']) ? sanitizeInput($_POST['postal_code'], "notes") : "";
        
        //Calling nameLowUp #79 to convert the first char to upper and the rest to lowercase
        $customerAddress['city'] = nameLowUp($customerAddress['city']);
        $customerAddress['province'] = nameLowUp($customerAddress['province']);
        $customerAddress['street_name'] = nameLowUp($customerAddress['street_name']);
        
        $returnedValue = $workWithRecordModels->workOnCustomerWithAddress($customerDetails, $customerAddress, $updateTables['action_name']);
        
        // Refer to views.php#32 about table_tabs
        $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
        $wpdb->update( $table_tabs, 
                        array( 
                            'tabs' => 'recordsTab'
                        ), 
                        array( 'id' => 1 ));
        
        echo $returnedValue;
    }
    elseif ($updateTables['action_name'] == 'cancel') { }
    elseif ($updateTables['action_name'] == 'delRecord') 
    { 
        // If action name is delRecord it means we delete the record using id_cd
        $returnValue = $workWithRecordModels->deleteRecords($updateTables['id_cd']);
        if($returnValue === FALSE) { echo 'error'; }
        else 
        { 
            // Refer to views.php#32 about table_tabs
            $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
            $wpdb->update( $table_tabs, 
                            array( 
                                'tabs' => 'recordsTab'
                            ), 
                            array( 'id' => 1 ));
            
            echo $returnValue;     
        }
    }
    
    wp_die();
}

/* #379
 * Function to display all info. Retrieves all fields from all tables to display full info
 * for a particular record
 */
add_action( 'wp_ajax_ed_da_delta_appointments_displayinfo_action', 'ed_da_delta_appointments_displayinfo_action_callback' );
function ed_da_delta_appointments_displayinfo_action_callback() 
{
    $customerDetails['id_cd'] = isset($_POST['id_cd']) ? sanitizeInput($_POST['id_cd'], "n") : "";
    $action_name = isset($_POST['action_name']) ? sanitizeInput($_POST['action_name'], "a") : "";
    
    $displayInfo = new ManipulateTables;
    
    $returnedValue = $displayInfo->displayInfoModels($customerDetails['id_cd'], $action_name);
    
    echo json_encode($returnedValue);
    
    wp_die();
}

/* #398
 * Function to enter appointment or payment details in the respective database table
 */
add_action( 'wp_ajax_ed_da_delta_appointments_apptAndPay_action', 'ed_da_delta_appointments_apptAndPay_action_callback' );
function ed_da_delta_appointments_apptAndPay_action_callback()
{
    global $wpdb;
    
    $workWithModels = new ManipulateTables();
    
    $customerDetails = array();
    $appointmentDetails = array();
    $paymentDetails = array();
    $actionName = isset($_POST['action_name']) ? sanitizeInput($_POST['action_name'], "a") : "";
    $customerDetails['id_cd'] = isset($_POST['id_cd']) ? sanitizeInput($_POST['id_cd'], "n") : "";

    // Enter appointment detaiils if actionName is makeAppt
    if ($actionName == 'makeAppt') 
    {   
        $appointmentDetails['date'] = isset($_POST['date']) ? sanitizeInput($_POST['date'], "notes") : "";
        if ( isset($_POST['time']) ) { $appointmentDetails['time'] = $_POST['time']; }
        $appointmentDetails['venue'] = isset($_POST['venue']) ? sanitizeInput($_POST['venue'], "notes") : "";
        $appointmentDetails['purpose'] = isset($_POST['purpose']) ? sanitizeInput($_POST['purpose'], "notes") : "";
        $appointmentDetails['notes'] = isset($_POST['appt_notes']) ? sanitizeInput($_POST['appt_notes'], "notes") : "";
        
        $appointmentDetails['date'] = convertDate($appointmentDetails['date'], false);
        
        $returnedApptAddMsg = $workWithModels->makeApptandPay($customerDetails['id_cd'], $appointmentDetails, $actionName);
        
        // Refer to views.php#32 about table_tabs
        $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
        $wpdb->update( $table_tabs, 
                        array( 
                            'tabs' => 'appointmentsTab'
                        ), 
                        array( 'id' => 1 ));
        
        echo "returned msg " + $returnedApptAddMsg;
    }
    // Enter payment details if actionName is enterPay
    else if ($actionName == 'enterPay')
    {   
        $paymentDetails['date'] = isset($_POST['date']) ? sanitizeInput($_POST['date'], "notes") : "";
        $paymentDetails['amount'] = isset($_POST['amount']) ? sanitizeInput($_POST['amount'], "an") : "";
        $paymentDetails['purpose'] = isset($_POST['purpose']) ? sanitizeInput($_POST['purpose'], "notes") : "";
        $paymentDetails['notes'] = isset($_POST['notes']) ? sanitizeInput($_POST['notes'], "notes") : "";
        
        $paymentDetails['date'] = convertDate($paymentDetails['date'], false);
        
        $returnedEnterPayment = $workWithModels->makeApptandPay($customerDetails['id_cd'], $paymentDetails, $actionName);
        
        // Refer to views.php#32 about table_tabs
        $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
        $wpdb->update( $table_tabs, 
                        array( 
                            'tabs' => 'paymentsTab'
                        ), 
                        array( 'id' => 1 ));
        
        echo $returnedEnterPayment;
        
    }
    elseif ($actionName == 'cancel') { }
    
    wp_die();
}

/* #465
 * Function to retrieve appointment times to avoid time conflict and 
 * also serves to delete an appointment
 */
add_action( 'wp_ajax_ed_da_delta_appointments_enterApptPay_action', 'ed_da_delta_appointments_enterApptPay_action_callback' );
function ed_da_delta_appointments_enterApptPay_action_callback()
{
    global $wpdb;
    
    $theDate = isset($_POST['date']) ? sanitizeInput($_POST['date'], "notes") : "";
    $actionName = isset($_POST['action_name']) ? sanitizeInput($_POST['action_name'], "notes") : "";
    $id_cd = isset($_POST['id_cd']) ? sanitizeInput($_POST['id_cd'], "n") : "";
    $theTime = isset($_POST['time']) ? $_POST['time'] : "";
 
    $theDate = convertDate($theDate, false);
    
    $workWithModels = new ManipulateTables();
    
    //retrieve all appointment times
    if( $actionName == 'retrieveAllApptTimes' ) 
    { 
        // Calling displayInfoModels in models.php, depending on the action name it will perform
        // a respective action
        $allApptTimes = $workWithModels->displayInfoModels($id_cd, $actionName); 
        echo json_encode($allApptTimes);
    }
    elseif ( $actionName == 'delAppt' || $actionName == 'delApptView' )
    {
        // If action name is delAppt (originate from controller) or delApptView (from views.php) then it means to delete an appointment

            // Refer to views.php#32 about table_tabs
            $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
            $wpdb->update( $table_tabs, 
                            array( 
                                'tabs' => 'appointmentsTab'
                            ), 
                            array( 'id' => 1 ));
            
        echo $workWithModels->deleteAppt($id_cd, $actionName, $theDate, $theTime);
    }
    
    wp_die();
}

/* #509
 * Payment Delete: Used to delete payments
*/
add_action( 'wp_ajax_ed_da_delta_appointments_delPay_action', 'ed_da_delta_appointments_delPay_action_callback' );
function ed_da_delta_appointments_delPay_action_callback()
{
    global $wpdb;
    
    $id_cd = isset($_POST['id_cd']) ? sanitizeInput($_POST['id_cd'], "n") : "";
    $payDate = isset($_POST['payDate']) ? sanitizeInput($_POST['payDate'], "notes") : "";
    $payAmount = isset($_POST['payAmount']) ? sanitizeInput($_POST['payAmount'], "notes") : "";

    $payDate = convertDate($payDate, false);

    $workWithModels = new ManipulateTables();
    
    // Refer to views.php#32 about table_tabs
    $table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
    $wpdb->update( $table_tabs, 
                    array( 
                        'tabs' => 'paymentsTab'
                    ), 
                    array( 'id' => 1 ));
    
    echo $workWithModels->deletePay($id_cd, $payDate, $payAmount);
    
    wp_die();
}


/* $539
 * Upcoming appointment notifications.
 * Still working on it. 
 * wp_schedule_event works when someone visits the website
 * It's ok for the time being but working on making it not depend on visits
 
wp_schedule_event(time()+3600, 'twicedaily', 'emailNotifications');
function appointmentsNotify()
{   
    global $wpdb;
    
    wp_mail( "my_email@gmail.com", "twice daily notifications", "twice daily notifications" );

   
}
 * 
 */