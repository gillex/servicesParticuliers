<?php
class Auth{
	static function isLogged(){
		if(isset($_SESSION['Auth']['id']) && isset($_SESSION['Auth']['email']) && isset($_SESSION['Auth']['login_string']) && isset($_SESSION['Auth']['role']))
			return true;
		else
			return false;
	}
	static function isAdmin(){
        if(isset($_SESSION['Auth']['id']) && isset($_SESSION['Auth']['email']) && isset($_SESSION['Auth']['login_string']) && isset($_SESSION['Auth']['role'])){
			if($_SESSION['Auth']['role']==2) {
				return true;
			}
			else{
				return false;
			}
		}
	}
	static function isMember(){
        if(isset($_SESSION['Auth']['id']) && isset($_SESSION['Auth']['email']) && isset($_SESSION['Auth']['login_string']) && isset($_SESSION['Auth']['role'])){
			if($_SESSION['Auth']['role']==1){
				return true;
			}
			else{
				return false;
			}
		}
	}
}