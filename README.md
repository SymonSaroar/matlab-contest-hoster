# Simple platform for hosting a matlab contest

# Required:
* MATLAB 
* MATLAB runtime for python
* python 2
* php

# Overview
The hoster uses a google form to take submissions.
The leaderboard is a webpage hosted locally. Overall procedure can be devided into three parts.
### Fetch Submitted code, save and judge them.
This is php script that should always run. This hooks on to the submission google sheet and continuously checks for any updated and submissions and pulls them. After saving a .m file this script runs the matlab engine to run the test script. From the output this php script also updates the verdict in a mySQL database which is shared across the server.
### Save the verdict in local excel sheet.
Previous script also saves the verdict locally for backup.
### Read database and update the leaderboard webpage
The index.php reads the database and constantly updates the webpage.

# General Procedure
1. Install XAMPP
2. Install Composer
3. Run `composer update` in ./src/
4. Get authentication token from google api.
5. Create a mySQL database using phpMyAdmin named 'substatus'.
6. start MATLAB parallel processing.
7. run `php ./src/getDriveFiles.php` in a terminal.
8. start the apache webserver.
9. Test everything is working

# Things to consider
1. Properly set apache denylist.
2. add credientials and auth tokens to github ignorelist.
3. use seperate file for database credentials , and add that to github ignorelist.
4. Use online server for the webpage.
   1. Google sheet as the leaderboard. (in development - `updateLeaderboard.php`)