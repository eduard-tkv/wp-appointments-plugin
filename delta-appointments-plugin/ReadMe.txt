THIS IS THE FREEWARE VERSION OF DELTA APPOINTMENTS

1. CROSS REFERENCING
This is how various pieces of code are cross-referenced. Any function will have a number matching 
the code line number indicated in the preceding comments e.g. 77. A reference to a number is made
using the file name, the function name and the number e.g. models.php #223. The function name should be
used whenever possible. In jQuery this is bit problematic as it could be something like 
jQuery( document ).on( "click", "#buttonAddCustomer", function() - since they are anonymous functions.
Here is the id name could be used instead of the function name.

Note that the reference number can and will change depending on the alterations made to the code, and
hopefully they will be not too far from each other i.e. the code line number and the reference number.
Whenever a mismatch noticed, an update is necessary.

A sample comment featuring its own reference number and the numbers in other files.
/* #77
 * Add new customer/record. Receives data from delta-appointments-plugin.js #16
 * and calls models.php::insertCustomerAddressTable #223
 * to insert a new row in customerDetails table
 */