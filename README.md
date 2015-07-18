# FORMS MODULE FOR FUEL CMS
This is a [FUEL CMS](http://www.getfuelcms.com) forms module for easily adding form functionality to your website.

## INSTALLATION
There are a couple ways to install the module. If you are using GIT you can use the following method
to create a submodule:

### USING GIT
1. Open up a Terminal window, "cd" to your FUEL CMS installation then type in: 
Type in:
``php index.php fuel/installer/add_git_submodule https://github.com/daylightstudio/FUEL-CMS-Forms-Module.git forms``

2. Then to install, type in:
``php index.php fuel/installer/install forms``


### MANUAL
1. Download the zip file from GitHub:
[https://github.com/daylightstudio/FUEL-CMS-Forms-Module](https://github.com/daylightstudio/FUEL-CMS-Forms-Module)

2. Create a "forms" folder in fuel/modules/ and place the contents of the forms module folder in there.

3. Import the forms_install.sql from the forms/install folder into your database

4. Add "forms" to the the `$config['modules_allowed']` in fuel/application/config/MY_fuel.php

## UNINSTALL

To uninstall the module which will remove any permissions and database information:
``php index.php fuel/installer/uninstall forms``

### TROUBLESHOOTING
1. You may need to put in your full path to the "php" interpreter when using the terminal.
2. You must have access to an internet connection to install using GIT.


## DOCUMENTATION
To access the documentation, you can visit it [here](http://docs.getfuelcms.com/modules/forms).

## TEAM
* David McReynolds, Daylight Studio, Main Developer

## BUGS
To file a bug report, go to the [issues](https://github.com/daylightstudio/FUEL-CMS-Forms-Module/issues) page.

## LICENSE
The Forms Module for FUEL CMS is licensed under [APACHE 2](http://www.apache.org/licenses/LICENSE-2.0).