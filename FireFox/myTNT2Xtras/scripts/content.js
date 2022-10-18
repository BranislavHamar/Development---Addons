////////////////////////////////////////////////////////////////////////////////
// 0 CONFIGURATION
////////////////////////////////////////////////////////////////////////////////
        
    var debug = "No";
    var akey;

////////////////////////////////////////////////////////////////////////////////
// 1 PREREQUISITIES
//////////////////////////////////////////////////////////////////////////////// 
                    
            // define cookie
            function getCookie(name) {
              var value = "; " + document.cookie;
              var parts = value.split("; " + name + "=");
              if (parts.length == 2) return parts.pop().split(";").shift();
            }
            
            // enter id & pw
            while (!getCookie("myTNT2Xtras")) 
            {
               akey = prompt("Please enter your 'MYTNT2Xtras' key:", "");
                while ( akey.search("@") == -1 || akey.search(".") == -1 || akey.search(":") == -1) 
                {
                  akey = prompt("Please enter correct format of 'MYTNT2Xtras' key:", "email:password");
                }
               var akey_split = akey.split(":");
               akey = akey_split[0] + ":" + MD5(akey_split[1]);
               akey = window.btoa(akey);
               document.cookie = "myTNT2Xtras="+akey+"; expires=Tue, 19 Jan 2038 03:14:07 UTC;";             
            } 
            
            // get id & pw from cookie
            akey = getCookie("myTNT2Xtras");

////////////////////////////////////////////////////////////////////////////////
// 2 LOAD CONTENT FILE
////////////////////////////////////////////////////////////////////////////////

    debug == "Yes" ? console.log("**CONTENT** > File content.js: STARTED ") : "";
        
                if (akey !== "") {
                      debug == "Yes" ? console.log("**CONTENT** > Javascript Download: STARTED ") : "";
                                      
                                      var xhttp = new XMLHttpRequest();
                                      xhttp.open("POST", "https://xxxxxxxxx/myTNT2Xtras/index.php", true);
                                      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                      xhttp.send("app=code&akey=" + akey);

                                      xhttp.onreadystatechange = function() {
                                        if (xhttp.readyState == 4 && xhttp.status == 200) {
                                          var oScriptText = document.createTextNode(xhttp.responseText);
                                          var oScript = document.createElement("script");
                                          oScript.appendChild(oScriptText);
                                          document.body.appendChild(oScript); 
                                        }
                                      };
            
                      debug == "Yes" ? console.log("**CONTENT** > Javascript Download: ENDED ") : "";
                 };

    debug == "Yes" ? console.log("**CONTENT** > File content.js: ENDED ") : "";



     
  
 
          