<?php
function handle($key, $namespace, $output = 'composer.json')
{
    $file = 'composer.json';
    $data = json_decode(file_get_contents($file), true);
    $data["autoload"]["psr-4"] = array($key => $namespace);
    file_put_contents($output, json_encode($data, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
}

handle('Nickyeoman\\Framework\\Controller\\','controllers/');

