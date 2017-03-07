<?php
require_once('Zend/XmlRpc/Client.php');
/*
 *
 * Distributed under the terms of the license described in COPYING
 *
 */
class_exists('CApi') or die();

CApi::Inc('common.plugins.change-password');

class CCustomChangePasswordPlugin extends AApiChangePasswordPlugin
{
    /**
     * @param CApiPluginManager $oPluginManager
     */
    public function __construct(CApiPluginManager $oPluginManager)
    {
    parent::__construct('1.0', $oPluginManager);
    }

    /**
     * @param CAccount $oAccount
     * @return bool
     */
    public function validateIfAccountCanChangePassword($oAccount)
    {
    $bResult = false;
    if ($oAccount instanceof CAccount)
    {
        $bResult = true;
    }

    return $bResult;
    }

    /**
     * @param CAccount $oAccount
     * @return bool
     */
    public function ChangePasswordProcess($oAccount)
    {
    $bResult = false;
    if (0 < strlen($oAccount->PreviousMailPassword) &&
        $oAccount->PreviousMailPassword !== $oAccount->IncomingMailPassword)
    {

    $username = $oAccount->Email;
    $password = $oAccount->PreviousMailPassword; 
    $newpassword = $oAccount->IncomingMailPassword;
    $loginUrl = 'https://admin.stsmail.ro/postfixadmin/xmlrpc.php';

    $xmlrpc = new Zend_XmlRpc_Client($loginUrl);

    $http_client = $xmlrpc->getHttpClient();
    $http_client->setCookieJar();

    $login_object = $xmlrpc->getProxy('login');
    $success = $login_object->login($username, $password);

    if ($success){
	$parola=$xmlrpc->getProxy('user');
	if($parola->changePassword($password,$newpassword)) {
	}
    } else {
	CApi::Log("XMLRPC threw error \"" . "\" for $query");
//	die("Auth failed");
	throw new CApiManagerException(Errs::UserManager_AccountNewPasswordUpdateError);
}

    }

    return $bResult;
    }
}

return new CCustomChangePasswordPlugin($this);
