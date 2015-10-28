/* 
 * Basic validation.
 * Not performing full validation i.e. not making sure there are chararcters before @ in email, or 
 * that a dot doesn't follow @ immediately after etc. 
 * It is user's responsibility to add correct information.
 * A pop up window will ask for confirmation of data being submitted as a final confirmation.
 * Basically functions make sure no illegal characters are sent i.e. pre-sanitization.
 * Additional sanitization is performed on the server side.
 */

function validateMe(text, id, errMsg, mandatory, countErr, descr)
{
    var reg;
    
    switch(descr)
    {
        // Alphabetic chars only, a - alphabetic
        case "a": reg = /[\d`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        break;
        
        // Allows numeric chars only, n - numeric
        case "n": reg = /\D/gi;
        break;
        
        // Allows alphanumeric chars only, an - alpha numeric
        case "an": reg = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
        break;
        
        // Allows dashes and numeric only e.g. street number 32-1, dn - dashes numeric
        case "dn": reg = /[^0-9\-]/g;
        break;
        
        // Allows dashes and alphanumeric chars e.g. street name 34th street, Meine-Liebe Strasse
        // dan - dashes alpha numeric
        case "dan": reg = /[`~!@#$%^&*()_|+=?;:'",.<>\{\}\[\]\\\/]/gi;
        break;
    }
    
    text = text.trim();
    
    if(mandatory)
    {
        if (text == "" || reg.test(text)) { alert(errMsg); jQuery(id).css({"border-color":"#FF6F6F"}); countErr['count'] = 1; }
        else { jQuery(id).css({"border-color":"#C3C3C3"}); }
    }
    else
    {
        if (reg.test(text)) { alert(errMsg); jQuery(id).css({"border-color":"#FF6F6F"}); countErr['count'] = 1; }
        else { jQuery(id).css({"border-color":"#C3C3C3"}); }
    }
        
}

function validateEmail(email, id, alertMsg, mandatory, countErr)
{
    var re = /[`~!#$%^&*()|+=?;:'",<>\{\}\[\]\\\/]/gi;
    // Matches @ char, match() returns an array, if the length is not 1 then either @ is missing or there are more than 1
    var monkeyChar = email.match(/@/g) == null ? 0 : email.match(/@/g).length ;
    
    if(mandatory)
    {
        if (email == "" || re.test(email) || monkeyChar != 1 ) { alert(alertMsg); jQuery(id).css({"border-color":"#FF6F6F"}); countErr['count'] = 1; }
        else { jQuery(id).css({"border-color":"#C3C3C3"}); }
    }
    else
    {
        if(email != "")
        {
            if (re.test(email) || monkeyChar != 1) { alert(alertMsg); jQuery(id).css({"border-color":"#FF6F6F"}); countErr['count'] = 1; }
            else { jQuery(id).css({"border-color":"#C3C3C3"}); }
        }
    }
}