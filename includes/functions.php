<?php

function sec_session_start() {
    $session_name = 'sec_session_id';   // Attribue un nom de session
    $secure = SECURE;
    // Cette variable empêche Javascript d’accéder à l’id de session
    $httponly = true;
    // Force la session à n’utiliser que les cookies
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Récupère les paramètres actuels de cookies
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"], 
        $cookieParams["domain"], 
        $secure,
        $httponly);
    // Donne à la session le nom configuré plus haut
    session_name($session_name);
    session_start();            // Démarre la session PHP 
    session_regenerate_id();    // Génère une nouvelle session et efface la précédente
}

/**
 * Verification de brute force
 *
 * @param String $user_id -> ID de l'utilisation
 * @param PDO $pdo -> objet PDO
 * @return true/false;
 */
function checkbrute($user_id) {
    $pdo = SPDO::getInstance();
    // Récupère le timestamp actuel
    $now = time();
 
    // Tous les essais de connexion sont répertoriés pour les 2 dernières heures
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $pdo->prepare("SELECT count(*) 
                             FROM hs_login_attempts
                             WHERE u_id = ? 
                            AND la_time > '$valid_attempts'")) {
        $stmt->execute(array($user_id));
 
        $row = $stmt->fetch(PDO::FETCH_BOTH);
				
        // S’il y a eu plus de 5 essais de connexion 
        if ($row[0] > 5) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Verification de la session en cours
 *
 * @return true/false;
 */
function login_check() {	
	$pdo = SPDO::getInstance();
    // Vérifie que toutes les variables de session sont mises en place
    if (isset($_SESSION['Auth']['id'], $_SESSION['Auth']['username'],$_SESSION['Auth']['login_string'])) 
	{
        $user_id = $_SESSION['Auth']['id'];
        $login_string = $_SESSION['Auth']['username'];
        $username = $_SESSION['Auth']['login_string'];
 
        // Récupère la chaîne user-agent de l’utilisateur
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $pdo->prepare("SELECT u_password FROM hs_users WHERE u_id = ? LIMIT 1")) 
		{
            if ($stmt->execute(array($user_id)))
			{
				$row = $stmt->fetch(PDO::FETCH_BOTH);

				if ($row) 
				{
					$password = $row[0];
					$login_check = hash('sha512', $password . $user_browser);
	 
					if ($login_check == $login_string) {
						// Connecté!!!! 
						return true;
					} else {
						// Pas connecté 
						return false;
					}
				} else {
					// Pas connecté 
					return false;
				}
			} else {
				// Pas connecté 
				return false;
			}
        } else {
            // Pas connecté 
            return false;
        }
    } else {
        // Pas connecté 
        return false;
    }
}

/**
 * Nettoyage de la vairable PHP_SELF
 *
 * @param String $url -> variable PHP_SELF
 * @return $url;
 */
function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // Nous ne voulons que les liens relatifs de $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

/**
 * Connection d'un utilisateur
 *
 * @param String $email -> email of the user (lel)
 * @param String $password -> password of the user (lel)
 * @param mysql $pdo -> database object
 * @return true/false;
 */
function login($email, $password) {
	$pdo = SPDO::getInstance();
    if ($stmt = $pdo->prepare("SELECT u_id, u_email, u_password, u_salt, u_isAdmin, u_prenom FROM hs_users WHERE u_email = ? LIMIT 1"))
    {
        if ($stmt->execute(array($email)))
        {
            $row = $stmt->fetch(PDO::FETCH_BOTH);

            if ($row) 
            {
                $user_id = $row[0];
                $email = $row[1];
                $db_password = $row[2];
                $salt = $row[3];
                $isAdmin = $row[4];
                $prenom = $row[5];

				// Hashe le mot de passe avec le salt unique
				$password = hash('sha512', $password . $salt);
				// Si l’utilisateur existe, le script vérifie qu’il n’est pas verrouillé
				// à cause d’essais de connexion trop répétés 
				if (checkbrute($user_id) == true) {
					// Le compte est verrouillé 
					// Envoie un email à l’utilisateur l’informant que son compte est verrouillé
					$_SESSION['error'] = "Compte vérouillé, veuillez contactez un administrateur ou verifier vos mails";
					return false;
				} else {
					// Vérifie si les deux mots de passe sont les mêmes
					// Le mot de passe que l’utilisateur a donné.
					if ($db_password == $password) {						
						// Le mot de passe est correct!
						// Récupère la chaîne user-agent de l’utilisateur						
						$user_browser = $_SERVER['HTTP_USER_AGENT'];
						
						// Protection XSS car nous pourrions conserver cette valeur
						$user_id = preg_replace("/[^0-9]+/", "", $user_id);
						$_SESSION['Auth']['id'] = $user_id;
						
                        $_SESSION['Auth']['prenom'] = $prenom;
						$_SESSION['Auth']['email'] = $email;

						$_SESSION['Auth']['login_string'] = hash('sha512', $db_password . $user_browser);

						if($isAdmin)
							$_SESSION['Auth']['role'] = 2;
						else
							$_SESSION['Auth']['role'] = 1;
												
						// Ouverture de session réussie.
						return true;					
					} else {
						// Le mot de passe n’est pas correct
						// Nous enregistrons cet essai dans la base de données
						$now = time();
						$brut = $pdo->prepare("INSERT INTO hs_login_attempts(u_id, la_time)
										VALUES ('$user_id', '$now')");
                        $brut->execute();
						$_SESSION['error'] = "Mot de passe incorrect";
						return false;
					}
				}
			} else {
				// L’utilisateur n’existe pas.
				$_SESSION['error'] = "L\'utilisateur n\'existe pas";
				return false;
			}
		} else {
			$_SESSION['error'] = "Erreur lors de la connexion";
			return false;
		}
	} else {
		$_SESSION['error'] = "Erreur lors de la connexion";
		return false;
	}
}

/**
 * Création d'un utilisateur
 *
 * @param String $username -> username of the user (lel)
 * @param String $password -> password of the user (lel)
 * @param mysql $pdo -> database object
 * @return true/false;
 */
function signup($nom, $prenom, $email, $email2, $password, $password2, $adresse, $codepostal, $ville, $telephone) {   
    $pdo = SPDO::getInstance(); 
    if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['email2'], $_POST['password'], $_POST['password2'], $_POST['adresse'], $_POST['codepostal'], $_POST['ville'], $_POST['telephone'])) {
        // Nettoyez et validez les données transmises au script
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $email2 = filter_input(INPUT_POST, 'email2', FILTER_SANITIZE_EMAIL);
        $email2 = filter_var($email2, FILTER_VALIDATE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) OR !filter_var($email2, FILTER_VALIDATE_EMAIL)) {          
            $_SESSION['error'] = "Adresse mail non valide";
            return false;
        }
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);

        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adresse = $_POST['adresse'];
        $codepostal = $_POST['codepostal'];
        $ville = $_POST['ville'];
        $telephone = $_POST['telephone'];

        if($email != $email2){                    
            $_SESSION['error'] = "Les emails ne correspondent pas.";
            return false;
        }

        if($password2 != $password){                    
            $_SESSION['error'] = "Les mots de passes ne correspondent pas.";
            return false;
        }

        if(!preg_match("/^[\p{L}-]*$/u", $nom)){                    
            $_SESSION['error'] = "Le nom n\'est pas correct.";
            return false;
        }

        if(!preg_match("/^[\p{L}-]*$/u", $prenom)){                    
            $_SESSION['error'] = "Le prenom n\'est pas correct.";
            return false;
        }

        if(!preg_match("/^[0-9a-zA-Z]+(?:[\s-][a-zA-Zéèàêàäâêëîï]+)*$/u", $adresse)){                    
            $_SESSION['error'] = "L\'adresse n\'est pas correct.";
            return false;
        }

        if(!preg_match("/^[0-9]{5}$/u", $codepostal)){                    
            $_SESSION['error'] = "Le code postal n\'est pas correct.";
            return false;
        }

        if(!preg_match("/^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/u", $ville)){                    
            $_SESSION['error'] = "La ville n\'est pas correct.";
            return false;
        }

        if(!preg_match("/^\d{10}$/u", $telephone)){                    
            $_SESSION['error'] = "Le numéro de telephone n\'est pas correct.";
            return false;
        }

        // La forme du nom d’utilisateur et du mot de passe a été vérifiée côté client
        // Cela devrait suffire, car personne ne tire avantage
        // à briser ce genre de règles.
        //
     
        $prep_stmt = "SELECT u_id FROM hs_users WHERE u_email = ? LIMIT 1";
        $stmt = $pdo->prepare($prep_stmt);
     
        if ($stmt) {
            $stmt->execute(array($email));
            $row = $stmt->fetch();
            if ($row) {            
                $_SESSION['error'] = "Il existe déja un utilisateur avec le même email";
                return false;
            }
        } else {
            $_SESSION['error'] = "Erreur de base de données";
            return false;
        }
     
        // CE QUE VOUS DEVEZ FAIRE: 
        // Nous devons aussi penser à la situation où l’utilisateur n’a pas
        // le droit de s’enregistrer, en vérifiant quel type d’utilisateur essaye de
        // s’enregistrer.
     

        // Crée un salt au hasard
        $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
 
        // Crée le mot de passe en se servant du salt généré ci-dessus 
        $password = hash('sha512', $password . $random_salt);
 
        // Enregistre le nouvel utilisateur dans la base de données
        if ($insert_stmt = $pdo->prepare("INSERT INTO hs_users (u_prenom, u_nom, u_email, u_password, u_salt, u_dateInscription, u_actif_token) VALUES (?, ?, ?, ?, ?,now(),?)")) 
        {
            if ($insert_stmt->execute(array($prenom, $nom, $email, $password, $random_salt,md5(uniqid(rand(), true)))))
            {       
                $u_id = $pdo->lastInsertId();
                if ($insert_stmt = $pdo->prepare("INSERT INTO hs_facturation (u_id, f_adresse, f_postal, f_ville, f_telephone) VALUES (LAST_INSERT_ID(), ?, ?, ?, ?)")) 
                {
                    if ($insert_stmt->execute(array($adresse, $codepostal, $ville, $telephone)))
                    {       
                        sendMailConfirmation($u_id);
                        return true;
                    } else {
                        $_SESSION['error'] = "Erreur de base de données facturation";
                        return false;
                    }
                } else {
                    $_SESSION['error'] = "Erreur de base de données facturation";
                    return false;
                }
            } else {
                $_SESSION['error'] = "Erreur de base de données";
                return false;
            }
        } else {
            $_SESSION['error'] = "Erreur de base de données";
            return false;
        }
    } else {
        $_SESSION['error'] = "Erreur de base de données";
        return false; 
    }
}

function changepassword() {   
    $pdo = SPDO::getInstance(); 
    if (isset($_POST['password'], $_POST['password2'], $_POST['password3'], $_POST['email'])) {

        $email = $_POST['email'];
        // Nettoyez et validez les données transmises au script
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $password2 = filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_STRING);
        $password3 = filter_input(INPUT_POST, 'password3', FILTER_SANITIZE_STRING);

        if($password2 != $password3){                    
            $_SESSION['error'] = "Les mots de passes ne correspondent pas";
            session_write_close();
            return false;
        }
     
        // La forme du nom d’utilisateur et du mot de passe a été vérifiée côté client
        // Cela devrait suffire, car personne ne tire avantage
        // à briser ce genre de règles.
        //
     
        $prep_stmt = "SELECT * FROM hs_users WHERE u_email = ? LIMIT 1";
        $stmt = $pdo->prepare($prep_stmt);
     
        $stmt->execute(array($email));
        $row = $stmt->fetch(PDO::FETCH_BOTH);     
        $verifPassword = $row['u_password'];
     
        // CE QUE VOUS DEVEZ FAIRE: 
        // Nous devons aussi penser à la situation où l’utilisateur n’a pas
        // le droit de s’enregistrer, en vérifiant quel type d’utilisateur essaye de
        // s’enregistrer.
 
        // Crée le mot de passe en se servant du salt généré ci-dessus 
        $password = hash('sha512', $password . $row['u_salt']);

        if ( $password != $verifPassword ) {
            $_SESSION['error'] = "Votre ancien mot de passe est incorrect";
            session_write_close();
            return false;
        } else {
            $new = hash('sha512', $password2 . $row['u_salt']);
            $queryUpdate = "UPDATE hs_users SET u_password = ? WHERE u_email = ?";
            $stmt = $pdo->prepare($queryUpdate);
            $stmt->execute(array($new, $email));

            return true;
        }
    } else {
        $_SESSION['error'] = "Erreur de base de données";
        return false; 
    }
}

function changeEmail() {   
    $pdo = SPDO::getInstance(); 
    if (isset($_POST['email'])) {
        // Nettoyez et validez les données transmises au script
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {          
            $_SESSION['error'] = "Adresse mail non valide";
            session_write_close();
            return false;
        }
     
        $prep_stmt = "SELECT u_id FROM hs_users WHERE u_email = ? LIMIT 1";
        $stmt = $pdo->prepare($prep_stmt);
     
        if ($stmt) {
            $stmt->execute(array($email));
            $row = $stmt->fetch();
            if ($row) {            
                $_SESSION['error'] = "Il existe déja un utilisateur avec le même email";
                session_write_close();
                return false;
            }
        } else {
            $_SESSION['error'] = "Erreur de base de données";
            return false;
        }

        $queryUpdate = "UPDATE hs_users SET u_email = ? WHERE u_id = ?";
        $stmt = $pdo->prepare($queryUpdate);

        if ( $stmt ) {
            $stmt->execute(array($email,$_SESSION['Auth']['id']));
            $_SESSION['Auth']['email'] = $email;
        } else {
            return false;
        }

        return true;

    } else {
            $_SESSION['error'] = "Erreur de base de données";
            return false; }
}

/**
 * Return le resultat d'une requete SQL
 *
 * @param $pdo -> Object
 * @param $query -> String
 * @param $fetch -> String fetch name
 * @param $type -> String fetch type
 * @return array.
 */
function getArrayFrom($pdo,$query,$fetch = "fetchAll", $type = "FETCH_ASSOC")
{
    if ($stmt = $pdo->prepare($query)) 
    {
        if ($stmt->execute()) 
        {
            switch ($fetch) {
                case 'fetchAll':
                    switch ($type) {
                        case 'FETCH_ASSOC':
                            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            break;
                        
                        case 'FETCH_BOTH':
                            $row = $stmt->fetchAll(PDO::FETCH_BOTH);
                            break;
                        
                        case 'FETCH_NUM':
                            $row = $stmt->fetchAll(PDO::FETCH_NUM);
                            break;
                        
                        case 'FETCH_OBJ':
                            $row = $stmt->fetchAll(PDO::FETCH_OBJ);
                            break;
                    }
                    break;
                case 'fetch':
                    switch ($type) {
                        case 'FETCH_ASSOC':
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            break;
                        
                        case 'FETCH_BOTH':
                            $row = $stmt->fetch(PDO::FETCH_BOTH);
                            break;
                        
                        case 'FETCH_NUM':
                            $row = $stmt->fetch(PDO::FETCH_NUM);
                            break;
                        
                        case 'FETCH_OBJ':
                            $row = $stmt->fetch(PDO::FETCH_OBJ);
                            break;
                    }
                    break;
            }

            if ($row) 
            {
                return $row;
            }
        }
    }
    return 0;
}

/**
 * Return la partie GET d'un lien donné
 *
 * @return String.
 */
function getLink(){
    $actual_link = $_SERVER['REQUEST_URI'];
    return explode('?',$actual_link)[1];
}

/**
 * Return le resultat d'une comparaison
 *
 * @param $first -> String
 * @param $second -> String
 * @return Boolean.
 */
function isSelected($first, $second){
    if($first == $second)
        return "true";
    return "false";
}

/**
 * 
 *
 * @param $array -> Array
 * @param $default -> String
 * @return String.
 */
function isReturned($pdo, $query){

    if ($stmt = $pdo->prepare($query)) 
    {
        if ($stmt->execute()) 
        {
            $row = $stmt->fetch(PDO::FETCH_BOTH);
            if($row){
                return true;
            } else {
                return false;
            }
        }
    }
}

/**
 * Return une chaine de caractère contenant un Select/Option HTML
 *
 * @param $array -> Array
 * @param $default -> String
 * @return String.
 */
function isInArray($array, $test){
    do {
        foreach ($array as $subArray) {
            foreach ($subArray as $value) {
                if ($value == $test) return true;
            }
        }
        $array = $value;
    } while (is_array($array));
    return false;
}

/**
 * Vérification 
 *
 * @param $array -> Array
 * @param $default -> String
 * @return String.
 */
function checkReCaptcha($post){
	$secret="6LdamA0UAAAAAA_o8HIR6JbBCIKpr1YLTMmuJy7I";
	$response=$post["g-recaptcha-response"];
	$verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
	$captcha_success=json_decode($verify);

	if ($captcha_success->success==false) {
		return false;
	} else if ($captcha_success->success==true) {
		return true;
	}
}

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// COOOOOKIE
function setCookieUser($name,$content,$time) {
    $time = $time*60;
    setcookie ($name, $content, time() + $time, '/', 'heavyserver.com',1);
}

// Générer une clé unique pour la réinitialisation de mot de passe
function generateUniqueKey() {
    $uniqid = uniqid(mt_rand(),true);
    return md5($uniqid);
}

function couponExiste($code){
    $pdo = SPDO::getInstance(); 
    $query = 'SELECT coup_id, coup_libelle, coup_type, coup_percent, coup_fixe, o_id from hs_coupons WHERE coup_libelle = ? LIMIT 1';
    if ($stmt = $pdo->prepare($query)) 
    {
        if ($stmt->execute(array($code)))
        {
            $row = $stmt->fetch(PDO::FETCH_BOTH);

            if ($row)
                return $row;
            else {
                $_SESSION['error'] = "Le coupon n\'existe pas";
                session_write_close();
                return false;
            }
        } else {
            $_SESSION['error'] = "Erreur pendant l\'application du coupon !";
            session_write_close();
            return false;
        }
    } else {
        $_SESSION['error'] = "Erreur pendant l\'application du coupon !";
        session_write_close();
        return false;
    }
}

function gigaDisponible() {
    $pdo = SPDO::getInstance(); 
    $query = 'SELECT sum(o_disque) AS somme FROM hs_commandes JOIN hs_offres USING(o_id)';
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_BOTH);

    return 3500-$row['somme'];
}