First, download and install XAMPP and MySQL Workbench from Google.
After installation is complete, download both ZIP files:
dump
suleman
Extract both ZIP files.

Take the suleman folder and paste it into:

C:\xampp\htdocs
Database Setup
Open MySQL Workbench.
Click on the “+” (Add Connection) icon:
Connection Name: localhost
Click OK
If it asks for a password, leave it empty and continue.

Open a new SQL tab and run the following:

create database sulemanrestro;
use sulemanrestro;
Now open the dump folder, and one by one:
Copy each SQL file content
Paste it into MySQL Workbench
Click Run
Run the Project
Open XAMPP Control Panel and start:
Apache
MySQL
Open your browser and go to:

User Panel:

http://localhost/suleman

Admin Panel:

http://localhost/suleman/admin
Admin Login
Username: admin
Password: admin123
