          
          
          
          let user_type="Consumer";
          const justforinputusertype=document.getElementById("input_for_usertype");

          const form = document.getElementById("loginform");
          justforinputusertype.value=user_type;

          function switch_user() {
            if(user_type=="Consumer"){
              user_type="Restaurant";
              document.getElementById("switch_the_user").innerHTML = " User: Restaurant";
              justforinputusertype.value=user_type;
            }
            else{
              user_type="Consumer";
              document.getElementById("switch_the_user").innerHTML =" User: Consumer";
              justforinputusertype.value=user_type;
            }
          }

         
            

         