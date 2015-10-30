<?php

defined('ABSPATH') or die ('Cannot access pages directly.');

/* 
 * class ManipulateTables - php model, create, update, delete, retrieve data from mysql database tables
 * 
 * METHODS:
 * createCustomerDetailsTable()
 *    - Creates CustomerDetails table and populates with sample data, holds personal details for a record
 * createAppointmentsTable()
 *    - Creates AppointmensTable and sample data, holds appointment details
 * createCustomerAddressTable()
 *    - Creates CustomerAddressTable, holds addresses
 * createPaymentsTable()
 *     - Creates payments table
 * insertCustomerDetailsTable(&$customerDetailsTable)
 *     - Inserts data into CustomerDetails
 * insertCustomerAddressTable(&$customerAddressTable)
 *    - Inserts data into CustomerAddress table
 * makeSearch(&$searchCustomer)
 *    - Performs search based on personal details (customer details) and address
 * getFirstLastName($fname, $lname)
 *    - Get first and last name of a record
 * workOnCustomerWithAddress(&$customerDetails, &$customerAddress, $actionName)
 *    - Retrieves values from customer details and address tables for updating purposes
 *    - Updates customer details and address tables
 * makeApptandPay($customerId, &$tableDetails, $actionName)
 *    - Inserts either appointment or payment details into respective tables
 * displayInfoModels($customerId, $actionName)
 *    - Retrieves data from all tables i.e. customer details, address, appointments and payments
 * deleteAppt
 *    - Deletes an appointment
 * deletePay
 *    - Deletes a payment
 * deleteRecords
 *    - Deletes a record i.e. customer details and address + appointments and payments
 * dropCustomerDetailsTable()
 *    - Drops Customer details table usually upon deactivation
 * dropCustomerAddressTable()
 *    - Drops Address table usually upon deactivation
 * dropAppointmentsTable()
 *    - Drops Appointments table usually upon deactivation
 * dropPaymentsTable()
 *    - Drops Payments table usually upon deactivation  
 * 
 */

class ManipulateTables
{
    private $table_customerDetails;
    private $table_appointments;
    private $table_payments;
    private $table_customerAddress;
    private $charset_collate;
    
    // Creates table names using wpdb prefix
    function __construct()
    {   
        global $wpdb;
        $this->table_tabs = $wpdb->prefix . 'ed_da_delta_appointments_tabs';
        $this->table_customerDetails = $wpdb->prefix . 'ed_da_delta_appointments_customer_details';
        $this->table_appointments = $wpdb->prefix . 'ed_da_delta_appointments_appointments';
        $this->table_payments = $wpdb->prefix . 'ed_da_delta_appointments_payments';
        $this->table_customerAddress = $wpdb->prefix . 'ed_da_delta_appointments_customer_address';
        
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    public function tabs()
    {
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_tabs (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `tabs` varchar(40) NOT NULL,
                PRIMARY KEY  (`id`)
                ) $this->charset_collate;";

        // Using dbDelta function to create tables, need to be loaded
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    
        $wpdb->insert( 
		$this->table_tabs, 
		array( 
			'tabs' => 'searchTab', 
		     ));
    }
    
/* #90
 * Creates customer details table (personal details) and populates it with sample data
 */
    public function createCustomerDetailsTable()
    {
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS $this->table_customerDetails (
                `id_cd` int(10) NOT NULL AUTO_INCREMENT,
                `first_name` varchar(40) NOT NULL,
                `middle_name` varchar(40) NOT NULL,
                `last_name` varchar(50) NOT NULL,
                `email` varchar(100) NOT NULL,
                `phone_home` varchar(20) NOT NULL,
                `phone_cell` varchar(20) NOT NULL,
                `birth_date` date NOT NULL,
                `notes` varchar(255) NOT NULL,
                PRIMARY KEY  (`id_cd`),
                UNIQUE KEY id (id_cd)
                ) $this->charset_collate;";

        // Using dbDelta function to create tables, need to be loaded
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    
        $wpdb->insert( 
		$this->table_customerDetails, 
		array( 
			'first_name' => 'Edward', 
            'middle_name' => 'Ontario',
			'last_name' => 'Trentovski',
            'email' => 'edward@edward.com',
            'phone_home' => '4168885555',
            'phone_cell' => '4168885556',
            'birth_date' => '1990-10-10',
            'notes' => 'check out my other plugins and themes'
		     ));
    
        $wpdb->insert( 
		$this->table_customerDetails, 
		array( 
			'first_name' => 'Julia', 
            'middle_name' => 'Manitoba',
			'last_name' => 'Foreign',
            'email' => 'julia@foreign.com',
            'phone_home' => '4168887777',
            'phone_cell' => '4168887776',
            'birth_date' => '1998-02-06',
            'notes' => 'check out my other plugins and themes'
		     ));
    }

/* #141
 * Creates Appointments Table. Holds appoinment details.
 * id_cd is the foreign key from Customer Details
 */
    public function createAppointmentsTable()
    {
        global $wpdb;
    
        $sql =  "CREATE TABLE IF NOT EXISTS $this->table_appointments (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `date` date NOT NULL,
                `time` varchar(80) DEFAULT NULL,
                `purpose` varchar(255) DEFAULT NULL,
                `venue` varchar(255) DEFAULT NULL,
                `notes` varchar(255) DEFAULT NULL,
                `id_cd` int(10) NOT NULL,
                PRIMARY KEY  (`id`),
                KEY `fk_Customer_Details_appointments` (`id_cd`),
                UNIQUE KEY id (id)
                ) $this->charset_collate;";

        // Using dbDelta function to create tables, need to be loaded
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    
        $sql = "ALTER TABLE $this->table_appointments
        ADD CONSTRAINT `fk_Customer_Details_appointments` FOREIGN KEY (`id_cd`) REFERENCES $this->table_customerDetails (`id_cd`)";
    
        dbDelta( $sql );
        
        $wpdb->insert( 
		$this->table_appointments, 
		array( 
			'date' => '2015-01-23', 
            'time' => '04:00 PM',
			'purpose' => 'Hair Cut',
            'venue' => 'Curly Hair Salon',
            'notes' => 'my fave customer $500 tip for $20 hair cut',
            'id_cd' => 1
		     ));
    
        $wpdb->insert( 
		$this->table_appointments, 
		array( 
			'date' => '2015-03-22',
            'time' => '04:30 PM',
			'purpose' => 'Hair Dyeing',
            'venue' => 'Curly Hair Salon',
            'notes' => 'least fave customer no tip',
            'id_cd' => 2
		     ));
    }

/* #194
 * Creates an Address table. Holds addresses. id_cd is the foreign key from Customer Details
 */    
    public function createCustomerAddressTable()
    {
        global $wpdb;
         
        $sql =  "CREATE TABLE IF NOT EXISTS $this->table_customerAddress (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `country` varchar(40) DEFAULT NULL,
                `province` varchar(40) DEFAULT NULL,
                `city` varchar(40) DEFAULT NULL,
                `street_name` varchar(40) DEFAULT NULL,
                `street_number` int(11) DEFAULT NULL,
                `apt_number` int(11) DEFAULT NULL,
                `postal_code` varchar(10) DEFAULT NULL,
                `id_cd` int(10) NOT NULL,
                PRIMARY KEY  (`id`),
                KEY `fk_Customer_Details_address` (`id_cd`)
                ) $this->charset_collate;";

        // Using dbDelta function to create tables, need to be loaded
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    
        $sql = "ALTER TABLE $this->table_customerAddress
        ADD CONSTRAINT `fk_Customer_Details_address` FOREIGN KEY (`id_cd`) REFERENCES $this->table_customerDetails (`id_cd`)";
    
        dbDelta( $sql );
        
        $wpdb->insert( 
		$this->table_customerAddress, 
		array( 
			'country' => 'Canada', 
			'city' => 'Toronto',
            'province' => 'Ontario',
            'street_name' => 'Bloor St',
            'street_number' => '220',
            'apt_number' => '3211',
            'postal_code' => 'M5F4C3',
            'id_cd' => 1
		     ));
    
        $wpdb->insert( 
		$this->table_customerAddress, 
		array( 
			'country' => 'Canada', 
			'city' => 'Toronto',
            'province' => 'Ontario',
            'street_name' => 'Yonge St',
            'street_number' => '330',
            'apt_number' => '11',
            'postal_code' => 'M5G5V5',
            'id_cd' => 2
		     ));
    }

/* #251
 * Creates a Payments table. Holds payments. id_cd is the foreign key from Customer Details
 */     
    public function createPaymentsTable()
    {
        global $wpdb;
    
        $sql =  "CREATE TABLE IF NOT EXISTS $this->table_payments (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `date` date DEFAULT NULL,
                `amount` int(10) DEFAULT NULL,
                `purpose` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
                `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                `id_cd` int(10) NOT NULL,
                PRIMARY KEY  (`id`),
                KEY `fk_Customer_Details_payments` (`id_cd`),
                UNIQUE KEY id (id)
                ) $this->charset_collate;";

        // Using dbDelta function to create tables, need to be loaded
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    
        $sql = "ALTER TABLE $this->table_payments
        ADD CONSTRAINT `fk_Customer_Details_payments` FOREIGN KEY (`id_cd`) REFERENCES $this->table_customerDetails (`id_cd`)";
    
        dbDelta( $sql );
        
        $wpdb->insert( 
		$this->table_payments, 
		array( 
			'date' => '2015-07-22', 
			'amount' => 200,
            'purpose' => 'hair cut',
            'notes' => '',
            'id_cd' => 1
		     ));
    
        $wpdb->insert( 
		$this->table_payments, 
		array( 
			'date' => '2015-08-14', 
			'amount' => 300,
            'purpose' => 'hair dyeing',
            'notes' => 'these are notes',
            'id_cd' => 2
		     ));
    }

/*
 * THE FOLLOWING ARE INSERT FUNCTIONS
 */    
    
/* #304
 * Receives data from ed-da-delta-appointments.php
 * and performs an insert into customerDetails table
 * to create a NEW record. The return value is id_cd which is id for Customer Details
 * table, and a foreign key for other tables.
 */
    public function insertCustomerDetailsTable(&$customerDetailsTable)
    {
        global $wpdb;
        $fname = "'".$customerDetailsTable['first_name']."'";
        $lname = "'".$customerDetailsTable['last_name']."'";
        $homePhone = "'".$customerDetailsTable['phone_home']."'";
        $cellPhone = "'".$customerDetailsTable['phone_cell']."'";
        $birthDate = "'".$customerDetailsTable['birth_date']."'";
        
        //$wpdb->insert escapes unwanted characters
        $returnData= $wpdb->insert( 
		$this->table_customerDetails, 
		array( 
			'first_name' => $customerDetailsTable['first_name'], 
            'middle_name' => $customerDetailsTable['middle_name'],
			'last_name' => $customerDetailsTable['last_name'],
            'email' => $customerDetailsTable['email'],
            'phone_home' => $customerDetailsTable['phone_home'],
            'phone_cell' => $customerDetailsTable['phone_cell'],
            'birth_date' => $customerDetailsTable['birth_date'],
            'notes' => $customerDetailsTable['notes']
		     ));       
       
        // Need to use ==== to take into account the data type because if it returns 0 it means
        // zero rows were updated but the query was successful. False means error
        if($returnData === FALSE) { return -1; } // Query was not successful
        else 
        { 
            // Query was successful, now retrieving freshly inserted record and returning id_cd
            // which is primary id for Customer Details table
            $insertedRow = $wpdb->get_results("SELECT id_cd
            FROM $this->table_customerDetails
            WHERE first_name = $fname AND last_name = $lname AND phone_home=$homePhone");
            return $insertedRow[0]->id_cd;       
        }
    }
    
/* #347
 * Receives data from ed-da-delta-appointments.php
 * and performs insertion into Customer Address table
 */
    public function insertCustomerAddressTable(&$customerAddressTable)
    {
        global $wpdb;
        
        //$wpdb->insert escapes unwanted characters
        $returnData= $wpdb->insert( 
		$this->table_customerAddress, 
		array( 
			'country' => $customerAddressTable['country'], 
            'province' => $customerAddressTable['province'],
            'city' => $customerAddressTable['city'],
			'street_name' => $customerAddressTable['street_name'],
            'street_number' => $customerAddressTable['street_number'],
            'apt_number' => $customerAddressTable['apt_number'],
            'postal_code' => $customerAddressTable['postal_code'],
            'id_cd' => $customerAddressTable['id_cd']
		     ));       
       
        // 0 means no rows were updated, False means there was an error. 
        // == false means it could be either.
        // If a distinction needs to be made between 0 and False, use ===
        if($returnData == FALSE) { return -1; } else { return TRUE;}
    
    }

/*
 * The following are SEARCH functions
 */

/* #380
 * Performs search based on various criteria from both Customer Details
 * and Address tables, values supplied by the user
 */
    public function makeSearch(&$customerTable, &$customerAddress)
    {
        global $wpdb;
        
        $queryCustomer = array();
        $queryAddress = array();
        $multiRowQuery = "";

        // Populating $queryCustomer with values from passed in customerTable
        // all keys exist but not all values, so need to extract only those with values
        foreach($customerTable as $key=>$value)
        {
            if( !empty($value) ) { $queryCustomer[$key] = $value; }
        }
        
        foreach($customerAddress as $key=>$value)
        {
            if( !empty($value) ) { $queryAddress[$key] = $value; }
        }
        
        //return count($queryAddress);
        
        // If there are more than one value
        if( count($queryCustomer) >= 1 && count($queryAddress) >= 1  )
        {   
            $k = 0;
            foreach( $queryCustomer as $key=>$value )
            {
                $multiRowQuery .= " AND ".$key." = '".$value."'";
                if($k == 0)
                {
                    // If k is 0 it means it is the first value in the array so $multiRowQuery will be
                    // overwritten with the starting query statment i.e. select from etc.
                    // If there are more keys in the array "AND key=key and value=value" will be added
                    // to multiRowQuery above
                    $value = "'".$value."'";
                    $multiRowQuery = "SELECT *
                                FROM $this->table_customerDetails tc JOIN $this->table_customerAddress ta 
                                ON tc.id_cd = ta.id_cd
                                WHERE $key = $value";
                }
                $k++;
            }
            
            foreach( $queryAddress as $key=>$value )
            {
                $multiRowQuery .= " AND ".$key." = '".$value."'";
            }
            
            $resultRow = $wpdb->get_results($multiRowQuery);
            return $resultRow;
        }
        elseif( count($queryCustomer) >= 1  )
        {   
            $k = 0;
            foreach( $queryCustomer as $key=>$value )
            {
                $multiRowQuery .= " AND ".$key." = '".$value."'";
                if($k == 0)
                {
                    // If k is 0 it means it is the first value in the array so $multiRowQuery will be
                    // overwritten with the starting query statment i.e. select from etc.
                    // If there are more keys in the array "AND key=key and value=value" will be added
                    // to multiRowQuery above
                    $value = "'".$value."'";
                    $multiRowQuery = "SELECT *
                                FROM $this->table_customerDetails
                                WHERE $key = $value";
                }
                $k++;
            }
            
            $resultRow = $wpdb->get_results($multiRowQuery);
            return $resultRow;
        }
        elseif( count($queryAddress) >= 1  )
        {   
            $k = 0;
            foreach( $queryAddress as $key=>$value )
            {
                $multiRowQuery .= " AND ".$key." = '".$value."'";
                if($k == 0)
                {
                    // If k is 0 it means it is the first value in the array so $multiRowQuery will be
                    // overwritten with the starting query statment i.e. select from etc.
                    // If there are more keys in the array "AND key=key and value=value" will be added
                    // to multiRowQuery above
                    $value = "'".$value."'";
                    $multiRowQuery = "SELECT *
                                FROM $this->table_customerDetails tc JOIN $this->table_customerAddress ta 
                                ON tc.id_cd = ta.id_cd
                                WHERE $key = $value";
                }
                $k++;
            }
            
            $resultRow = $wpdb->get_results($multiRowQuery);
            return $resultRow;
        }
        
        
        else { return -1; }
            
    }    

/*
 * These are RETREIEVE functions
 */
    
    public function getFirstLastName($fname, $lname)
    {
        global $wpdb;
        $fname = "'".$fname."'";
        $lname = "'".$lname."'";
        
        return $wpdb->get_results("SELECT first_name, last_name FROM $this->table_customerDetails WHERE first_name = $fname AND last_name=$lname");
    }

/* #502
 * - Gets edit values i.e. existing values (customer details and address) for a record so that it can be edited, added etc
 * - Performs actual update
 */    
    public function workOnCustomerWithAddress(&$customerDetails, &$customerAddress, $actionName)
    {
        global $wpdb;
        $k = 0;
        $countCust = 0;
        $countAddr = 0;
        
        // Retrieving and returning edit values i.e. the values that exist in the db and will be inserted
        // into an edit form at front end for user to see what values exist and which ones
        // need to be updated/added
        if($actionName == 'getEditValues')
        {
            return $wpdb->get_results("SELECT tc.id_cd AS 'id_cd', tc.first_name AS 'First_Name', tc.middle_name AS 'Middle_Name', tc.last_name AS 'Last_Name', tc.email AS 'Email', tc.phone_home AS 'Home_Phone', tc.phone_cell AS 'Cell_Phone', tc.birth_date AS 'Birth_Date', tc.notes AS 'Customer_Notes', tadd.id AS 'id', tadd.country AS 'Country', tadd.province AS 'Province', tadd.city AS 'City', tadd.street_name AS 'Street_Name', tadd.street_number AS 'Street_Number', tadd.apt_number AS 'Apt_Number', tadd.postal_code AS 'Postal_Code' FROM $this->table_customerDetails AS tc LEFT JOIN $this->table_customerAddress AS tadd ON tc.id_cd = tadd.id_cd WHERE tc.id_cd = $customerDetails");
        }
        // If it's fullEdit it means we have incoming values that need to be updated
        elseif ( $actionName == 'fullEdit' ) 
        {
            $whereClause = array("id_cd"=>$customerDetails['id_cd']);
            foreach($customerDetails as $key=>$value)
            {
                trim($value);
                // By setting $k to 0 we're skipping the first value i.e. id_cd which
                // we don't need to count, we need to know if there's more than 1 value in customer details
                // table except id_cd, if count is >=1 then we need to query customer details table
                if($k != 0)
                {
                    if( !empty($value) )
                    { 
                        // If there are any values for customer details table then $localCustomerdetails array will be populated
                        // and serve as data for wpdb->update i.e. data that will be updated as wpdb->update takes
                        // this parameter as an array
                        $localCustomerDetails[$key] = $value;
                        $countCust ++;
                    }
                }
                $k++;
            }
            
            $k = 0;
            
            foreach($customerAddress as $key=>$value)
            {
                trim($value);
                // By setting $k to 0 we're skipping the first value i.e. id_cd which
                // we don't need to count, we need to know if there's more than 1 value in address
                // table except id_cd, if count is >=1 then we need to query address table
                if( $k!=0 )
                {
                    if( !empty($value) )
                    { 
                        // If there are any values for address table then $localCustomerAddress array will be populated
                        // and serve as data for wpdb->update i.e. data that will be updated as wpdb->update takes
                        // this parameter as an array
                        $localCustomerAddress[$key] = $value;
                        $countAddr++;
                    }
                }
                $k++;
            }
            
            $fname_lname = $wpdb->get_results("SELECT first_name, last_name FROM $this->table_customerDetails WHERE id_cd='".$customerDetails['id_cd']."'");
      
            if( $countCust && !$countAddr )
            {
                //update customer details table only as !$countAddr means no values for address table
                $successMsg = $wpdb->update($this->table_customerDetails, $localCustomerDetails, $whereClause);
                switch($successMsg)
                {
                    case FALSE: return 'Error occurred, please try again or contact administrator';
                    break;
                
                    case 0: return 'No records have been updated.';
                    break;
                
                    default: return "Records for ".$fname_lname[0]->first_name." ".$fname_lname[0]->last_name." have been updated.";
                }
            }
            elseif( !$countCust && $countAddr )
            {
                //update address table only as !$countCust means no values for customer details table
                $successMsg = $wpdb->update($this->table_customerAddress, $localCustomerAddress, $whereClause);
                switch($successMsg)
                {
                    case FALSE: return 'Error occurred, please try again or contact administrator';
                    break;
                
                    case 0: return 'No records have been updated.';
                    break;
                
                    default: return "Records for ".$fname_lname[0]->first_name." ".$fname_lname[0]->last_name." have been updated.";
                }
            }
            elseif( $countCust && $countAddr )
            {
                // updating both customer details and address tables
                $successMsg_1 = $wpdb->update($this->table_customerDetails, $localCustomerDetails, $whereClause);
                $successMsg_2 = $wpdb->update($this->table_customerAddress, $localCustomerAddress, $whereClause);
                
                if($successMsg_1 || $successMsg_2)
                {
                    return "Records for ".$fname_lname[0]->first_name." ".$fname_lname[0]->last_name." have been updated.";
                }
                else { return "No records have been updated or errors occurred. Please try again."; }
            }
        }
                   
        
    }

/* #615
 * Inserting data into appointment or payment table
 */    
     public function makeApptandPay($customerId, &$tableDetails, $actionName)
    {
        global $wpdb;
        $tableName = "";
        $dataArr = array();
        
        // the following data items will be identical whether it is
        // an appointment or payment. Those items that are different will be populated below
        $dataArr['date'] = $tableDetails['date'];
        $dataArr['purpose'] = $tableDetails['purpose'];
        $dataArr['notes'] = $tableDetails['notes'];
        $dataArr['id_cd'] = $customerId;
        
        if($actionName == "makeAppt")
        {
            $dataArr['time'] = $tableDetails['time'];
            $dataArr['venue'] = $tableDetails['venue'];
            $tableName = $this->table_appointments;
        }
        elseif ($actionName == "enterPay")
        {
            $dataArr['amount'] = $tableDetails['amount'];
            $tableName = $this->table_payments;
        }
        
        $returnData= $wpdb->insert($tableName, $dataArr);       

        /* 
         * Need to use ==== to take into account data type because if it returns 0 it means
         *  zero rows were updated but the query was successful.
        */
        return $returnData;

    }

/* #653
 * Retrieves data from all tables pertaining to a certain record
 * Used to display all data
 * 
 * Also retrieves appointment times for a certain record on a certain day.
 * These times are used to determine whether there is a conflict
 */    
    public function displayInfoModels($customerId, $actionName)
    {
        global $wpdb;
        
        $customerId = "'".$customerId."'";
        
        if($actionName == 'retrieveAllApptTimes') //Retrieve all appointment times
        {
            return $wpdb->get_results("SELECT time, date FROM $this->table_appointments WHERE id_cd = $customerId");
        }
        else //Retrieve all information for a specific record
        {
        $customerDetailsAddress = $wpdb->get_results("SELECT tc.id_cd AS 'id_cd', tc.first_name AS 'First_Name', tc.middle_name AS 'Middle_Name', tc.phone_home AS 'Home_Phone', tc.phone_cell AS 'Cell_Phone', tc.birth_date AS 'Birth_Date', tc.notes AS 'Customer_Notes', tc.last_name AS 'Last_Name', CONCAT(IF(tad.apt_number IS NULL OR tad.apt_number = '','',CONCAT('Apt ',tad.apt_number,', ')), IF(tad.street_number IS NULL OR tad.street_number = '','',CONCAT(tad.street_number,' ')), IF(tad.street_name IS NULL OR tad.street_name = '','', CONCAT(tad.street_name,', ')), IF(tad.city IS NULL OR tad.city = '','', CONCAT(tad.city,', ')), IF(tad.province IS NULL OR tad.province ='','', CONCAT(tad.province,', ')), IF(tad.postal_code IS NULL OR tad.postal_code = '','', CONCAT(tad.postal_code,', ')), IF(tad.country IS NULL OR tad.country = '','',tad.country)) AS 'Address' FROM $this->table_customerDetails AS tc LEFT JOIN $this->table_customerAddress AS tad ON tc.id_cd = tad.id_cd WHERE tc.id_cd = $customerId");
        
        $appointments = $wpdb->get_results("SELECT ta.id_cd AS 'taid_cd', ta.date AS 'Appointment_Date', ta.time AS 'Appointment_Time', ta.purpose AS 'Appointment_Purpose', ta.venue AS 'Appointment_Venue', ta.notes AS 'Appointment_Notes' FROM $this->table_appointments AS ta WHERE ta.id_cd = $customerId");
        
        $payments = $wpdb->get_results("SELECT tp.id_cd AS 'tpid_cd', tp.date AS'Payment_Date', tp.amount AS'Payment_Amount', tp.purpose AS 'Payment_Purpose', tp.notes AS 'Payment_Notes' FROM $this->table_payments AS tp WHERE id_cd = $customerId");
        
        $customerDetailsAddress[] = $appointments;
        $customerDetailsAddress[] = $payments; 
        return $customerDetailsAddress;
        }
        
    }
    

/*
 * These are ALTER, DELETE functions
 */

/* #690
 * Deletes an appointment based on id_cd, date and time 
 */
    public function deleteAppt($id_cd, $actionName, $date, $time)
    {
        global $wpdb;
        $where = array("id_cd"=>$id_cd, "date"=>$date, "time"=>$time);
        return $wpdb->delete( $this->table_appointments, $where );
    }

/* $700
 * Deletes a payment record based on id_cd, date and amount 
 */
    public function deletePay($id_cd, $date, $amount)
    {
        global $wpdb;
        $where = array("id_cd"=>$id_cd, "date"=>$date, "amount"=>$amount);
        return $wpdb->delete( $this->table_payments, $where );
    }
    
/* #710
 * Deletes a record
 */    
    public function deleteRecords($id_cd)
    {
        global $wpdb;
        $where = array("id_cd"=>$id_cd);
        //$age = array("Peter"=>"35", "Ben"=>"37", "Joe"=>"43");
        $wpdb->delete( $this->table_customerAddress, $where );
        $wpdb->delete( $this->table_appointments, $where );
        $wpdb->delete( $this->table_payments, $where );
        return $wpdb->delete( $this->table_customerDetails, $where );
    }
    
/*
 * These are DROP functions. The names are self explanatory
 */
    public function dropTabsTable()
    {
       global $wpdb;
        
        $sql = "DROP TABLE IF EXISTS $this->table_tabs";

        $wpdb->query($sql);
    }
    
    public function dropCustomerDetailsTable()
    {
        global $wpdb;
        
        $sql = "DROP TABLE IF EXISTS $this->table_customerDetails";

        $wpdb->query($sql);
    }

    public function dropCustomerAddressTable()
    {
        global $wpdb;
        
        $sql = "DROP TABLE IF EXISTS $this->table_customerAddress";

        $wpdb->query($sql);
    }
    
    public function dropAppointmentsTable()
    {
        global $wpdb;
        
        $sql = "DROP TABLE IF EXISTS $this->table_appointments";

        $wpdb->query($sql);
    }
    
    public function dropPaymentsTable()
    {
        global $wpdb;
        
        $sql = "DROP TABLE IF EXISTS $this->table_payments";

        $wpdb->query($sql);
    }   

    
} // End of class ManipulateTables