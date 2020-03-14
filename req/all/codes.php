<?php
	//===============================
	// HTTP CODES
	//===============================
	$HTTP_OK = "HTML/200";
	$HTTP_NOT_FOUND = "HTML/404";
	$HTTP_SERVER_ERROR = "HTML/500";
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
	$API_DISABLE_ACCOUNT_ACCOUNT_DISABLED = 30104;
	$API_ENABLE_ACCOUNT_ACCOUNT_ENABLED = 30105;
	$API_GRANT_ADMIN_ADMIN_GRANTED = 30106;
	$API_REVOKE_ADMIN_ADMIN_REVOKED = 30107;
	$API_VALIDATE_RECOVERY_TOKEN_TOKEN_VALID = 30108;
	$API_CHANGE_PASSWORD_PASSWORD_CHANGED = 30109;
	
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
	$ERROR_ADMIN_SCENARIOS_USER_ID_NOT_RECEIVED = 40002;
	$ERROR_IPLOG_USER_NOT_RECEIVED = 40003;

	//-------------------------------
	// 401xx - API
    //-------------------------------
	// CREATE ACCOUNT
	$API_CREATE_ACCOUNT_FNAME_NOT_RECEIVED = 40100;
	$API_CREATE_ACCOUNT_LNAME_NOT_RECEIVED = 40101;
	$API_CREATE_ACCOUNT_EMAIL_NOT_RECEIVED = 40102;
	$API_CREATE_ACCOUNT_PASSWORD_NOT_RECEIVED = 40103;
	$API_CREATE_ACCOUNT_ACCOUNT_EXISTS = 40104;
	
	// LOGIN
	$API_LOGIN_EMAIL_NOT_RECEIVED = 40105;
	$API_LOGIN_PASSWORD_NOT_RECEIVED = 40106;
	$API_LOGIN_ACCOUNT_DOES_NOT_EXIST = 40107;
	$API_LOGIN_ACCOUNT_DISABLED = 40108;
	$API_LOGIN_INCORRECT_PASSWORD = 40109;
	$API_LOGIN_DISABLING_ACCOUNT = 40125;
	
	// SAVE SCENARIO
	$API_SAVE_SCENARIO_USER_ID_NOT_RECEIVED = 40110;
	$API_SAVE_SCENARIO_SCENARIO_ID_NOT_RECEIVED = 40126;
	$API_SAVE_SCENARIO_DATA_NOT_RECEIVED = 40111;
	
	// GET SCENARIO
	$API_GET_SCENARIO_SCENARIO_ID_NOT_RECEIVED = 40112;
	$API_GET_SCENARIO_SCENARIO_DOES_NOT_EXIST = 40113;
	
	// DELETE SCENARIO
	$API_DELETE_SCENARIO_ID_NOT_RECEIVED = 40114;
	$API_DELETE_SCENARIO_SCENARIO_DOES_NOT_EXIST = 40115;
	
	// DISABLE/ENABLE ACCOUNT
	$API_DISABLE_ACCOUNT_ID_NOT_RECEIVED = 40116;
	$API_ENABLE_ACCOUNT_ID_NOT_RECEIVED = 40117;
	
	// GET USER EMAIL BY ID
	$API_GET_USER_EMAIL_BY_ID_ID_NOT_RECEIVED = 40118;
	$API_GET_USER_EMAIL_BY_ID_ACCOUNT_DOES_NOT_EXIST = 40119;
	
	// GET USER NAME BY EMAIL
	$API_GET_USER_NAME_BY_EMAIL_EMAIL_NOT_RECEIVED = 40120;
	$API_GET_USER_NAME_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST = 40121;
	
	// GET USER NAME BY ID
	$API_GET_USER_NAME_BY_ID_ID_NOT_RECEIVED = 40122;
	$API_GET_USER_NAME_BY_ID_ACCOUNT_DOES_NOT_EXIST = 40123;
	
	// IS LOGGED IN
	$API_IS_LOGGED_IN_SESSION_ID_INVALID = 40124;

	// GET IPLOG
	$API_GET_IPLOG_ID_NOT_RECEIVED = 40125;
	
	// GRANT/REVOKE ADMIN
	$API_GRANT_ADMIN_ID_NOT_RECEIVED = 40126;
	$API_REVOKE_ADMIN_ID_NOT_RECEIVED = 40127;

	// RECOVER ACCOUNT
	$API_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED = 40128;
	$API_RECOVER_ACCOUNT_ACCOUNT_DOES_NOT_EXIST = 40129;

	// VALIDATE RECOVERY TOKEN
	$API_VALIDATE_RECOVERY_TOKEN_EMAIL_NOT_RECEIVED = 40130;
	$API_VALIDATE_RECOVERY_TOKEN_TOKEN_NOT_RECEIVED = 40131;
	$API_VALIDATE_RECOVERY_TOKEN_COMBINATION_DOES_NOT_EXIST = 40132;
	$API_VALIDATE_RECOVERY_TOKEN_TOKEN_EXPIRED = 40133;

	// GET USER ID BY EMAIL
	$API_GET_USER_ID_BY_EMAIL_EMAIL_NOT_RECEIVED = 40134;
	$API_GET_USER_ID_BY_EMAIL_ACCOUNT_DOES_NOT_EXIST = 40135;

	// CHANGE PASSWORD
	$API_CHANGE_PASSWORD_KEYS_NOT_RECEIVED = 40136;
	$API_CHANGE_PASSWORD_NEW_PASSWORD_NOT_RECEIVED = 40137;
	$API_CHANGE_PASSWORD_COULD_NOT_GET_USER_ID = 40138;
	$API_CHANGE_PASSWORD_COULD_NOT_ENABLE_ACCOUNT = 40139;
	
	//-------------------------------
	// 402xx - CONTACT-DO.PHP
	//-------------------------------
	$DO_CONTACT_DATA_NOT_RECEIVED = 40200;
	$DO_CONTACT_NAME_NOT_RECEIVED = 40201;
	$DO_CONTACT_EMAIL_NOT_RECEIVED = 40202;
	$DO_CONTACT_MESSAGE_NOT_RECEIVED = 40203;
	$DO_CONTACT_MESSAGE_NOT_SENT = 40204;
	$DO_CONTACT_TOKEN_NOT_RECEIVED = 40205;
	$DO_CONTACT_ACTION_NOT_RECEIVED = 40206;

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
	
	//-------------------------------
	// 406xx - USER-TOGGLE-ENABLED.PHP
	//-------------------------------
	$DO_TOGGLE_ENABLED_USER_NOT_RECEIVED = 40600;
	$DO_TOGGLE_ENABLED_ACTION_NOT_RECEIVED = 40601;
	$DO_TOGGLE_ENABLED_INVALID_ACTION = 40602;
	
	//-------------------------------
	// 407xx - USER-TOGGLE-ADMIN.PHP
	//-------------------------------
	$DO_TOGGLE_ADMIN_USER_NOT_RECEIVED = 40700;
	$DO_TOGGLE_ADMIN_ACTION_NOT_RECEIVED = 40701;
	$DO_TOGGLE_ADMIN_INVALID_ACTION = 40702;

	//-------------------------------
	// 408xx - RECOVER-ACCOUNT-DO.PHP
	//-------------------------------
	$DO_RECOVER_ACCOUNT_EMAIL_NOT_RECEIVED = 40800;
	$DO_RECOVER_ACCOUNT_EMAIL_NOT_SENT = 40801;
	$DO_RECOVER_ACCOUNT_EMAIL_SENT = 40802;

	//-------------------------------
	// 409xx - LOGIN-DO.PHP
	//-------------------------------
	$DO_LOGIN_EMAIL_NOT_RECEIVED = 40900;
	$DO_LOGIN_PASSWORD_NOT_RECEIVED = 40901;

	//-------------------------------
	// 410xx - RESET-PASSWORD-DO.PHP
	//-------------------------------
	$DO_RESET_PASSWORD_EMAIL_NOT_RECEIVED = 41000;
	$DO_RESET_PASSWORD_TOKEN_NOT_RECEIVED = 41001;
	$DO_RESET_PASSWORD_NEW_PASSWORD_NOT_RECEIVED = 41002;
	$DO_RESET_PASSWORD_CONFIRM_PASSWORD_NOT_RECEIVED = 41003;
	$DO_RESET_PASSWORD_PASSWORDS_DO_NOT_MATCH = 41004;
	$DO_RESET_PASSWORD_PASSWORD_TOO_SHORT = 41005;
	$DO_RESET_PASSWORD_PASSWORD_TOO_SIMPLE = 41006;
?>