<?php defined('SYSPATH') or die('No direct script access.');


class Task_ArchiveObjects extends Minion_Task
{
    protected $_options = array(
        'limit' => 10,
        'sendmail' => TRUE
    );

    protected function _execute(array $params)
    {
        $limit  = $params['limit'];
        $sendmail  = $params['sendmail'];

        $this->post_to_main_domain($params);

    }

    function post_to_main_domain($data)
    {
        $main_domain = "surgut.".Kohana::$config->load("common.main_domain");
        $url = "http://".$main_domain."/static/object_to_archive";
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        Minion_CLI::write( $result );

    }

}