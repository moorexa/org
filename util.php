<?php

// read line
function readInput()
{
    if (PHP_OS == "WINNT") return trim(stream_get_line(STDIN, 1024));
    
    // not windows
    return trim(readline());
}

// ask user 
fwrite(STDOUT, PHP_EOL . 'What do you want to use moorexa for ');
fwrite(STDOUT, PHP_EOL . '1. Website or Web App ');
fwrite(STDOUT, PHP_EOL . '2. API services ');
fwrite(STDOUT, PHP_EOL . PHP_EOL. 'Or Hit Enter to load default >> ');

// read input
$input = readInput();

// option
$default = 0;

if (is_numeric($input)) :
    $option = intval($input);
    if ($option >= 2) $option = 1;
    if ($option <= 1) $option = 0;
    $default = $option;
endif;

// build array
$optionArray = [
    __DIR__ . '/src/web.php',
    __DIR__ . '/src/api.php',
];

// create install file
file_put_contents(__DIR__ . '/install.php', file_get_contents($optionArray[$default]));

// delete all those files
foreach ($optionArray as $file) : 
    unlink($file);
endforeach;

// delete folder
rmdir(__DIR__ . '/src');

// delete this file
unlink(__FILE__);