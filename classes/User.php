<?php

namespace classes\domain;

use classes\mapper as Mapper;
use classes\util\Hash;

class User extends DomainObject {
    private $username;
    private $password;
    private $result;

    function __construct( $id = null, $username = null ) {
        $this->username = $username;
        parent::__construct( $id );
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername( $username )
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword( $password )
    {
        $this->password = $password;
    }

    public static function isLoggedIn() {
        if ( isset( $_SESSION["user"] ) ) {
            $userId = $_SESSION["user"];
            if ( self::userExists( $userId ) ) {
                return true;
            }
        }

        return false;
    }

    private static function userExists( $userId ) {
        $userMapper = new Mapper\UserMapper();
        $obj = $userMapper->find( $userId );

        if ( isset( $obj ) ) {
            return true;
        }

        return false;
    }

    public function selectUser( $username, $password ) {
        $userMapper = new Mapper\UserMapper();
        $select = $userMapper->selectStmt();
        $select->bindParam( 1, $username, \PDO::PARAM_STR );
        $select->bindParam( 2, Hash::make( $password ), \PDO::PARAM_STR );

        if ( $select->execute() ) {
            if ( $select->rowCount() === 1 ) {
                $this->result = $select->fetch( \PDO::FETCH_OBJ );
                return true;
            }
        }

        return false;
    }

    public function login( $username = null, $password = null ) {
        if ( $this->selectUser( $username, $password ) ) {
            $_SESSION["user"] = $this->result->id;
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        if ( isset( $_SESSION['user'] ) ) {
            unset( $_SESSION['user'] );
        }
    }
}
