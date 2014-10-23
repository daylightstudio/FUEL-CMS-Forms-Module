<?php 
$config['name'] = 'Forms Module';
$config['version'] = FORMS_VERSION;
$config['author'] = 'David McReynolds';
$config['company'] = 'Daylight Studio';
$config['license'] = 'Apache 2';
$config['copyright'] = '2014';
$config['author_url'] = 'http://www.thedaylightstudio.com';
$config['description'] = 'A FUEL CMS module to easily create forms and track form entries.';
$config['compatibility'] = '1.0';
$config['instructions'] = 'forms';
$config['permissions'] = array('forms', 'form_entries');
$config['migration_version'] = 0;
$config['install_sql'] = 'forms_install.sql';
$config['uninstall_sql'] = 'forms_uninstall.sql';
$config['repo'] = 'git://github.com/daylightstudio/FUEL-CMS-Forms-Module.git';