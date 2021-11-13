/*Script creates required users in database

Note: It is assumed the database runs on the same server as the webpage, if not change 'localhost' to '%'
If running the database on the observer device change '%' to 'localhost' for security

Author kookakanga (Bryce)
Last updated Nov 2021*/

/*Permissions for program taking observations (Python code)*/
CREATE USER 'observer'@'%' IDENTITIED BY 'myPassword';
GRANT INSERT ON 'weather'.* TO 'observer'@'%';

/*Permissions for program reading observations (PHP code)*/
CREATE USER 'selecter'@'localhost' IDENTITIED BY 'myPassword';
GRANT SELECT ON 'weather'.* TO 'selecter'@'localhost';

FLUSH PRIVILEGES;