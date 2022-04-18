n order to connect remotely, you have to have MySQL bind port 3306 to your machine's IP address in my.cnf. Then you have to have created the user in both localhost and '%' wildcard and grant permissions on all DB's as such . See below:

my.cnf (my.ini on windows)

#Replace xxx with your IP Address 
bind-address        = xxx.xxx.xxx.xxx
Then:

CREATE USER 'myuser'@'localhost' IDENTIFIED BY '!abc123@';
CREATE USER 'myuser'@'%' IDENTIFIED BY '!abc123@';
Then:

GRANT ALL ON *.* TO 'myuser'@'localhost';
GRANT ALL ON *.* TO 'myuser'@'%';
FLUSH PRIVILEGES;

34.125.185.125

my.cnf

!includedir /etc/mysql/conf.d/
!includedir /etc/mysql/mysql.conf.d/


try install phpmyadmin
https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-phpmyadmin-on-ubuntu-20-04


https://www.youtube.com/watch?v=19v0wRP_Uz8
