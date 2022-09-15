// JS used to show or not the nav menu when user click on his profil picture on the navbar
// I get the class .user_nav_logo after i listen the click on this class and the function HandleUserClick start at click and if the display of the aside class
//is flex the function change that to none and if u click again the display will change again to flex and etc.


const user = document.querySelector(".user_nav_logo");

user.addEventListener('click',handleUserClick);

function handleUserClick(){
    
        var e = document.querySelector('.aside').style.display;
    
        if (e == "flex" ) 
        {
            document.querySelector('.aside').style.display = "none";
           
        }
        else 
        {
           
            document.querySelector('.aside').style.display = "flex";
           
        }
    }

