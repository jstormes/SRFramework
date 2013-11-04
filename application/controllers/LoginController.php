<?php

class LoginController extends Zend_Controller_Action
{

    private $config = null;
    private $user   = null;
    
    public function init()
    {
        /* Initialize action controller here */
    	$this->config     = Zend_Registry::get('config');
    	$this->user       = Zend_Registry::get('user');
    	
    	// this controller uses classic php sessions
    	@session_start();
    }
    
     /*Validate an email address.
     Provide email address (raw input)
     Returns true if the email address has the email
     address format and the domain exists.*/
    private function validEmail($email)
    {
        $isValid = true;
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex)
        {
            $isValid = false;
        }
        else
        {
            $domain = substr($email, $atIndex+1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64)
            {
                // local part length exceeded
                $isValid = false;
            }
            else if ($domainLen < 1 || $domainLen > 255)
            {
                // domain part length exceeded
                $isValid = false;
            }
            else if ($local[0] == '.' || $local[$localLen-1] == '.')
            {
                // local part starts or ends with '.'
                $isValid = false;
            }
            else if (preg_match('/\\.\\./', $local))
            {
                // local part has two consecutive dots
                $isValid = false;
            }
            else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
            {
                // character not valid in domain part
                $isValid = false;
            }
            else if (preg_match('/\\.\\./', $domain))
            {
                // domain part has two consecutive dots
                $isValid = false;
            }
            else if
            (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                    str_replace("\\\\","",$local)))
            {
                // character not valid in local part unless
                // local part is quoted
                if (!preg_match('/^"(\\\\"|[^"])+"$/',
                        str_replace("\\\\","",$local)))
                {
                    $isValid = false;
                }
            }
            if ($isValid && !(checkdnsrr($domain,"MX") ||
                     checkdnsrr($domain,"A")))
            {
                // domain not found in DNS
                $isValid = false;
            }
        }
        return $isValid;
    }
    
    private function uuid()
    {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                mt_rand( 0, 0x0fff ) | 0x4000,
                mt_rand( 0, 0x3fff ) | 0x8000,
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
    }

    public function indexAction()
    { 
        $this->view->headTitle('Login');
        
        $this->view->message='';
        
        // get our domain for cookies
        $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
        $this->domain=$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
         
         
        // Logout any logged in user ,$_COOKIE['cavpad']
        $redirect=false;
        if (isset($_COOKIE['cavuser'])) {
            setcookie("cavuser",$_COOKIE['cavuser'], time() - 3600, "/", $this->domain);
            $redirect=true;
        }
        if (isset($_COOKIE['cavpad'])) {
            setcookie("cavpad",$_COOKIE['cavpad'], time() - 3600, "/", $this->domain);
            $redirect=true;
        }
        if ($redirect)
            $this->_redirect($_SERVER["REQUEST_URI"]);    // redirect to clear out cookies.
        
        
        if (isset($_SESSION['msg'])) {
            $this->view->message=$_SESSION['msg'];
            $_SESSION['msg']=null;
            unset($_SESSION['msg']);
        }
        
        if ($this->getRequest()->isPost()) {
            $this->view->email=$_POST['email'];
            $this->view->password=$_POST['password'];

            // Reqest Access button action
            if (isset($_POST['request_access'])) {
                // redirect to request access page.
                // $_SESSION['email']=$this->view->email;
                $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
                if ($this->config->federated==true)
                    $domain="http://".$this->config->authenticationsubdomain.".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1]."/login/request";
                else
                    $domain="http://".$split_hostname[count($split_hostname)-3].".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1]."/login/request";
                //$return = urlencode($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
                header( "Location: $domain" );
                exit();
            }
                                
            // Login button action
            if (isset($_POST['login']) && $this->validEmail($this->view->email)) {
                
                // select from user where email=$email
                $UserTable = new Application_Model_Shared_User();
                $sel = $UserTable->select();
                $sel->where("user_nm = ? ",$this->view->email);
                $UserRow=$UserTable->fetchAll($sel)->current();
                if ($UserRow) {
                    if ($UserRow->deleted==false){
                        if ($UserRow->password==md5($this->view->password.$UserRow->salt)) {
                            // ************ User is valid ************
                            
                            if ($UserRow->onetimepad==null) {
                                $UserRow->onetimepad = $this->uuid();
                                $UserRow->save();
                            }   
                            
                            // Set cookies
                            setcookie("cavuser",$this->view->email, 0, "/", $this->domain);
                             
                            // Set common one time pad.
                            setcookie("cavpad",$UserRow->onetimepad , 0, "/", $this->domain);
                             
                            
                            // Redirect to home page
                            if ($_GET['ret']!="")
                            {
                                $this->_redirect("http://".$_GET['ret']);
                            }
                            else
                            {
                                $this->_redirect("/");
                             
                            }
                            exit();
                        }
                    }
                    else
                        $this->view->message="I am sorry, that account is disabled. You can request access by clicking on the request access button.";
                }
                if ($this->view->message=='')
                    $this->view->message="Invalid username or password.";
            }
                
            // Forgot Password button action
            if (isset($_POST['reset_password']) && $this->validEmail($this->view->email)) {
                $UserTable = new Application_Model_Shared_User();
                $sel = $UserTable->select();
                $sel->where("user_nm = ? ",$this->view->email);
                $UserRow=$UserTable->fetchAll($sel)->current();
                if ($UserRow) {
                    if ($UserRow->deleted==false){
                        
                        $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
                        $domain=$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
                        $contact_name = $this->config->contact_name;
                        $contact_email = $this->config->contact_email;
                        $reset_link = $_SERVER['SERVER_NAME']."/login/reset?pad=".$UserRow->onetimepad;
                        $ip_address = $_SERVER['REMOTE_ADDR'];
                        
                        // *****************  User is valid for password reset **************
                        $mail = new Zend_Mail();
                        $mail->setFrom('noreply@cavokgroup.com', 'No Reply');
                        $mail->addTo($this->view->email);
                        $mail->setSubject('Password reset request');
                        $mail->setBodyText(
"A new password was requested for your account at $domain from IP Address $ip_address.

To reset your password please copy this link into your browser: http://$reset_link

If you did not request a password reset please contact $contact_name at $contact_email.

Thank you
");

                        $mail->setBodyHtml(
"A new password was requested for your account at $domain from IP Address $ip_address. <br /><br />

To reset your password please click the link: <a href='http://$reset_link'>http://$reset_link</a>  <br /><br />

If you did not request a password reset please contact $contact_name at <a href='mailto:$contact_email?Subject=Password%20Request'>$contact_email</a>. <br /><br />

Thank you <br />
");
                        $mail->send();
                        
                        $this->view->message='A reset password email has been sent to your email address.  Please follow the directions in that email.';
                        
                    }
                }
                if ($this->view->message=='')
                    $this->view->message="I am sorry, that is not an active email account in this system.";

            }   else if(!$this->validEmail($this->view->email)) {
                    $this->view->message="Invalid email address.";
            }
        }
    }
    
    public function redirect_msg($msg) {
        // if we are here we don't have a good password reset context so redirect to login.
        $_SESSION['msg']=$msg;

        $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
        if ($this->config->federated==true)
            $domain="http://".$this->config->authenticationsubdomain.".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1]."/login";
        else
            $domain="http://".$split_hostname[count($split_hostname)-3].".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1]."/login";
        //$return = urlencode($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
        header( "Location: $domain" );
        exit();
    }
    
    private function passwordCheck($password) {
        
        return (preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/", $password));
        
    }
    
    public function resetAction() {
        
        $UserTable = new Application_Model_Shared_User();
        
        $this->view->headTitle('Password Reset');
        $this->view->enable_navigation=false;        
        
        // if cancel then redirect back to once we came.
        if ($this->getRequest()->isPost()) {
            if (isset($_POST['cancel'])) {
                // Redirect to home page
                if (isset($_POST['ret']))
                {
                    $this->_redirect("http://".$_POST['ret']);
                }
                else
                {
                    $this->_redirect("/");
                }
                exit();
            }
        }
        
        // if we are not logged in but have a valid PAD then we are respoing to a password change from an email.
        if (isset($_GET['pad'])) {
            
            $sel = $UserTable->select();
            $sel->where("onetimepad = ? ",$_GET['pad']);
            $UserRow=$UserTable->fetchAll($sel)->current();
            if ($UserRow) {
                if ($UserRow->deleted==false){
                    // *********** We have a valid Pad ***************
                    $this->view->have_pad=true;
                    if ($this->getRequest()->isPost()) {
                        if ($_POST['new_passwd']!==$_POST['ver_passwd']) {
                            $this->view->message="Password don't match please try again.";
                            return;
                        }
                        else {
                            if ($this->passwordCheck($_POST['new_passwd'])){
                                $UserRow->onetimepad=$this->uuid();
                                $UserRow->salt=$this->uuid();
                                $UserRow->password=md5($_POST['new_passwd'].$UserRow->salt);
                                $UserRow->save();
                                $this->redirect_msg("Your password has been changed.  You may login below.");
                            }
                            else {
                                $this->view->message="New Password must be at least 6 characters with one upper case, one lower case and one number.";
                            }
                                
                        }
                    }                    
                    return;
                } 
            }
        }
        else {
            // We must be logged in already to change the password
            if ($this->user!=null) {
                if ($this->getRequest()->isPost()) {
                    if ($_POST['new_passwd']!==$_POST['ver_passwd']) {
                        $this->view->message="Password don't match please try again.";
                        return;
                    }
                    
                    $sel = $UserTable->select();
                    $sel->where("onetimepad = ? ",$_COOKIE['cavpad']);
                    $sel->where("user_nm = ? ",$_COOKIE['cavuser']);
                    $UserRow = $UserTable->fetchAll($sel)->current();
                    if ($UserRow->password==md5($_POST['old_passwd'].$UserRow->salt)) {
                        if ($this->passwordCheck($_POST['new_passwd'])){
                            // We are good to chang the password
                            $UserRow->onetimepad=$this->uuid();
                            $UserRow->salt=$this->uuid();
                            $UserRow->password=md5($_POST['new_passwd'].$UserRow->salt);
                            $UserRow->save();
                            $this->redirect_msg("Your password has been changed.  You may login below.");
                        }
                        else {
                            $this->view->message="New Password must be at least 6 characters with one upper case, one lower case and one number.";
                        }
                    } 
                    else {
                        $this->view->message="Your old password did not match.";
                    }
                    
                }
                return;
            }
            $this->view->message="Unknown error please try again.";
        }
        $this->redirect_msg("This password reset request has ben used or has expired.  You may request another password reset below."); 
    }
    
    public function requestAction() {
        
        $this->view->headTitle('Request Access');
        $this->view->message='';
        
        if (isset($_SESSION['email'])) {
            $this->view->email=$_SESSION['email'];
            unset($_SESSION['email']);
        }
        
        if ($this->getRequest()->isPost()) {
            if (isset($_POST['cancel']))
                $this->_redirect('/login');
            
            $this->view->name=$_POST['name'];
            $this->view->email=$_POST['email'];
            $this->view->phone=$_POST['phone'];
            $this->view->mgr_name=$_POST['mgr_name'];
            $this->view->mgr_email=$_POST['mgr_email'];
            $this->view->mgr_phone=$_POST['mgr_phone'];
            $this->view->instructions=$_POST['instructions'];
            
            if (strlen($this->view->name)<3) {
                $this->view->message.='Your name is too short.<br/>';
            }
            
            if(!$this->validEmail($this->view->email)){
                $this->view->message.='Your email address is not valid.<br/>';
            }
            
            if(!$this->validEmail($this->view->mgr_email)){
                $this->view->message.='Your maanger\'s email address is not valid.';
            }
            
            if ($this->view->message=='') {
                
                $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
                $domain=$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
                $contact_name = $this->config->contact_name;
                $contact_email = $this->config->contact_email;
                $reset_link = $_SERVER['SERVER_NAME']."/login/reset?pad=".$UserRow->onetimepad;
                $ip_address = $_SERVER['REMOTE_ADDR'];
                
                $name=$this->view->name;
                $email=$this->view->email;
                $phone=$this->view->phone;
                $mgr_name=$this->view->mgr_name;
                $mgr_email=$this->view->mgr_email;
                $mgr_phone=$this->view->mgr_phone;
                $instructions=$this->view->instructions;
                
                
                // *****************  Send email for access request **************
                $mail = new Zend_Mail();
                $mail->setFrom('noreply@cavokgroup.com', 'No Reply');
                $mail->addTo($this->config->contact_email);
                $mail->setSubject("Access request for domain $domain");
                $mail->setBodyText(
                        "Access to $domain was requested from IP Address $ip_address.
                
User Name: $name
User Email: $email
User Phone: $phone
                        
Maanger's Name: $mgr_name
Manager's Email: $mgr_email
Manager's Phone: $mgr_phone
                        
Special Instructions:
$instructions
");
                
                $mail->setBodyHtml(
                "Access to $domain was requested from IP Address $ip_address. <br/><br/>
                
User Name: $name<br/>
User Email: $email<br/>
User Phone: $phone<br/><br/>
                        
Maanger's Name: $mgr_name<br/>
Manager's Email: $mgr_email<br/>
Manager's Phone: $mgr_phone<br/><br/>
                        
Special Instructions:<br/>
<pre>
$instructions
</pre>
                ");
                $mail->send();
                
                $this->view->message='Your request for access haas ben sent, you will receive an email when you access is granted.';
            }
        }
    }
}

