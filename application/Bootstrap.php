<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{   
    // Our Standard Registry objects (Candidates for Zend Cache)
    protected $config           = null; // $this->config->(Option) = application.ini config option
    protected $db               = null; // Application DB
    protected $log              = null; // Logging object
    protected $application              = null; // Application specific configuration from shared db.
    protected $user             = null; // The user row from the user table in the shared db.

    // local only properties
    protected $Signed_in        = false; // No user signed in by default
    
    protected $AuthServer       = ''; // Authentication server, logins are redirected to this server.
    protected $LogInOutURL      = ''; // URL to login/logout of the applications.
    protected $ProfileURL       = ''; // URL for the user to modify their profile.
    
    /***********************************************************************
     * Force SSL for our production environment.
     **********************************************************************/
    protected function _initForceSSL() {
        // if we are command line then just return
        if (PHP_SAPI == 'cli')
            return;
    
        if ( APPLICATION_ENV == 'production' )
        {
            if($_SERVER['SERVER_PORT'] != '443') {
                header('Location: https://' . $_SERVER['HTTP_HOST'] .
                $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }
    
    /***********************************************************************
     * Get a copy of our configuration environment
     **********************************************************************/
    protected function _initConfig()
    {
        $this->config = new Zend_Config($this->getOptions(), true); 
        Zend_Registry::set('config', $this->config);
    }
    
    /***********************************************************************
     * Initialize our databases, setup two connections.
     * 
     * The default connection will be used by our standard models in 
     * /application/models/DbTables, and a second connection for our shared
     * models in /applicaiton/models/Common.
     **********************************************************************/
    protected function _initDatabases()
    {
        // Load the multi db plugin
        $resource = $this->getPluginResource('multidb');
        $resource->init();
    
        // set our shared database connection used by the
        // models in the /application/models/Shared directory.
        Zend_Registry::set('shared_db', $resource->getDb('shared'));
    
        // Setup our default database connection
        $this->bootstrap('db');
        $db = $this->getPluginResource('db');
    

        // force UTF-8 connection
        $stmt = new Zend_Db_Statement_Pdo(
                $db->getDbAdapter(),
                "SET NAMES 'utf8'"
        );
        $stmt->execute();
    
        $dbAdapter = $db->getDbAdapter();
    
        // Query profiler (if enabled and not in production)
        $options = $db->getOptions();
        if ($options['profiler']['enabled'] == true
                && APPLICATION_ENV != 'production'
        ) {
            $profilerClass  = $options['profiler']['class'];
            $profiler       = new $profilerClass('All DB Queries');
            $profiler->setEnabled(true);
            $dbAdapter->setProfiler($profiler);
        }
    
        Zend_Registry::set('db', $dbAdapter);
    }
    
    /***********************************************************************
     * Initialize our logging.  
     * 
     * All logging more severe that "DEBUG" is sent to the log table of the
     * application database.  Firebug (FirePHP) is only enabled for 
     * non production environments. 
     * 
     * log:
     * --------------------------------------------------------------------------------------------
     * | log_id  | message     | priority | timestamp  | priorityName | user_id     | request_uri |
     * --------------------------------------------------------------------------------------------
     * | Primary | Text string | Numeric  | Time error | String text  | user_id of  | URL of the  |
     * | Key     | of error    | priority | occurred   | of priority  | the user if | request if  |
     * |         | message.    |          |            |              | available.  | available.  |
     * --------------------------------------------------------------------------------------------
     **********************************************************************/
    protected function _initLogger() {
    
        // Setup logging
        $this->log = new Zend_Log();
         
        // Add user_id to the logged events
        $this->log->setEventItem('user_id', 0);
        // Add the URI to the logged events
        $this->log->setEventItem('request_uri', $_SERVER["REQUEST_URI"]);
         
        $writer_db = new Zend_Log_Writer_Db(Zend_Registry::get('db'), 'log');
        $this->log->addWriter($writer_db);
         
        // Prevent debug messages from going to the DB.
        $filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
        $writer_db->addFilter($filter); 
         
        // if we are not in produciton enable Firebug
        // http://www.firephp.org/
        if ( APPLICATION_ENV != 'production' ) {
            $writer_firebug = new Zend_Log_Writer_Firebug();
            $this->log->addWriter($writer_firebug);
        }
    
        Zend_Registry::set( 'log', $this->log );
    
        // Examples:
        //Zend_Registry::get('log')->debug("this is a debug log test"); // least severe only shown on FireBug console
        //$this->log->debug("this is a debug log test");    // least severe only shown on FireBug console
        //Zend_Registry::get('log')->info("this is a info log test");
        //Zend_Registry::get('log')->notice("this is a notice log test");
        //Zend_Registry::get('log')->warn("this is a warn log test");
        //Zend_Registry::get('log')->err("this is a err log test");
        //Zend_Registry::get('log')->crit("this is a crit log test");
        //Zend_Registry::get('log')->alert("this is a alert log test");
        //Zend_Registry::get('log')->emerg("this is a emerg log test"); // Most severe 
    }
    
    /***********************************************************************
     * Load application specific information from the shared databases.
     * 
     * This is where we will override any application.ini configuration with
     * database driven configuration data.
     * 
     * Required Table Structure (Table may contain more columns, but must
     * contain these):
     *
     * app:
     * -------------------------------------------------------------
     * | app_id           | app_nm      | app_sub_domain | deleted |
     * -------------------------------------------------------------
     * | Primary Key Must | Application | Sub-Domain for | Record  |
     * | Must match       | Name        | building URL   | Deleted |
     * | app_id in *.ini  |             |                |         |
     * -------------------------------------------------------------   
     **********************************************************************/
    protected function _initApp()
    {
        $application_model = new Application_Model_Shared_Application();        
        $this->app = $application_model->find($this->config->app_id)->current();
        Zend_Registry::set('app', $this->app);
    }
    
    /***********************************************************************
     * Load the ACL from the application.ini file.
     * 
     * Format in application.ini is:
     * roles = (base role), (parent role):(child role), ..., administrator
     * 
     * Example:
     * roles = view, user:view, admin:user, administrator
     * 
     * view - The most basic role.
     * user - Can do anything view can + anything user can.
     * admin - Can do anytiing view + user + admin can.
     * administrator - Specal role that can do anything.
     **********************************************************************/
    protected function _initACL() {
        //return;
        $this->acl = new Zend_Acl();
    
        $acls = explode(',',$this->config->roles);
        foreach($acls as $acl_pair) {
            $acl = explode(':', $acl_pair);
    
            if (isset($acl[1])) {
                $this->acl->addRole(new Zend_Acl_Role(trim($acl[0])),trim($acl[1]));
            }
            else {
                $this->acl->addRole(new Zend_Acl_Role(trim($acl[0])));
            }
    
            // our prvilages match our roles.
            if (trim($acl[0])!='administrator')
                $this->acl->allow(trim($acl[0]), null, trim($acl[0]));
            else
                $this->acl->allow(trim($acl[0])); // Special role administrator can do anything!!!!
        }
    
        Zend_Registry::set('acl', $this->acl);
    }
    
    /***********************************************************************
     * Setup our login/logout URL and profile URL.  Allow for some other 
     * server on the same domain to provide login services for multiple 
     * applications.  This server could also use OAuth, Active Directory, 
     * etc...
     * 
     * As long as the proper cookies are setup to match the shared user table
     * the user will be "logged on".
     ***********************************************************************/
    protected function _initAuthServer() {
        // If we have a login server use it to login else use our current server
        if (isset($this->config->login_server))
            $this->AuthServer = $this->config->login_server;
        else
            $this->AuthServer = $_SERVER["HTTP_HOST"];
        
        $this->LogInOutURL = "//".$this->AuthServer."/login";
        $this->ProfileURL  = "//".$this->AuthServer."/login/reset"; // for now we just reset password.
    }

    protected function _initProjectId(){
                   
        $uri = explode("/",$_SERVER["REQUEST_URI"]);
        $this->project_id=0;
        if (in_array('project_id',$uri)) {
            // We have a project_id so check that the project belongs to the customer.
            $index = array_search('project_id',$uri);
            $index++;
            $project_id=$uri[$index];
            //print_r($project_id);
            
            $this->project_id=$project_id;
        }
            
        Zend_Registry::set('project_id',$this->project_id);     
    }
        protected function _initProject() {
        // *******************************
        // Link to project table
        // *******************************
        $this->projectName='';
        $project_id=0;
        
        // Get the project_id if we have one.
        $uri = explode("/",$_SERVER["REQUEST_URI"]);
        if (in_array('project_id',$uri)) {
            $index = array_search('project_id',$uri);
            $index++;
            $project_id=$uri[$index];
        }
        
        try {
            if ($project_id!=0) {
                $this->bootstrap('db');
                $db = $this->getPluginResource('db');
                
                $ProjectTable = new Application_Model_DbTable_Project();
                
                $row = $ProjectTable->find($project_id)->current();
                $this->projectName=$row->project_txt;
            }
        }
        catch (Exception $Ex) {
            // ignore any erros;
            //print_r($Ex);
            //exit();
        }
        
    }
    
    /***********************************************************************
     * Make sure the user is logged in, and setup the user "object" for use 
     * by the reset of the application.  This is who the user "is".  We have 
     * three type of user logins.
     * 
     * The first login type is command line where we use a command line user from
     * the config.  The second type is a cookied user, where the user has a 
     * valid set of cookies that match the user table from the shared 
     * database.  The final type of login in a HTTP BASIC Auth, used for 
     * webservices.
     * 
     * NOTE: This security is not hardened or tested.  Needs more research
     * and though.
     * 
     * Required Table Structure (Table may contain more columns, but must
     * contain these):
     * 
     * user:
     * -------------------------------------------------------------------------------------
     * | user_id     | user_nm    | password         | salt   | onetimepad       | deleted |
     * -------------------------------------------------------------------------------------
     * | Primary Key | user email | MD5(password_txt | Random | Key for cookies  | user    |
     * |             | address    | +salt)           | string | & password reset | deleted |
     * -------------------------------------------------------------------------------------
     **********************************************************************/
    protected function _initUser() {

        // User table
        $user_model = new Application_Model_Shared_User();
    
        // By default the User is not logged in
        $UserRow=false;
        
        // If we are command line attempt to use the command line user from 
        // the config file.
        if (PHP_SAPI == 'cli') {
            if (isset($config->command_line_user))
                $UserRow = $user_model->find($config->command_line_user)->current();
        }
    
        // See if the user is logged in via cookies
        if (isset($_COOKIE['cavuser']) && isset($_COOKIE['cavpad']))
            $UserRow = $user_model->getUserByNameAndPad($_COOKIE['cavuser'],$_COOKIE['cavpad']);
    
        // See if the user is logged in via HTTP BASIC Auth, used mostly for Webservices
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_PW']) {
            $UserRow = $user_model->getUserByNameAndPassword($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
            if (!$UserRow)    // If we have HTTP BASIC Auth but could not sign in with a password try the pad
                $UserRow = $user_model->getUserByNameAndPad($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        }
    
        if ($UserRow) {
            if ($UserRow->deleted==0) {
                $this->user = $UserRow;
                $UserRow->password='';    // Obscure the passwords from the data set.
                $UserRow->salt='';        // Obscure the salt from the data set
    
                $this->Signed_in=true;
    
                // Set the user_id in the logger
                $this->log->setEventItem('user_id', $this->user->user_id);
    
                Zend_Registry::set('user', $UserRow);
                
                return;
            }
        }
         
        // We have not logged in the user so redirect the user to the login page.
        $this->user=null;
        Zend_Registry::set('user', null);
            
        // These are ok urls if we are not logged in.
        $login_urls=array('/login','/login/reset','/login/request','/login/forgot');
        if (isset($_SERVER['REDIRECT_URL']))
            if (in_array($_SERVER['REDIRECT_URL'],$login_urls))
                return; // we on on an allowed url,
                        // so return before we redirect to the lgoin server.
    
        // Get our current_url
        $current_url=urlencode($_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
        
        // We were not authenticated and not on a login url so redirect to our login server.
        header( "Location: ".$this->LogInOutURL."?ret=".$current_url);
        exit();
    }
    
    /***********************************************************************
     * Get the current user role, if there is no current user role will be 
     * null.  This is what the user can do in the application.
     * 
     * ----------------------------------------------------------------
     **********************************************************************/
    protected function _initRole() {
        if ($this->Signed_in) {
            $user_model = new Application_Model_Shared_UserApplication();
            $this->role = $user_model->getUserRoleByApplication($this->user['user_id'], $this->config->application_id);
             if ($this->role==null) {
                 // Error this user has no role for this applicaiton
                 echo('Well this is embarrassing.<br>');
                 echo('Error: User '.$this->user['user_full_nm'].' user_id '.$this->user['user_id'].' has no role for this applicaiton '.$this->config->application_id.'.<br>');
                 echo('Please contact the applicaiton development group to fix this.');
                 exit();
                 
             }
            Zend_Registry::set('user_role', $this->role);
        }
    }
    
    /***********************************************************************
     * Populate the layout.  See resources.layout.layoutPath in *.ini file.
     **********************************************************************/
    protected function _initBuildLayout() {
    
        // Bind our css for the layout to the view
        $this->bootstrap('layout');
        $this->layout = $this->getResource('layout');
        $this->view = $this->layout->getView();
        
        
        // *******************************************************
        // * Bootstrap front-end framwork 
        // * http://getbootstrap.com/
        // *******************************************************
        $this->view->headLink()->appendStylesheet('/bootstrap/dist/css/bootstrap.min.css','screen, print');
        $this->view->headScript()->appendFile('/js/jquery-1.9.1.min.js');
        $this->view->headScript()->appendFile('/bootstrap/dist/js/bootstrap.min.js');

        $this->view->headScript()->appendFile('/multiselect/js/bootstrap-multiselect.js');

        $this->view->headScript()->appendFile('/multiselect/js/prettify.js');

        $this->view->headScript()->appendFile('/js/jquery-ui-1.10.3.custom.min.js');
        
        // Poplate the base css files
        $this->view->headLink()->appendStylesheet('/css/layout/body.css','screen');    // Bind screen CSS for our layout
        $this->view->headLink()->appendStylesheet('/css/layout/body-print.css','print'); // Bind print CSS for our layout
        $this->view->headLink()->appendStylesheet('/css/layout/header.css','screen');    // Bind screen CSS for our header
        $this->view->headLink()->appendStylesheet('/css/css3pie.css','screen');    // Bind screen CSS for our header
        $this->view->headLink()->appendStylesheet('/multiselect/css/bootstrap-multiselect.css','screen');    // Bind screen CSS for our header
        $this->view->headLink()->appendStylesheet('/css/ui-lightness/jquery-ui-1.10.3.custom.min.css','screen');    // Bind screen CSS for our header

        // User info to the view
        $this->view->user = $this->user;
        
        // http://fortawesome.github.io/Font-Awesome/
        $this->view->headLink()->appendStylesheet('/font-awesome/css/font-awesome.css','screen, print'); 
    
        
                
        // set the default title from the config
        $this->view->app_name = $this->config->application_name;    
        $this->view->title = "Project Name";
    
        // Watermark to show envirment, helpfull so you don't accidently update production.
        $this->view->watermark=false;
        // If watermark is enabled in the config put a background image in the header
        if ($this->config->watermark==1)
            $this->view->watermark="/images/layout/".APPLICATION_ENV.".png";
        //  $this->view->watermark="style=\"background-image:url('/images/layout/".APPLICATION_ENV.".png');background-repeat:repeat-x;background-size:\"";
    
        // Links for the user toolbar.
        $this->log->debug($this->ProfileURL);
        $this->view->LogInOutURL=$this->LogInOutURL;
        $this->view->ProfileURL=$this->ProfileURL;
        
        // Links for the footer.
        $this->view->copyright_company = $this->config->copyright_company;
        $this->view->copyright_link = $this->config->copyright_link;
    }
    
    /***********************************************************************
     * Build the Application menu.
     *********************************************************************/
    protected function _initAppMenu() {
        if ($this->user) {
            $user_app_role_model = new Application_Model_Shared_UserApplication();
            $app_model = new Application_Model_Shared_Application();
            
            // Get All Applications this user has access to
            $row=$user_app_role_model->fetchRow($user_app_role_model->select()
                    ->where('user_id = ?',$this->user['user_id']));
            if ($row) {
                $apps=$row->findDependentRowset($app_model);
            }
            
            $this->view->appMenu = array();
            $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
            foreach($apps as $key=>$app) {
                $this->view->appMenu[$key]['label']=$app['application_nm'];
                // Build out uri
                $this->view->appMenu[$key]['uri']="http://".$app['application_url'].".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
            }
        }
    }

    protected function _initCustomers() {
        if ($this->Signed_in) {
            $customer_model = new Application_Model_Shared_Customer();
            // Get Clients
            $this->customers = $customer_model->getCustomerPairsByUserId($this->user['user_id']);
            if ($this->customers==null) {
                // Error this user has no client for this applicaiton
                echo('Well this is embarrassing.<br>');
                echo('Error: This user has no client for this applicaiton.<br>');
                echo('Please contact the applicaiton development group to fix this.');
                exit();
            }   
            $this->view->header_customers = $this->customers;
            Zend_Registry::set('customers', $this->customers);
        }  
    }

    protected function _initCurrentCustomer() {
        if ($this->Signed_in) {
            
            $this->bootstrap = new Zend_Session_Namespace('bootstrap');
            
            $ValidCustomerIDs = array_keys($this->customers);
            $FirstCustomerID=$ValidCustomerIDs[0];
            
            // Set the client ID if it has not ben set
            if (!isset($this->bootstrap->current_customer)) {
                
                $this->bootstrap->current_customer=$this->customers[$FirstCustomerID];
                $this->bootstrap->current_customer=$FirstCustomerID;
            }
            
            // Yes, I know this is bad form, can make it a bitch to debug.  James
            if (isset($_POST['ClientSelectForm'])&&isset($_POST['ClientSelector'])) {
                if (in_array($_POST['ClientSelector'],$ValidCustomerIDs)) {
                    if ($this->bootstrap->current_customer!=$_POST['ClientSelector']) {    // If we have a customer selection change, the current contex becomes invalid
                        $this->bootstrap->current_customer=$_POST['ClientSelector'];
                        header('Location: /');                                       // so redirect to a context we know is valid, index/index.
                    }
                }
            }
            $this->view->header_current_customer=$this->bootstrap->current_customer;
            
            $customer_model = new Application_Model_Shared_Customer();
            $CustomerRow = $customer_model->find($this->bootstrap->current_customer)->current();
            
            // Set our current customer
            Zend_Registry::set('current_customer', $CustomerRow);
        }
    }

    protected function _initApplications() {
        if ($this->Signed_in) {
            $application_model = new Application_Model_Shared_Application();
            // Get Applications
            $this->apps = $application_model->getApplicationsByUserId($this->user['user_id']);
            Zend_Registry::set('applications', $this->apps);
            
            // Bind application menu to the view
            $this->view->appMenu = array();
            $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
            foreach($this->apps as $key=>$app) {
                $this->view->appMenu[$key]['label']=$app['application_nm'];
                // Build out uri
                $this->view->appMenu[$key]['uri']="http://".$app['application_url'].".".$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1].$app['application_landing_url'];
            }
        }
    }
    
    /***********************************************************************
     * Build the menu.
     **********************************************************************/
    protected function _initNavigation() {
        if ($this->user) {
             
            // Add menu as a resource to the acl
            $this->acl->add(new Zend_Acl_Resource('menu'));
    
            // Bind our menu into the view
            $this->menu = new Zend_Navigation($this->config->menu);
            
            $this->view->registerHelper(new PhpSlickGrid_View_Helper_Menu(), 'menu');
            
            //Zend_Registry::set('navigation', $this->menu);
            //Zend_Registry::set('Zend_Navigation', $this->menu);

            // Load our role into the menue
            $this->view->navigation($this->menu)->setAcl($this->acl)->setRole(trim($this->role->application_role_nm));
    
            // Looks for any navigation pages requiring project_id information and injects
            // the id into the element or removes the element if we have no project_id.
            $pages = $this->menu->findAllBy('params_id', 'PROJECT_ID');
            foreach($pages as &$page){
                if ($this->project_id!=0) {
                    if (method_exists($page, 'getParams')) {
                        $params = $page->getParams();
                        $params['project_id']=$this->project_id;
                        $page->setParams($params);
                    }
                }
                else {
                    $this->menu->removePage($page);
                }
            }
        }
    }
}