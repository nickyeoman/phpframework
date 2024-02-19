<?php

use Tracy\Debugger;

// Enable Tracy debugger if DEBUG is set to 'display'
if ($_ENV['DEBUG'] === 'display') {
    Debugger::enable(Debugger::DEVELOPMENT);
}