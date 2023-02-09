# opensimscripts

<h2>Donations</h2>

I hate asking for money.

I have been helping grid owners set things up - this takes a lot of my time so if you would like to make a donation you can do so at <a href="https://grid.wolfterritories.org">https://grid.wolfterritories.org</a>, if you find this stuff or my help useful.

<h1>How it works</h1>

So this system works by having a template folder called "Template" which has a template version of OpenSim in it, the INI files OpenSim.ini and GridCommon.ini are edited with fields that are replaced when the system runs.

The system takes the template and copies it into a regions folder in the folder you specify in config.php - note that you should create a config folder and use exampleconfig.php and rename it to config.php and have a copy in the regions folder, edit that first.

The OpenSim template is avialable on request from Lone Wolf#8120 on discord. Please note it will need changing to your grid set up. 

<h1>Robust</h2>

I run 10 robust servers using an Apache load balancer. You can find details on my YouTube channel <a href="https://www.youtube.com/channel/UCzyzYXxLm7lPPCFUnIlcxwg">Here!</a>

If you get to more than 100 regions you'll be wanting to implement this set up - again talk to me on Discord. IT's a security risk for me to give out this information. 


<h1>The code</h1>

SO this is some scripts I use for running opensim.

You'll need a database called grid and a table called regions - this is where the regions are stored, also create config.php from exampleconfig.php

Then have a look through the scripts

php runregion.php copies the template folder in with the ini files it needs and then changes them and fires up an opensim region with it. This is using opensim ngc. 

This is incomplete and is just here for interest. The live running services if I give it out would compromise my grid. But the idea is you have a template folder with ini files in them with things that get replaced.




<pre>
CREATE TABLE `regions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `regionname` varchar(200) DEFAULT NULL,
  `servername` varchar(255) DEFAULT NULL,
  `xpos` int(200) DEFAULT NULL,
  `ypos` int(200) DEFAULT NULL,
  `estatename` varchar(200) DEFAULT NULL,
  `owner` varchar(200) DEFAULT NULL,
  `shortname` varchar(200) DEFAULT NULL,
  `createdate` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uuid` varchar(200) DEFAULT uuid(),
  `port` int(80) DEFAULT NULL,
  `databasename` varchar(79) DEFAULT NULL,
  `nocopy` varchar(1) DEFAULT 'N',
  `params` varchar(255) DEFAULT 'ulimit -s 262144',
  `paypalemail` varbinary(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `regionname` (`regionname`)
) ENGINE=InnoDB AUTO_INCREMENT=1375 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
</pre>

My latest routine

rungridmulti.php 

Will run multiple instances of opensim on the same simulator which saves a HUGE amount of resources *BUT* you have to start with a default user and estate so if you are creating multple different estates and regions you have to create them then assign them over.


This is very much a work in progress, so please use what you find if it helps :-)

My regions are in a folder called

regions

for the multi run you need to create

regions/master/bin

before running

Lone Wolf
Wolf Territories Grid


