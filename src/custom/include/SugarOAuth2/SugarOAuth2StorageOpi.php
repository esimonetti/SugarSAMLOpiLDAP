<?php

// Enrico Simonetti
// enricosimonetti.com
//
// 2017-06-08
// Tested on Sugar 7.9.0.0
//
// Note: the original content of SugarOAuth2StorageBase->checkUserCredentials might have to be adapted for previous and future versions of Sugar
//       the custom code is enclosed within comments


class SugarOAuth2StorageOpi extends SugarOAuth2StorageBase
{
    public function checkUserCredentials(IOAuth2GrantUser $storage, $client_id, $username, $password)
    {
        // START - CUSTOM CODE

        global $sugar_config;
        $ldap_config = Administration::getSettings('ldap');

        // only customise/override the behaviour if SAML is currently active and LDAP settings are enabled, otherwise call parent
        if(
            empty($sugar_config['authenticationClass']) || $sugar_config['authenticationClass'] != 'SAMLAuthenticate' ||
            (
                $sugar_config['authenticationClass'] == 'SAMLAuthenticate' &&
                (
                    empty($ldap_config->settings['ldap_hostname']) ||
                    empty($ldap_config->settings['ldap_port']) ||
                    empty($ldap_config->settings['ldap_bind_attr']) ||
                    empty($ldap_config->settings['ldap_login_attr']) ||
                    empty($ldap_config->settings['ldap_base_dn'])
                )
            )
        ) {
            // call parent method
            $GLOBALS['log']->debug('Custom SugarOAuth2StorageBaseOpi - Executing core code');
            return parent::checkUserCredentials($storage, $client_id, $username, $password);
        } else {
            // call ldap authentication if LDAP is setup and SAML is active
            $GLOBALS['log']->debug('Custom SugarOAuth2StorageBaseOpi - Forcing LDAP lookup for Outlook Plugin');
            $auth = AuthenticationController::getInstance('LDAPAuthenticate');
        }

        // END - CUSTOM CODE
        
        $clientInfo = $storage->getClientDetails($client_id);
        if ( $clientInfo === false ) {
            return false;
        }

        // START - CUSTOM CODE

        // comment out this section as we already initiated the LDAP version of $auth or called the parent method

        // Is just a regular Sugar User
        //$auth = AuthenticationController::getInstance();

        // END - CUSTOM CODE

        // noHooks since we'll take care of the hooks on API level, to make it more generalized
        $loginSuccess = $auth->login($username,$password,array('passwordEncrypted'=>false,'noRedirect'=>true, 'noHooks'=>true));
        if ( $loginSuccess && !empty($auth->nextStep) ) {
            // Set it here, and then load it in to the session on the next pass
            // TODO: How do we pass the next required step to the client via the REST API?
            $GLOBALS['nextStep'] = $auth->nextStep;
        }

        if ( $loginSuccess ) {
            $this->userBean = $this->loadUserFromName($username);
            return array('user_id' => $this->userBean->id);
        } else {
            if(!empty($_SESSION['login_error'])) {
                $message = $_SESSION['login_error'];
            } else {
                $message = null;
            }
            throw new SugarApiExceptionNeedLogin($message);
        }
    }
}
