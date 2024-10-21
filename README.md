# Online_Courses-App
How To Add Authentication to Your RESTful API with Php
Some familiarity with apache2. Apache2/ Ngnix installed on a local environment. Knowledge of Basic Linux Navigation and File Management. Here is a diagram to provide a sense of what the file structure of the project will look like once you have completed the task:
## step1: Installing dependencies
 > sudo nano etc/apache2/sites-available/timo.conf 
<VirtualHost *:80>
     # Add machine's IP address (use ifconfig command)
     ServerName 192.168.41.201
     <Directory /home/timothy/timothy/>
     		# set permissions as per apache2.conf file
            Options FollowSymLinks
            AllowOverride None
            Require all granted
     </Directory>

     <Directory /var/www/html/Online-Courses-App/api>
        AllowOverride All
        Require all granted
     </Directory>

     ErrorLog ${APACHE_LOG_DIR}/error.log
     LogLevel warn
     CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

 > sudo systemctl start apache2
 > sudo systemctl status apache2
 > sudo tail -f /var/log/apache2/error.log


# Online-Courses-App
├── api
│   ├── Applicant.php
│   ├── Application.php
│   ├── Course.php
│   └── Intake.php
├── config
│   └── database.php // provide the correct credentials
├── controllers
│   ├── ApplicantController.php
│   ├── ApplicationsController.php
│   └── LoginController.php
├── database.sql
├── public
│   ├── application.php
│   ├── courseId.php
│   ├── dashboard.php
│   ├── index.html
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   └── styles.css
└── README.md


## step 2 Creating database user
 > mysql -u root -p < database.sql

## Step — 3 Execute the Project
Now, in a web browser, you can navigate to the possible URLs and see the text returned 
For Example: http://localhost/Online-Courses-App/public/register.php

## Step 4 Postman Collection
Open postman application
RESTful API collection
This api contains collection requests from localhost

It contains the following requests

  api/Applicant.php //CRUD Applicant
  api/Application.php //CRUD pplication
  api/Courses.php //CRUD Course
  api/Intake.php //CRUD Intake

