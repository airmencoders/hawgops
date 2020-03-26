<?php
/**
 * This API is used for the Hawg-Ops website at https://hawg-ops.com
 *
 * It is written and maintained by @chris-m92, "Porkins"
 * It is owned by AirmenCoders and the U.S. Air Force
 * 
 * https://github.com/airmencoders/hawgops.git
 * https://github.com/airmencoders/hawgops/req/all/codes-v2.php
 * 
 * LICENSE: MIT
 * Copyright (c) 2020 Airmen Coders
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal 
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all 
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE 
 * SOFTWARE.
 * 
 * @author 		Porkins
 * @copyright 	2020 Airmen Coders
 * @license 	MIT
 * @version 	2.0.0
 */

/**
 * HTTP Codes
 * 
 * Used for logging purposes.
 * 
 * @since 	1.0.0
 * @since	2.0.0	Shortened names for easier coding
 */
$H_OK = "HTML/200";
$H_NOT_FOUND = "HTML/404";
$H_SERVER_ERROR = "HTML/500";

/**
 * Success Codes (1xx)
 * 
 * Used to show green alert containers, providing feedback to user.
 * Uses the Bootstrap "success" contextual class.
 * 
 * @since 	1.0.0
 * @since 	2.0.0	Shortened names for easier coding, consolidated codes.
 */
$S_ACNT_CREATED 		= 100;
$S_ACNT_DELETED 		= 101;
$S_ACNT_DISABLED 		= 102;
$S_ACNT_ENABLED 		= 103;
$S_ADMIN_GRANTED 		= 104;
$S_ADMIN_REVOKED 		= 105;
$S_EMAIL_SENT			= 106;
$S_NAME_CHANGED 		= 107;
$S_PSWD_CHANGED		 	= 108;
$S_SCEN_DELETED 		= 109;
$S_SCEN_SAVED 			= 110;
$S_SCEN_UPDATED 		= 111;
$S_TOKEN_DELETED		= 113;
$S_TOKEN_VALID		 	= 112;
$S_USER_AUTHENTICATED 	= 114;

/**
 * Error Codes (2xx)
 * 
 * Used to show red alert containers, providing feedback to user.
 * Uses the Bootstrap "danger" contextual class.
 * 
 * @since 	1.0.0
 * @since 	2.0.0	Shortened names for easier coding, consolidated codes.
 */
$E_ACNT_DISABLED		= 200;
$E_ACNT_DOESNT_EXIST	= 201;
$E_ACNT_EXISTS			= 202;
$E_CPSWD_DOESNT_MATCH	= 203;
$E_CPWSD_NOT_RCVD		= 204;
$E_CRITERIA_NOT_RCVD	= 205;
$E_EMAIL_NOT_RCVD		= 206;
$E_EMAIL_NOT_SENT		= 207;
$E_FNAME_NOT_RCVD 		= 208;
$E_LNAME_NOT_RCVD 		= 209;
$E_MODE_INVALID			= 210;
$E_MODE_NOT_RCVD		= 211;
$E_MSG_NOT_RCVD			= 212;
$E_MYSQL 				= 213;
$E_NAME_NOT_RCVD		= 214;
$E_PSWD_INVALID			= 215;
$E_PSWD_NOT_RCVD		= 216;
$E_PSWD_TOO_SHRT		= 217;
$E_PSWD_TOO_SMPL		= 218;
$E_SCEN_DATA_NOT_RCVD	= 219;
$E_SCEN_DOESNT_EXIST	= 220;
$E_SCEN_ID_NOT_RCVD		= 221;
$E_SCEN_NAME_NOT_RCVD	= 222;
$E_TOKEN_EXPIRED		= 223;
$E_TOKEN_INVALID		= 224;
$E_TOKEN_NOT_RCVD		= 225;
$E_UNAUTHORIZED 		= 226;
$E_USER_ID_NOT_RCVD 	= 227;
?>