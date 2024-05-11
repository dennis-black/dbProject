<?php

if(($_SERVER["REQUEST_METHOD"] === "POST") && ($_POST["fUpdateUserPassword"]))
{

    include_once("Database_EstConnection.php");

    // Get credentials info
    $bOldPasswordVerify     = $_POST["fOldPasswordVerify"]      ?? "";
    $bChangePassword        = $_POST["fUpdatePassword"]         ?? "";
    $bChangePassword_Again  = $_POST["fUpdatePassword_Again"]   ?? "";

    $SQL_STATMENT = $dbHandler->prepare("SELECT `Password` FROM `User_Security` WHERE `UserID` = :UserID");
    $SQL_STATMENT-> bindParam(':UserID', $_SESSION["UserID"]);

    try{
    
        $SQL_STATMENT -> execute();
        $User_Security = $SQL_STATMENT -> fetch(PDO::FETCH_ASSOC);
    
        if(password_verify($bOldPasswordVerify, $User_Security["Password"])){

            if($bChangePassword === $bChangePassword_Again){

                $bHashedPassword = password_hash($bChangePassword, PASSWORD_DEFAULT);

                try{
                    $SQL_STATMENT = $dbHandler->prepare("UPDATE `User_Security` SET `Password` = :Password WHERE `UserID` = :UserID");
                    $SQL_STATMENT -> bindParam(':UserID', $_SESSION["UserID"]);
                    $SQL_STATMENT -> bindParam(':Password', $bHashedPassword);
                
                    if($SQL_STATMENT->execute()){
                        
                        _logout_("Login.php", "Password is successfully changed, now logging out");
                    }
                }catch(PDOException $ERR_){

                    echo "DATABASE ERROR: " . $ERR_->getMessage();
                }
            }else{

                echo
                "
                    <script>

                        alert(\" Change Password Failed: New password is not matched \");
                        window.location.href = \" UserAccountSettings.php \";
                    </script>
                ";
            }
        }else{

            echo
            "
                <script>

                    alert(\" Verification Failed: Old password is not matched \");
                    window.location.href = \" UserAccountSettings.php \";
                </script>
            ";
        }
    }catch(PDOException $ERR_){

        echo "DATABASE ERROR: " . $ERR_->getMessage();
    }
}