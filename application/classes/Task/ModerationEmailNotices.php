<?php defined('SYSPATH') or die('No direct script access.');


class Task_ModerationEmailNotices extends Minion_Task
{
    protected $_options = array(
        'limit' => 50
    );

    protected function _execute(array $params)
    {
        $limit  = $params['limit'];

      
        $log = ORM::factory('Object_Moderation_Log')
                    ->select(array("object.id","object_id"), array("object.title","object_title"), array("object.city_id","object_city_id") )
                    ->join("object","left")
                        ->on("object_id","=","object.id")
                    ->where('noticed',"=",FALSE)
                    ->limit($limit)
                    ->getprepared_all();


        $user_ids = array_map(function($item){
            return $item->user_id;
        }, $log);

        $user_ids = array_unique($user_ids);

        if (!count($user_ids)) {
            return;
        }

        $users_for_notice = ORM::factory('User')->where("id","IN",$user_ids)->find_all();

        foreach ($users_for_notice as $user) {
            
            if (!$user->email) continue;

            Minion_CLI::write('Notice for: '.$user->email);

            $actions_for_user = array_filter( $log, function($item) use ($user){
                return $item->user_id == $user->id;
            });

            $ids_for_user = array_map(function($item){
                return $item->id;
            },$actions_for_user);


            $city_id = (count($actions_for_user) > 0) ? $actions_for_user[0]->object_city_id : NULL;
            
            $actions_for_user_negative = array_filter( $actions_for_user, function($item) {
                return $item->reason <> 'STATUS1';
            });

            $actions_for_user_positive = array_filter( $actions_for_user, function($item) {
                return $item->reason === 'STATUS1';
            });

            Minion_CLI::write('Actions negative: '.count($actions_for_user_negative).' , positive:'.count($actions_for_user_positive) );


            $actions_for_user_negative = array_map(function($item){
                 return array('id' =>$item->object_id, 'title' => $item->object_title , 'reason' => $item->description);
            }, $actions_for_user_negative);

            $actions_for_user_positive = array_map(function($item){
                return array('id' =>$item->object_id, 'title' => $item->object_title);
            }, $actions_for_user_positive);


             $params = array(
                 'actions_negative' => $actions_for_user_negative,
                 'actions_positive' => $actions_for_user_positive,
                 'domain' => $city_id
             );

             Email_Send::factory('moderate_object')
                    ->to( $user->email )
                    ->set_params($params)
                    ->set_utm_campaign('moderate_object')
                    ->send();

             DB::update('object_moderation_log')
                ->set(array("noticed" => "T"))
                ->where('id','IN',$ids_for_user)
                ->execute();
        }


    }

}