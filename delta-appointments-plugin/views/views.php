<?php

/*
 * Class DeltaAppointmentViews - creates HTML views
 * 
 * public function createAllTabs() 
 *    - Calls functions that create all tabs for HTML tabbed view
 * private function searchTab($activeClass)
 *    - Creates the Search tab, contains search fields
 * private function viewRecordsTab($activeClass)
 *    - Creates the Records tab, contains personal data for a record
 * private function viewPaymentsTab($activeClass)
 *    - Creates the Payments tab, contains payment data
 * public function newRecordTab()
 *    - Creates the New Record tab, contains fields for creation of new record
 * private function viewAppointmentsTab($activeClass)
 *    - Creates the Appointments tab, contains appointment data
 * 
 */

defined('ABSPATH') or die ('Cannot access pages directly.');

class DeltaAppointmentsViews
{
        private $table_customerDetails;
        private $table_appointments;
        private $table_payments;
        private $table_customerAddress;
        private $charset_collate;
    
    function __construct()
    {   
        global $wpdb;
        $this->table_customerDetails = $wpdb->prefix . 'ed_da_delta_appointments_customer_details';
        $this->table_appointments = $wpdb->prefix . 'ed_da_delta_appointments_appointments';
        $this->table_payments = $wpdb->prefix . 'ed_da_delta_appointments_payments';
        $this->table_customerAddress = $wpdb->prefix . 'ed_da_delta_appointments_customer_address';
        $this->table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
        
        $this->charset_collate = $wpdb->get_charset_collate();
    }
    
    public function createAllTabs()
    {
        if ( !current_user_can( 'manage_options' ) )  
        {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        global $wpdb;
        
        /* #32
         * table_tabs is updated on add new record, delete record, edit record,
         * delete payment, delete appointment. There is just one row with two values 
         * id which is always 1 and tab value that can be searchTab, appointmentTab,
         * paymentTab, viewRecordsTab which indicate which tab should be active after
         * the plugin page reloads, refreshes after an action was performed.
         */
        $query = $wpdb->get_results("SELECT tabs FROM $this->table_tabs WHERE id=1");
        
        echo '<div class="bootstrap-wrapper">
        <ul class="nav nav-tabs">';
        
        switch($query[0]->tabs)
        {
            case "searchTab":
                    echo '<li class="active"><a data-toggle="tab" href="#makeSearch">Search</a></li>
                    <li><a data-toggle="tab" href="#viewAppts">View Appointments</a></li>
                    <li><a data-toggle="tab" href="#viewCustomers">View Records</a></li>
                    <li><a data-toggle="tab" href="#viewPayments">View Payments</a></li>
                    <li><a data-toggle="tab" href="#newCustomer">New Record</a></li>';
            break;
        
            case "appointmentsTab":
                    echo '<li><a data-toggle="tab" href="#makeSearch">Search</a></li>
                    <li class="active"><a data-toggle="tab" href="#viewAppts">View Appointments</a></li>
                    <li><a data-toggle="tab" href="#viewCustomers">View Records</a></li>
                    <li><a data-toggle="tab" href="#viewPayments">View Payments</a></li>
                    <li><a data-toggle="tab" href="#newCustomer">New Record</a></li>';
            break;
        
            case "recordsTab":
                    echo '<li><a data-toggle="tab" href="#makeSearch">Search</a></li>
                    <li><a data-toggle="tab" href="#viewAppts">View Appointments</a></li>
                    <li class="active"><a data-toggle="tab" href="#viewCustomers">View Records</a></li>
                    <li><a data-toggle="tab" href="#viewPayments">View Payments</a></li>
                    <li><a data-toggle="tab" href="#newCustomer">New Record</a></li>';
            break;
        
            case "paymentsTab":
                    echo '<li><a data-toggle="tab" href="#makeSearch">Search</a></li>
                    <li><a data-toggle="tab" href="#viewAppts">View Appointments</a></li>
                    <li><a data-toggle="tab" href="#viewCustomers">View Records</a></li>
                    <li class="active"><a data-toggle="tab" href="#viewPayments">View Payments</a></li>
                    <li><a data-toggle="tab" href="#newCustomer">New Record</a></li>';
            break;
        
            default:
                    echo '<li class="active"><a data-toggle="tab" href="#makeSearch">Search</a></li>
                    <li><a data-toggle="tab" href="#viewAppts">View Appointments</a></li>
                    <li><a data-toggle="tab" href="#viewCustomers">View Records</a></li>
                    <li><a data-toggle="tab" href="#viewPayments">View Payments</a></li>
                    <li><a data-toggle="tab" href="#newCustomer">New Record</a></li>';
        
        }

        echo '</ul><div class="tab-content">';

        switch($query[0]->tabs)
        {
            case "searchTab":   
                    $this->searchTab("tab-pane fade in active");
                    $this->viewAppointmentsTab(0);
                    $this->viewRecordsTab(0);
                    $this->viewPaymentsTab(0);
                    $this->newRecordTab(0);
            break;
        
            case "appointmentsTab": 
                    $this->searchTab(0);
                    $this->viewAppointmentsTab("tab-pane fade in active");
                    $this->viewRecordsTab(0);
                    $this->viewPaymentsTab(0);
                    $this->newRecordTab(0);
            break;
        
            case "recordsTab":
                    $this->searchTab(0);
                    $this->viewAppointmentsTab(0);
                    $this->viewRecordsTab("tab-pane fade in active");
                    $this->viewPaymentsTab(0);
                    $this->newRecordTab(0);
            break;
        
            case "paymentsTab":
                    $this->searchTab(0);
                    $this->viewAppointmentsTab(0);
                    $this->viewRecordsTab(0);
                    $this->viewPaymentsTab("tab-pane fade in active");
                    $this->newRecordTab(0);
            break;
        
            default:
                    $this->searchTab("tab-pane fade in active");
                    $this->viewAppointmentsTab(0);
                    $this->viewRecordsTab(0);
                    $this->viewPaymentsTab(0);
                    $this->newRecordTab(0);
        }

    }
    
    private function searchTab($activeClass)
    {
        //searchResultsDiv is hidden by js at the beginning of the search and displayed at the end of it.
        global $wpdb;
        
        $class = $activeClass ? $activeClass : "tab-pane fade";
       
        $searchTab = '<div class="'.$class.'" id="makeSearch"> 
                    <div id="searchResultsDiv">
                    <h4>Search Results</h4>
                      <p>The following records have been retrieved based on your request</p>         
                      <div class="table-responsive"><table class="table table-hover table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Home</th>
                            <th>Phone Cell</th>
                            <th>Options</th>
                          </tr>
                        </thead>
                        <tbody id="searchResultsBody">

                        </tbody>
                      </table>
                      </div>
                      </div><!--End searchResultDiv-->
                      
            <h4>Search by one or more criteria</h4>
            <h6>Edit, delete, make appointments, enter payments and view all infomation from within search results</h6>
            <form id="customerSearchForm" class="form-horizontal" role="form">';

        // Refer to helpers-views.php for the functions below
        $searchTab .= personalDataForm(true);
  
        $searchTab .= selectCountry(true);  
        
        $searchTab .= addressForm(true);
        
        
        $searchTab .= '<div class="form-group"><div class="col-sm-offset-1 col-sm-11">
                        <button type="button" id="buttonSearch" class="btn btn-default">Search</button>
                        </div></div></form></div><!--End of "makeSearch" div-->';  
        
        echo $searchTab;
    }

    private function viewRecordsTab($activeClass)
    {
        global $wpdb;
        $class = $activeClass ? $activeClass : "tab-pane fade";
        
            echo '<div class="'.$class.'" id="viewCustomers">
            <div class="table-responsive"><table class="table table-hover table-bordered table-striped">
            <thead>
              <tr>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Phone Home</th>
                <th>Phone Cell</th>
                <th>Birth Date</th>
                <th>Address</th>
                <th>Notes</th>
                <th>Options</th>
              </tr>
            </thead>
            <tbody>';
        
        $allCustomerDetailsRows = $wpdb->get_results("SELECT tc.id_cd AS 'id_cd', tc.first_name AS 'First_Name', tc.middle_name AS 'Middle_Name', tc.last_name AS 'Last_Name', tc.email AS 'Email', tc.phone_home AS 'Phone_Home', tc.phone_cell AS 'Phone_Cell', tc.birth_date AS 'Birth_Date', tc.notes AS 'Notes', CONCAT(IF(tad.apt_number IS NULL OR tad.apt_number = '','',CONCAT('Apt ',tad.apt_number,', ')), IF(tad.street_number IS NULL OR tad.street_number = '','',CONCAT(tad.street_number,' ')), IF(tad.street_name IS NULL OR tad.street_name = '','', CONCAT(tad.street_name,', ')), IF(tad.city IS NULL OR tad.city = '','', CONCAT(tad.city,', ')), IF(tad.province IS NULL OR tad.province ='','', CONCAT(tad.province,', ')), IF(tad.postal_code IS NULL OR tad.postal_code = '','', CONCAT(tad.postal_code,', ')), IF(tad.country IS NULL OR tad.country = '','',tad.country)) AS 'Address' FROM $this->table_customerDetails AS tc JOIN $this->table_customerAddress AS tad ON tc.id_cd = tad.id_cd ORDER BY tc.last_name ASC ");

    if ( $allCustomerDetailsRows != NULL )
    {                        
        foreach ( $allCustomerDetailsRows as $oneRow ) 
        {
            $id_cd = (isset($oneRow->id_cd)) ? $oneRow->id_cd: '';
            $birth_date = (isset($oneRow->Birth_Date)) ? $oneRow->Birth_Date: '';
            $birth_date = convertDate($birth_date, true);
            
            echo '<tr><td>'; 
            echo (isset($oneRow->Last_Name)) ? $oneRow->Last_Name: '';
            echo '</td><td>'; 
            echo (isset($oneRow->Middle_Name)) ? $oneRow->Middle_Name: '';
            echo '</td><td>'; 
            echo (isset($oneRow->First_Name)) ? $oneRow->First_Name: ''; 
            echo '</td><td>'; 
            echo (isset($oneRow->Email)) ? $oneRow->Email: ''; 
            echo '</td><td>'; 
            echo (isset($oneRow->Phone_Home)) ? $oneRow->Phone_Home: ''; 
            echo '</td><td>'; 
            echo (isset($oneRow->Phone_Cell)) ? $oneRow->Phone_Cell: '';
            echo '</td><td>'; 
            echo $birth_date; 
            echo '</td><td>'; 
            echo (isset($oneRow->Address)) ? $oneRow->Address: ''; 
            echo '</td><td>'; 
            echo (isset($oneRow->Notes)) ? $oneRow->Notes: ''; 
            echo '<td name ="'.$id_cd.'"><a name="delRecord" id="delRecord">Delete</a></td>
          </tr>'; 
        }
    }
    else
    {
        echo 'Nothing to display. Either there is an error or your list is empty.';
    }

     echo '</tbody></table></div></div>';
    }
    
    private function viewPaymentsTab($activeClass)
    {
        $class = $activeClass ? $activeClass : "tab-pane fade";
        global $wpdb;
        $payTable = "";
        $payTable = '<div class="'.$class.'" id="viewPayments">
    <div class="table-responsive"><table class="table table-hover table-bordered table-striped">';
    

    $allPaymentRows = $wpdb->get_results( 
        "SELECT tp.date AS 'Payment_Date', tp.amount AS 'Amount', tp.purpose AS 'Purpose', tp.notes AS 'Notes', tc.id_cd AS 'id_cd', tc.first_name AS 'First_Name', tc.last_name AS 'Last_Name', tc.phone_home AS 'Phone_Home', tc.phone_cell AS 'Phone_Cell'
        FROM $this->table_payments AS tp JOIN $this->table_customerDetails AS tc ON tp.id_cd = tc.id_cd ORDER BY tp.date DESC");

    if ( $allPaymentRows != NULL )
    {
        $payTable .= '<thead>
          <tr>
            <th>Payment Date</th>
            <th>Amount</th>
            <th>Purpose</th>
            <th>Notes</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Home Phone</th>
            <th>Cell Phone</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>';
        //print_r($resultsAppointments);
        foreach ( $allPaymentRows as $oneRow ) 
        {
                $payAmount = (isset($oneRow->Amount)) ? $oneRow->Amount: '';
                $payDate = (isset($oneRow->Payment_Date)) ? $oneRow->Payment_Date: '';
                $id_cd = (isset($oneRow->id_cd)) ? $oneRow->id_cd: '';
                $payDate = convertDate($payDate, true);
                
                $payTable .= '<tr><td>'; 
                $payTable .= $payDate; 
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->Amount)) ? $oneRow->Amount: ''; 
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->Purpose)) ? $oneRow->Purpose: ''; 
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->Notes)) ? $oneRow->Notes: ''; 
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->First_Name)) ? $oneRow->First_Name: '';
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->Last_Name)) ? $oneRow->Last_Name: ''; 
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->Phone_Home)) ? $oneRow->Phone_Home: ''; 
                $payTable .= '</td><td>'; 
                $payTable .= (isset($oneRow->Phone_Cell)) ? $oneRow->Phone_Cell: ''; 
                $payTable .= '</td><td name ="'.$id_cd.'" abbr="'.$payDate.'" axis="'.$payAmount.'"><a name="delPayView" id="delPay">Delete</a></td></tr>';
        }
    } 
    else { $payTable .= '<tbody>View Payments. Nothing to display'; }

        $payTable .= '</tbody></table></div></div>'; 
        echo $payTable;
    }
    
    public function newRecordTab()
    {   
        $newCustTab = '<div class="tab-pane fade" id="newCustomer"><h4>Add a New Record </h4><span id="newRecordErr" class="text-danger"></span>
                       <form id="customerAddForm" class="form-horizontal" role="form">';

        // Refer to helpers-views.php for the functions below
        $newCustTab .= personalDataForm(false);
  
        $newCustTab .= selectCountry(false);  
        
        $newCustTab .= addressForm(false);

        $newCustTab .= '<div class="form-group">        
                        <div class="col-sm-offset-1 col-sm-11">
                        <button type="button" id="buttonAddCustomer" class="btn btn-default">Add New Record</button>
                        </div></div></form></div></div>';
        
        echo $newCustTab;
        
    }
    
    private function viewAppointmentsTab($activeClass)
    {
        $class = $activeClass ? $activeClass : "tab-pane fade";
            global $wpdb;
            echo '<div class="'.$class.'" id="viewAppts">
                <div class="table-responsive"><table class="table table-hover table-bordered table-striped">
        <thead>
          <tr>
            <th>Appointment Date</th>
            <th>Appointment Time</th>
            <th>Purpose</th>
            <th>Venue</th>
            <th>Notes</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Home Phone</th>
            <th>Cell Phone</th>
            <th>Customer Notes</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>';

    $allAppointmentRows = $wpdb->get_results( 
        "SELECT ta.date AS 'Appointment_Date', ta.time AS 'Time', ta.purpose AS 'Purpose', ta.venue AS 'Venue', ta.notes AS 'Notes', tc.id_cd AS 'id_cd', tc.first_name AS 'First_Name', tc.last_name AS 'Last_Name', tc.phone_home AS 'Phone_Home', tc.phone_cell AS 'Phone_Cell', tc.notes AS 'Customer_Notes' FROM $this->table_appointments AS ta JOIN $this->table_customerDetails AS tc ON ta.id_cd = tc.id_cd ORDER BY ta.date DESC");

    if ( $allAppointmentRows != NULL )
    {
        foreach ( $allAppointmentRows as $oneApptsRow ) 
        {
                $id_cd = (isset($oneApptsRow->id_cd)) ? $oneApptsRow->id_cd : "";
                $appt_time = (isset($oneApptsRow->Time)) ? $oneApptsRow->Time : "";
                $appt_date = (isset($oneApptsRow->Appointment_Date)) ? $oneApptsRow->Appointment_Date : "";
                $appt_date = convertDate($appt_date, true);

                echo '<tr>
                <td>'; echo $appt_date; 
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Time)) ? $oneApptsRow->Time : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Purpose)) ? $oneApptsRow->Purpose : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Venue)) ? $oneApptsRow->Venue : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Notes)) ? $oneApptsRow->Notes : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->First_Name)) ? $oneApptsRow->First_Name : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Last_Name)) ? $oneApptsRow->Last_Name : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Phone_Home)) ? $oneApptsRow->Phone_Home : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Phone_Cell)) ? $oneApptsRow->Phone_Cell : "";
                echo '</td><td>'; 
                echo (isset($oneApptsRow->Customer_Note)) ? $oneApptsRow->Customer_Note : "";
                echo '</td><td name="'.$id_cd.'" abbr="'.$appt_time.'" axis="'.$appt_date.'"><a name="delApptView" id="delAppt">Delete</a></td></tr>';

        }
    } else { echo 'View Appointments. Nothing to display'; }

        echo '</tbody></table> <button type="button" class="btn btn-default" onclick="reloadz()">Refresh</button>
      <p class="text-warning">Click Refresh to sync the list with the database for up-to-date info.</p> </div></div><!--Two final divs-->';
    }
    
}