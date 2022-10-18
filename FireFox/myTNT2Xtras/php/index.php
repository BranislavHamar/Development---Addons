<?php
////////////////////////////////////////////////////////////////////////////////
// 0. DESCRIPTION
////////////////////////////////////////////////////////////////////////////////
//
//    A. content.js - akey & xhttp
//    B. index.php - download & upload
//      1. CONFIGURATION
//      2. LOAD SPECIFIC CONTENT
//      3. UPLOAD SPECIFIC CONTENT
//
////////////////////////////////////////////////////////////////////////////////
// 1. CONFIGURATION
////////////////////////////////////////////////////////////////////////////////

     // 0. Define variables
     //////////////////////  
      $debug                  = "Yes";
      
      $country_profile[0]     = "1"; //allow_chat
      $country_profile[1]     = "1"; //allow_quotes_get
      $country_profile[2]     = "1"; //allow_quotes_like
      $country_profile[3]     = "1"; //allow_messages
                  
      $dbhost                 = 'xxxxx';
      $dbuser                 = 'yyyyy';
      $dbpass                 = 'zzzzz';
      $dbname                 = 'uuuuu';
      
      $app                    = sanitizeString($_POST["app"]);
      $akey                   = sanitizeString($_POST["akey"]);
      $content                = urldecode(sanitizeString($_POST["content"]));
      $akey_decoded           = base64_decode($akey);        
      $akey_split             = explode(":", $akey_decoded); //email:password
     
     // 1. Clean variables
     //////////////////////       
      function sanitizeString($var)
      {
          $var = stripslashes($var);
          $var = strip_tags($var);
          $var = htmlentities($var);
          return $var;
      }
     
     // 2. Setup origin
     //////////////////////           
      header("Access-Control-Allow-Origin: https://mytnt.tnt.com");
      header("Vary: Origin");
      header("Access-Control-Max-Age: 1000");
      header("Access-Control-Allow-Methods: POST");

     // 3. Basic check
     //////////////////////     
        if(!$akey || !$app) 
        {
            die("Hey! Hotlinking not permitted!");
        }

     // 4. Create connection
     //////////////////////     
       $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  
       if (!$conn) {
              die("Connection failed: " . mysqli_connect_error());
       }
       mysqli_set_charset($conn,"utf8");
                                                        
////////////////////////////////////////////////////////////////////////////////
// 2. LOAD SPECIFIC CONTENT
////////////////////////////////////////////////////////////////////////////////

        if ($app == "code") {

                //  PHP ** User profile read **
                ///////////////////////////////////
                      
                      $sql = "SELECT allow_chat, allow_quotes_get, allow_quotes_like, allow_messages 
                              FROM MYTNT2_PROFILE 
                              WHERE email = '".$akey_split[0]."' AND akey = '".$akey_split[1]."' ";
                      $result = mysqli_query($conn, $sql);
                      
                      if (mysqli_num_rows($result) > 0) {
                          // output data of each row
                          while($row = mysqli_fetch_assoc($result)) {
                              $user_profile=array($row["allow_chat"],$row["allow_quotes_get"],$row["allow_quotes_like"],$row["allow_messages"]);
                          }
                      } else {
                            $user_profile=array("0","0","0","0");
                      }

                      //  PHP ** User profile update **
                      ///////////////////////////////////
                            
                            $sql = "UPDATE MYTNT2_PROFILE SET last_access = '" . date("YmdHis") . "'  
                                    WHERE email = '".$akey_split[0]."' AND akey = '".$akey_split[1]."' ";
                            $result = mysqli_query($conn, $sql);
       
                //  Javascript ** Chat **
                ///////////////////////////////////
                
                      If ($user_profile[0]=="1" AND $country_profile[0]=="1") {
                        
                        if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Chat STARTED ');";};
                        
                            echo " 
                        						(function() { 
                        							livechatooCmd = function() { livechatoo.embed.init({account : 'tnt', lang : 'sk', side : 'left'}) }; 
                        							var l = document.createElement('script'); l.type = 'text/javascript'; l.async = !0; 
                        							l.src = 'http' + (document.location.protocol == 'https:' ? 's' : '') + '://app.livechatoo.com/js/web.min.js'; 
                        							var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(l, s); 
                        						})(); 
                                 ";
                        
                        if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Chat ENDED ');";};
                        
                       }

                //  Javascript ** Quote Monitor ** 
                /////////////////////////////////////

                      If ($user_profile[1]=="1" AND $country_profile[1]=="1") {
      
                               if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Quote Get STARTED ');";};
                               
                               echo "
                                      function QuoteElementArray(tag) {
                                                    var myStringArray = document.getElementsByTagName(tag);
                                                    var arrayLength = myStringArray.length;
                                                    var name;
                                                      for (var i = 0; i < arrayLength; i++) {
                                                          if  (myStringArray[i].getAttribute('id')) { name = myStringArray[i].getAttribute('id'); };
                                                          if  (myStringArray[i].getAttribute('name')) { name = myStringArray[i].getAttribute('name'); };
                                                          QuoteString += '| [' + tag + '][' + i + '][' + name + ']---' + myStringArray[i].value + ' ';
                                                      }
                                      };
                                  
                                        
                                        
                                      if (document.getElementById('\$ctrl.quickCalculatorForm')) {
                                            document.getElementById('\$ctrl.quickCalculatorForm').onsubmit = function() {
                                                
                                                  QuoteString = '';
                                                  QuoteElementArray('input');                        
                                                  QuoteElementArray('select');
                                                          ".($debug == "Yes" ? "console.log('**CONTENT** '+new Date()+' >>> MYTNT2-VIP-QUOTE-INFO: ' + QuoteString);" : " ")."
                                                         
                                                          var xhttp2 = new XMLHttpRequest();
                                                          xhttp2.open('POST', 'https://xxxxxxxxx/myTNT2Xtras/index.php', true);
                                                          xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                                                          xhttp2.send('app=quote&akey=" . $akey . "&content=' + QuoteString);
      
                                                          xhttp2.onreadystatechange = function() {
                                                            if (xhttp2.readyState == 4 && xhttp2.status == 200) {
                                                                ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >>> MYTNT2-VIP-QUOTE-UPLOADED: ' + xhttp2.responseText); " : " ")."
                                                            }
                                                          };
                                                  QuoteString = '';
                                          }
                                      }; 
                                      ";      
                      
                                if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Quote Get ENDED ');";};
                                                                 
                      }                     


                //  Javascript ** Quote Like ** 
                ////////////////////////////////////
                          
                    If ($user_profile[2]=="1" AND $country_profile[2]=="1") {

                              if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Quote Like STARTED ');";};
                          
                              echo "       
                                      var Like;
                                      var getLike;
                                      var List;

                                      function getLike(Like,List) 
                                      {
                                                                            QuoteString = '';
                                                                            QuoteElementArray('input');                        
                                                                            QuoteElementArray('select');
                                                                            QuoteString = QuoteString + '| [service]['+ List.substr(List.length-1) + '][content] --- ' + Like;
                                                                                    ".($debug == "Yes" ? "console.log('**CONTENT** '+new Date()+' >>> MYTNT2-VIP-QUOTELIKE-INFO: ' + QuoteString);" : " ")."
                                                                                    
                                                                                    var xhttp3 = new XMLHttpRequest();
                                                                                    xhttp3.open('POST', 'https://xxxxxxxxx/myTNT2Xtras/index.php', true);
                                                                                    xhttp3.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                                                                                    xhttp3.send('app=quotelike&akey=" . $akey . "&content=' + QuoteString);
                                
                                                                                    xhttp3.onreadystatechange = function() {
                                                                                      if (xhttp3.readyState == 4 && xhttp3.status == 200) {
                                                                                          ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >>> MYTNT2-VIP-QUOTELIKE-UPLOADED: ' + xhttp3.responseText); " : " ")."
                                                                                           
                                                                                      }
                                                                                    };
                                                                            QuoteString = '';
                                                                            
                                                                            document.getElementById('Like'+List.substr(List.length-1)).innerHTML = 'Ďakujeme za Vašu odpoveď!';
                                      
                                      };

                              


                               var checkCalculatorServiceListAvailable = 'no';
                               
                                function checkCalculatorServiceList()
                                {
                                    if (document.getElementsByTagName('quick-calculator-quote')[0] && checkCalculatorServiceListAvailable == 'no') {
                                      ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >> QUOTELIKE Dynamic - Im inserting because - '+checkCalculatorServiceListAvailable);" : " ")."
                                                                                                                                                                                                                                                                                                                  
                                                          var objLike;
                                                          var objCalc;
                                                          var LikeQ; 
                                                          var LikeY;
                                                          var LikeN; 
                               
                                                          for (i = 0; i < document.getElementsByTagName('quick-calculator-quote').length; i++) {
                                                             
                                                             LikeQ = '<div style=\"width:70%;float:left;padding:5px;text-align:right;\">Páči sa Vám táto služba?</div><div id=\"Like' + i + '\" style=\"padding:5px;font-size:13px;color:#ff6600;width:30%;float:left;border-style:solid;border-width:1px;border-color:#DFDFDF;text-align:center;background-color:white;\">';
                                                             LikeY = '<div id=\"LikeYes' + i + '\" style=\"width:50%;float:left;\"><a href=\"javascript:getLike(document.getElementsByClassName(\'service-all\')[' + i + '].innerText+\'|[like][answer][0]---Yes\',\'LikeYes' + i + '\');\"><img style=\"height:15px;\" src=\"https://xxxxxxxxxxxxxxxxx/myTNT2Xtras/images/thumb_up.svg\"> Áno</a></div>';
                                                             LikeN = '<div id=\"LikeNo'  + i + '\" style=\"width:50%;float:left;\"><a href=\"javascript:getLike(document.getElementsByClassName(\'service-all\')[' + i + '].innerText+\'|[like][answer][0]---No\',\'LikeNo' + i + '\');\"><img style=\"height:15px;\" src=\"https://xxxxxxxxxxxxxxxxx/myTNT2Xtras/images/thumb_down.svg\"> Nie</a></div>';                                         
                                                             
                                                             objLike = angular.element('<div style=\"font-size:13px;margin-bottom:10px;float:right;width:100%;\">' +
                                                                                            LikeQ +
                                                                                                    LikeY +
                                                                                                    LikeN +
                                                                                            '</div>' + 
                                                                                       '</div>');
                                                                                objCalc = angular.element(document).find('quick-calculator-quote').eq(i);
                                                                                objCalc.append(objLike);
                                                          } 
                                                                                       
                                      
                                      checkCalculatorServiceListAvailable = 'yes';
                            
                                    }
                                    
                                    if (!document.getElementsByTagName('quick-calculator-quote')[0]) {
                                      checkCalculatorServiceListAvailable = 'no'; 
                                      ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >> QUOTELIKE Dynamic - Im waiting because - '+checkCalculatorServiceListAvailable);" : " ")." 
                            
                                    }
                                    
                                      setTimeout(checkCalculatorServiceList, 1000);
                                    
                                    
                                    ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >> QUOTELIKE Dynamic - Im waiting and again because - '+checkCalculatorServiceListAvailable);" : " ")."
                                }
                            
                            
                            
                                      setTimeout(checkCalculatorServiceList, 1000);

                              ";
                  
                              if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Quote Like ENDED ');";};
                  
                    }                        

                //  Javascript ** Message Bar **
                //////////////////////////////////

                    If ($user_profile[3]=="1" AND $country_profile[3]=="1") {                                         

                           if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Message Bar STARTED ');";};

                           echo "var checkCalculatorAvailable = 'no';
                               
                                function checkCalculator()
                                {
                                    if (document.getElementsByTagName('quick-calculator')[0] && checkCalculatorAvailable == 'no') {
                                 ";
                                                if ($debug == "Yes") {echo " console.log('**CONTENT** '+new Date()+' MESSAGEBAR Dynamic - Im inserting because - ' + checkCalculatorAvailable);";};
            
                                                $sql = "(SELECT aa.message_text as MSG_TXT, aa.message_date AS MSG_DATE, aa.message_priority AS MSG_PRIO
                                                            FROM MYTNT2_MESSAGES_SPECIFIC AS aa, MYTNT2_PROFILE AS cc
                                                            WHERE cc.email = '".$akey_split[0]."'
                                                            AND cc.account = aa.message_account
                                                            AND aa.message_expires >= DATE_FORMAT( CURDATE( ) , '%Y%m%d' )
                                                        )
                                                        UNION
                                                        (SELECT b.message_text as MSG_TXT, a.message_date AS MSG_DATE, a.message_priority AS MSG_PRIO
                                                            FROM MYTNT2_MESSAGES_STANDARD AS a, MYTNT2_MESSAGES_CODES AS b, MYTNT2_PROFILE AS c
                                                            WHERE c.email = '".$akey_split[0]."'
                                                            AND c.account = a.message_account
                                                            AND a.message_code = b.message_code
                                                            AND a.message_expires >= DATE_FORMAT( CURDATE( ) , '%Y%m%d' )
                                                        )
                                                        ORDER BY MSG_DATE DESC
                                                
                                                ";
                                                $result = mysqli_query($conn, $sql);
                                                
                                                if (mysqli_num_rows($result) > 0) 
                                                      {
                                                      
                                                      echo "var objWidget;
                                                            var objCalc2;
                                                            
                                                            objWidget = angular.element('<div id=\"messagebar\" style=\"padding-top:8px;padding-bottom:8px;background-color:#5B656C;width:100%;\">' +
                                                            ";
                                                                $i = 0;
                                                                // output data of each row
                                                                while($row = mysqli_fetch_assoc($result)) {
                                                                    
                                                                    $message_date = date_create_from_format('Ymd', $row["MSG_DATE"]);
                                                                    $message_date = date_format($message_date, 'd.m.Y');
                                                                    
                                                                        echo "'<div id=\"messagebar_row".$i."\" style=\"font-size:13px;padding-left:25px;padding-top:2px;padding-bottom:2px;padding-right:25px;text-align:left;margin:0 auto;width:90%;margin-top:4px;max-width:1150px;background-color:#FF6600;color:white;\">' +
                                                                                                  '".($row["MSG_PRIO"] == "1" ? "<b>>>" : ">>")." " . $message_date ." - ".$row["MSG_TXT"]."".($row["MSG_PRIO"] == "1" ? "</b>" : "")."' +
                                                                              '</div>' + ";
                                                                     $i++;
                                                                };
                                                          
                                                      echo "'</div>');
                                                            objCalc2 = angular.element(document).find('quick-calculator').eq(0);
                                                            objCalc2.append(objWidget); 
                                                                               
                                                            document.getElementsByClassName('expansion-panel__button')[0].onclick = function(){document.getElementById('messagebar').style.display='none';};
                                                           ";
                                                      }; 
                                   
                            echo "checkCalculatorAvailable = 'yes';
                            
                            }
                                    
                                    if (!document.getElementsByTagName('quick-calculator')[0]) {
                                      checkCalculatorAvailable = 'no'; 
                                      ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >> MESSAGEBAR Dynamic - Im waiting because - ' + checkCalculatorAvailable);" : " ")."  
                            
                                    }
                                    
                                      setTimeout(checkCalculator, 1000);
                                    
                                    ".($debug == "Yes" ? " console.log('**CONTENT** '+new Date()+' >> MESSAGEBAR Dynamic - Im waiting again because - ' + checkCalculatorAvailable);" : " ")."
                                }
                            
                            
                            
                          setTimeout(checkCalculator, 1000);
                          ";                 
                           if ($debug == "Yes") {echo " console.log('**CONTENT** ".date("d.m.Y H:i:s")." >> Message Bar ENDED ');";};                        

                      }
                  
 

        }

////////////////////////////////////////////////////////////////////////////////
// 3. UPLOAD SPECIFIC CONTENT
////////////////////////////////////////////////////////////////////////////////    

        //  PHP ** quotes **
        ///////////////////////////////
              if ($app == "quote") {
            
                      if ($debug == "Yes") {echo " 
                        **CONTENT** ".date("d.m.Y H:i:s")." >> APP -> " . $app ." EMAIL ID -> " . $akey_split[0]. " akey -> " . $akey_split[1] . "\n
                        **CONTENT** ".date("d.m.Y H:i:s")." >> " . $content . " <<< \n
                        **CONTENT** ".date("d.m.Y H:i:s")." >> UPLOAD STARTED <<< \n
                      ";};   
            
                             $sql = "INSERT INTO MYTNT2_QUOTES_CALC (timestamp,email,content)
                                     VALUES ('".date("YmdHis")."', '".$akey_split[0]."', '".$content."')";
                                
                             $result = mysqli_query($conn, $sql);
                             
                             if(! $result ) {
                                die('Could not enter data: ' . mysql_error());
                             }
                             
                             
                      if ($debug == "Yes") {echo " **CONTENT** ".date("d.m.Y H:i:s")." >> ".date("d.m.Y H:i:s")." >>> UPLOAD " . $app ." FINISHED SUCCESSFULY<<< ";};        
                     
              }
      
        //  PHP ** quotes **  like
        ///////////////////////////////
        
              if ($app == "quotelike") {
            
                      if ($debug == "Yes") {echo " 
                        **CONTENT** ".date("d.m.Y H:i:s")." >> APP -> " . $app ." EMAIL ID -> " . $akey_split[0]. " akey -> " . $akey_split[1] . "\n
                        **CONTENT** ".date("d.m.Y H:i:s")." >> " . $content . " <<< \n
                        **CONTENT** ".date("d.m.Y H:i:s")." >> UPLOAD STARTED <<< \n
                      ";};
                            
                             $sql = "INSERT INTO MYTNT2_QUOTES_CALCLIKE (timestamp,email,content)
                                     VALUES ('".date("YmdHis")."','".$akey_split[0]."', '".$content."')";
                                
                             $result = mysqli_query($conn, $sql);
                             
                             if(! $result ) {
                                die('Could not enter data: ' . mysql_error());
                             }
                             
                      if ($debug == "Yes") {echo " **CONTENT** ".date("d.m.Y H:i:s")." >> ".date("d.m.Y H:i:s")." >>> UPLOAD " . $app ." FINISHED SUCCESSFULY<<< ";};         
              }
  
////////////////////////////////////////////////////////////////////////////////
//  1. CONFIGURATION
////////////////////////////////////////////////////////////////////////////////

     // Close Connection
     //////////////////////
                           
      mysqli_close($conn);
      
?>