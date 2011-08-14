<?php
if (!is_dir($vendorDir = dirname(__FILE__).'/vendor')) {
    mkdir($vendorDir, 0777, true);
}

$deps = array(
    array('doctrine', 'http://github.com/doctrine/doctrine2.git', '2.1.0'),
    array('doctrine-dbal', 'http://github.com/doctrine/dbal.git', '2.1.0'),
    array('doctrine-common', 'http://github.com/doctrine/common.git', '2.1.0'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;
    echo "> Installing/Updating $name\n";
    $installDir = $vendorDir.'/'.$name;
    if (!is_dir($installDir)) {
        system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }
    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
