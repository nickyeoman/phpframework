<?php

namespace Nickyeoman\Framework\Classes;

use Nickyeoman\Dbhelper\Dbhelp as DB;

class Logger {
    
    public function __construct() {
        // Needs session I think
    }

    public function log($level = 'DEBUG', $title = 'Called log', $location = 'Base Controller', $content = "NULL") {
        if ($_ENV['LOGGING'] == 'mysql') {
            $post = $this->getPostData();

            $log = array(
                'level'     => strtoupper($level),
                'title'     => $title,
                'content'   => $content,
                'location'  => $location,
                'ip'        => $this->viewData->data['ip'],
                'url'       => $this->viewData->data['uri'],
                'session'   => json_encode($this->sessionManager->data),
                'post'      => $post,
                'time'      => date('Y-m-d H:i:s') // Use current timestamp
            );

            $this->saveLogToDatabase($log);
        }
    }

    private function getPostData() {
        if ($this->requestManager->submitted) {
            // Remove sensitive data from $_POST before logging
            $post = $_POST;
            unset($post['password']); // Assuming password is sensitive data
            return json_encode($post);
        } else {
            return null;
        }
    }

    private function saveLogToDatabase($log) {
        $DB = new DB();
        $DB->create('logs', $log);
        $DB->close();
    }
}
