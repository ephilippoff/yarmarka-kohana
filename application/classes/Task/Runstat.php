<?php defined('SYSPATH') or die('No direct script access.');


class Task_Runstat extends Minion_Task
{
    protected $_options = array(
        'limit' => 1000,
        'type'  => NULL,
    );

    protected function _execute(array $params)
    {
        $limit  = $params['limit'];
        $type   = $params['type'];

        $this->fetch_search();
        $this->fetch_object_visits();
    }

    function fetch_search()
    {
        Cachestat::factory("search")->fetch_search(function($data){
            Minion_CLI::write('search:'.Debug::vars($data));
            foreach ($data["ids"] as $object_id) {
                $pos = array_search($object_id, $data["ids"]) + 1;
                $string = "~ ".($pos * $data["page"])." место в рубрике <a href='".$data["url"]."'>".$data["title"]."</a>";
                Cachestat::factory($object_id."insearch")
                    ->add(0, $string);
            }
        });
    }

    function fetch_object_visits()
    {
        $objects = Cachestat::factory("objects_in_visit_counter")->fetch_all(TRUE);
        if (!$objects) return;
        foreach ($objects as $object_id) {
            Minion_CLI::write('detail:'.Debug::vars($object_id));
            $visits = Cachestat::factory($object_id."object_visit_counter")->fetch(TRUE);
            $object = ORM::factory('Object', $object_id);
            if ($object->loaded())
            {

                $object->visits = $object->visits + $visits;
                $object->save();

                $date = date("Y-m-d");

                // $os = ORM::factory('Object_Statistic')
                //         ->where("date","=",$date)
                //         ->where("object_id","=", $object_id)
                //         ->find();
                // if ($os->loaded()) {
                //     $os->visits = $os->visits + $visits;
                // } else {
                //     $os->visits = $visits;
                // }
                // $os->object_id = $object->id;
                // $os->date = $date;
                // $os->save();

            }
        }

    }
}