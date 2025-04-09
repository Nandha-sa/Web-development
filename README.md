This is the simple project of sign-in and sign-up form

Tech stach used : HTML, CSS,PHP, MySQL, MongoDB, jQuery AJAX, Redis, Bootstrap

* After first successful login a token will be generated and stored in Redis and the local storage of the browser

* On every step of navigation, this tokens will be validated and if it fails, the user will be redirected to the login page

* These tokens are used for internal authentication

* In this project, the expiry for the token is set to 60 mins (1 hour) for the demo purpose
  
Key Features:
*  Secure Register > Login > Profile Management system
*  MySQL for user authentication & MongoDB for profile storage
*  Session management using Redis & LocalStorage
*  AJAX-based interactions (jQuery) for seamless UX
