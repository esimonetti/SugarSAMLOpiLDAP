# SugarSAMLOpiLDAP
When SSO SAML authentication is enabled, bind the credentials against LDAP for the Outlook Plugin (OPI) instead of local Sugar database credentials

## Notes
This customisation applies to you, if your Sugar system has a SSO SAML based solution for authentication that sources and/or synchronises periodically users credentials from LDAP, and you have the need authentication the Outlook Plugin (OPI) to archive emails against the LDAP credentials.

## Requirements
* Tested on Sugar 7.9.0.0
* Tested with Okta SSO SAML
* Tested with OpenLDAP

## Solution description

The solution provided is a mix between LDAP authentication (for the Outlook Plugin) and SSO SAML authentication (for Web UI and Mobile). The assumption is that the SAML provider sources/syncs the users from LDAP. This customisation overcomes the need of local Sugar authentication when accessing the Outlook Plugin.<br/>

It will give the admin users the possibility of having credentials provisioned outside Sugar, for all the regular users. To guarantee that scenario, it is recommended to set the "SAMLAuthenticate Only" attribute on each of the user's profiles.<br/>

The assumption is that the LDAP and the SAML solutions are already setup correctly, and the Sugar users have been provisioned correctly as well.<br/>
To set up the customisation, deploy the files and repair the system. The admin user then needs to setup correctly LDAP on Sugar UI and test that it works correctly. The LDAP settings would look similar to the below openldap test setup:<br/>

![Sugar LDAP test settings](https://raw.githubusercontent.com/esimonetti/SugarSAMLOpiLDAP/master/sugar_ldap.png)

Now it is possible for the admin user to switch to the SSO/SAML setup. Once it is all working correctly with the SAML solution, and the SAML users are sourced from LDAP, test the Outlook Plugin authentication. It will now log-in with the matching LDAP credentials when leveraging the Outlook Plugin.<br/>

A screenshot of the test LDAP tree can be seen below:<br/>

![OpenLDAP tree](https://raw.githubusercontent.com/esimonetti/SugarSAMLOpiLDAP/master/openldap.png)

A screenshot of an OPI postman login response can be seen below:<br/>

![Postman request](https://raw.githubusercontent.com/esimonetti/SugarSAMLOpiLDAP/master/postman.png)

## Installation
* Copy the full folder structure within `src` to your Sugar system
* Run a quick repair and rebuild
* Configure correctly and test LDAP first
* Configure correctly and test SAML without removing the previously configured LDAP settings

## Debugging
As it is not immediate to debug LDAP authentication, if the users encounter issues it is recommended to disable temporarily SAML and enable LDAP only, to test if the user can log-in correctly. If not, there must be some configuration mismatch on the LDAP section.
