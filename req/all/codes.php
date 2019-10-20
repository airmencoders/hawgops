<?php
    //===============================
    // 1xxxx - Primary (Dark Blue)
    //===============================
    // 101xx - API
    //-------------------------------

    //===============================
    // 2xxxx - Secondary (Light Gray)
    //===============================
    // 201xx - API
    //-------------------------------

    //===============================
    // 3xxxx - Success (Green)
    //===============================
    // 301xx - API
	//-------------------------------
	$API_CREATE_ACCOUNT_ACCOUNT_CREATED = 30100;	
	$API_LOGIN_USER_AUTHENTICATED = 30101;	
	$API_SAVE_SCENARIO_SCENARIO_SAVED = 30102;
	$API_DELETE_SCENARIO_SCENARIO_DELETED = 30103;
	
	//-------------------------------
	// 302xx - CONTACT-DO.PHP
	//-------------------------------
	$DO_CONTACT_EMAIL_SENT = 30200;

	//-------------------------------
	// 303xx - SHARE-SCENARIO-DO.PHP
	//-------------------------------
	$DO_SHARE_SCENARIO_EMAIL_SENT = 30300;

    //===============================
    // 4xxxx - Danger (Red)
    //===============================
    // 400xx - SHARED
	//-------------------------------
	$ERROR_MYSQL = 40000;
	$ERROR_UNAUTHORIZED = 40001;

	//-------------------------------
	// 401xx - API
    //-------------------------------
	$API_CREATE_ACCOUNT_FNAME_NOT_RECEIVED = 40100;
	$API_CREATE_ACCOUNT_LNAME_NOT_RECEIVED = 40101;
	$API_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED = 40102;
	$API_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED = 40103;
	$API_CREATE_ACCOUNT_ACCOUNT_EXISTS = 40104;

	$API_LOGIN_EMAIL_NOT_RECEIVED = 40105;
	$API_LOGIN_PASSWORD_NOT_RECEIVED = 40106;
	$API_LOGIN_ACCOUNT_DOES_NOT_EXIST = 40107;
	$API_LOGIN_ACCOUNT_DISABLED = 40108;
	$API_LOGIN_INCORRECT_PASSWORD = 40109;
	
	$API_SAVE_SCENARIO_USER_ID_NOT_RECEIVED = 40110;
	$API_SAVE_SCENARIO_DATA_NOT_RECEIVED = 40111;
	
	$API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED = 40112;
	$API_GET_SCENARIO_SCENARIO_DOES_NOT_EXIST = 40113;
	
	$API_DELETE_SCENARIO_ID_NOT_RECEIVED = 40114;
	$API_DELETE_SCENARIO_SCENARIO_DOES_NOT_EXIST = 40115;
	
	//-------------------------------
	// 402xx - CONTACT-DO.PHP
	//-------------------------------
	$DO_CONTACT_DATA_NOT_RECEIVED = 40200;
	$DO_CONTACT_NAME_NOT_RECEIVED = 40201;
	$DO_CONTACT_EMAIL_NOT_RECEIVED = 40202;
	$DO_CONTACT_MESSAGE_NOT_RECEIVED = 40203;
	$DO_CONTACT_MESSAGE_NOT_SENT = 40204;

	//-------------------------------
	// 403xx - CREATE-ACCOUNT-DO.PHP
	//-------------------------------
	$DO_CREATE_ACCOUNT_DATA_NOT_RECEIVED = 40300;
	$DO_CREATE_ACCOUNT_FNAME_NOT_RECEIVED = 40301;
	$DO_CREATE_ACCOUNT_LNAME_NOT_RECEIVED = 40302;
	$DO_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED = 40303;
	$DO_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED = 40304;
	$DO_CREATE_ACCOUNT_CONFIRM_PASSWORD_NOT_RECEIVED = 40305;
	$DO_CREATE_ACCOUNT_PASSWORD_TOO_SHORT = 40306;
	$DO_CREATE_ACCOUNT_PASSWORD_TOO_SIMPLE = 40307;
	$DO_CREATE_ACCOUNT_PASSWORDS_DO_NOT_MATCH = 40308;
	
	//-------------------------------
	// 404xx - DEL-SCENARIO-DO.PHP
	//-------------------------------
	$DO_DEL_SCENARIO_ID_NOT_RECEIVED = 40400;
	
	//-------------------------------
	// 405xx - SHARE-SCENARIO-DO.PHP
	//-------------------------------
	$DO_SHARE_SCENARIO_NAME_NOT_RECEIVED = 40500;
	$DO_SHARE_SCENARIO_ID_NOT_RECEIVED = 40501;
	$DO_SHARE_SCENARIO_EMAIL_NOT_RECEIVED = 40502;
	$DO_SHARE_SCENARIO_EMAIL_NOT_SENT = 40503;

    //===============================
    // 5xxxx - Warning (Yellow)
    //===============================
    // 501xx - API
    //-------------------------------

    //===============================
    // 6xxxx - Info (Light Blue)
    //===============================
    // 601xx - API
    //-------------------------------

?>