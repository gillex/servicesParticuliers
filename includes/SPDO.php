<?php

class SPDO extends PDO
{
  /**
   * Instance de la classe PDO
   *
   * @var PDO
   * @access private
   */ 
  private $PDOInstance = null;
 
   /**
   * Instance de la classe SPDO
   *
   * @var SPDO
   * @access private
   * @static
   */ 
  private static $instance = null;
 
  /**
   * Constructeur
   *
   * @param void
   * @return void
   * @see PDO::__construct()
   * @access private
   */
  public function __construct()
  {
	  try {		  
		parent::__construct('mysql:dbname='.DATABASE.';host='.HOST,USER ,PASSWORD);
		$this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );		
    $this->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
		return $this;
	  } catch(PDOException $e){
		  echo 'ERROR: '.$e->getMessage();
	  }
  }
 
   /**
    * Crée et retourne l'objet SPDO
    *
    * @access public
    * @static
    * @param void
    * @return SPDO $instance
    */
  public static function getInstance()
  {  
    if(is_null(self::$instance))
    {
      self::$instance = new SPDO();
    }
    return self::$instance;
  }
 
  /**
   * Exécute une requête SQL avec PDO
   *
   * @param string $query La requête SQL
   * @return PDOStatement Retourne l'objet PDOStatement
   */
  public function query($query)
  {
    return $this->PDOInstance->query($query);
  }
}