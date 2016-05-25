<?php

    define('PERCH_LICENSE_KEY', 'P21209-PLB932-JYP401-NET749-JUN007');

    define("PERCH_DB_USERNAME", 'ryandev');
    define("PERCH_DB_PASSWORD", 'Decarlo2012');
    define("PERCH_DB_SERVER", "ryandev.db.2139840.hostedresource.com");
    define("PERCH_DB_DATABASE", "ryandev");
    define("PERCH_DB_PREFIX", "perch2_");

    define('PERCH_EMAIL_FROM', 'heydanwaz@gmail.com');
    define('PERCH_EMAIL_FROM_NAME', 'Dan Wasilewski');

    define('PERCH_LOGINPATH', '/perch');
    define('PERCH_PATH', str_replace(DIRECTORY_SEPARATOR.'config', '', dirname(__FILE__)));
    define('PERCH_CORE', PERCH_PATH.DIRECTORY_SEPARATOR.'core');


    define('PERCH_RESFILEPATH', PERCH_PATH . DIRECTORY_SEPARATOR . 'resources');
    define('PERCH_RESPATH', PERCH_LOGINPATH . '/resources');

    define('PERCH_HTML5', true);

?>