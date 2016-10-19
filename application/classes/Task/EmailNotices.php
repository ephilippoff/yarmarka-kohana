<?php defined('SYSPATH') or die('No direct script access.');


class Task_EmailNotices extends Minion_Task
{
    protected $_options = array(
        // 'limit' => 50
    );

    protected function _execute(array $params)
    {
        //$this->aboutPhoto();
        //$this->aboutUp();
        $this->aboutExpiration();
        $this->aboutSubscription();

    }

    public function aboutPhoto() {

        $notice_query =  DB::select("on.object_id")
                        ->from(array("object_notice","on"))
                        ->where("on.object_id","=",DB::expr("o.id"))
                        ->where("on.name","=", Model_Object_Notice::PHOTO)
                        ->where("on.date","<", DB::expr("NOW() + interval '7 day'"));

        $objects_query =  DB::select("o.id")
                        ->from(array("object","o") )
                        ->where("o.active", "=", 1 )
                        ->where("o.is_published", "=", 1 )
                        ->where("o.number", "IS", NULL )
                        ->where(DB::expr("NOW()"),">", DB::expr("o.real_date_created + interval '20 hours'"))
                        ->where(DB::expr("NOW()"),"<", DB::expr("o.real_date_created + interval '26 hours'"))
                        ->where("o.main_image_id","IS", NULL)
                        ->where("o.category", "NOT IN", array(0))
                        ->where("o.id", "NOT IN", $notice_query );

        $subquery_authors = DB::select("oa.author")
                                ->from(array("object","oa") )
                                ->where("oa.id","IN", $objects_query)
                                ->group_by("oa.author");

        $users = ORM::factory('User')
                    ->where("id", "IN", $subquery_authors)
                    ->where("email","IS NOT", NULL)
                    ->find_all();

        if (!count($users)) {
            Minion_CLI::write('nothing to send about photo');
            return;
        } else {
             Minion_CLI::write('email about photo will send to '.count($users).' users');
        }

        foreach ($users as $user) {

           $objects = DB::select("o.*")
                            ->from(array("object","o") )
                            ->where("o.id","IN", $objects_query)
                            ->where("o.author","=", $user->id)
                            ->execute();
            if (!count($objects)) continue;

            Minion_CLI::write('notice about '.Model_Object_Notice::PHOTO.' in '.count($objects).' objects sent to: '.$user->email);

            $query = DB::select("o.id", DB::expr("'".Model_Object_Notice::PHOTO."'"))
                ->from(array("object","o"))
                ->where("o.id", "IN", $objects_query)
                ->where("o.author","=", $user->id);

            DB::insert('object_notice', array('object_id', 'name'))
                 ->select( $query )
                 ->execute();

        }


    }

    public function aboutUp() {

        $notice_query =  DB::select("on.object_id")
                        ->from(array("object_notice","on"))
                        ->where("on.object_id","=",DB::expr("o.id"))
                        ->where("on.name","=", Model_Object_Notice::UP)
                        ->where("on.date","<", DB::expr("NOW() + interval '7 day'"));

        $objects_query =  DB::select("o.id")
                        ->from(array("object","o") )
                        ->where("o.active", "=", 1 )
                        ->where("o.is_published", "=", 1 )
                        ->where("o.number", "IS", NULL )
                        ->where(DB::expr("NOW()"),">", DB::expr("o.date_created + interval '7 days'"))
                        ->where(DB::expr("NOW()"),"<", DB::expr("o.date_created + interval '8 days'"))
                        ->where("o.category", "NOT IN", array(0))
                        ->where("o.id", "NOT IN", $notice_query );

        $subquery_authors = DB::select("oa.author")
                                ->from(array("object","oa") )
                                ->where("oa.id","IN", $objects_query)
                                ->group_by("oa.author");

        $users = ORM::factory('User')
                    ->where("id", "IN", $subquery_authors)
                    ->where("email","IS NOT", NULL)
                    ->find_all();

        if (!count($users)) {
            Minion_CLI::write('nothing to send about up');
            return;
        } else {
             Minion_CLI::write('email about up will send to '.count($users).' users');
        }

        foreach ($users as $user) {

           $objects = DB::select("o.*")
                            ->from(array("object","o") )
                            ->where("o.id","IN", $objects_query)
                            ->where("o.author","=", $user->id)
                            ->execute();

            if (!count($objects)) continue;

            Minion_CLI::write('notice about '.Model_Object_Notice::UP.' in '.count($objects).' objects sent to: '.$user->email);

            $query = DB::select("o.id", DB::expr("'".Model_Object_Notice::UP."'"))
                ->from(array("object","o"))
                ->where("o.id", "IN", $objects_query)
                ->where("o.author","=", $user->id);

            DB::insert('object_notice', array('object_id', 'name'))
                 ->select( $query )
                 ->execute();

        }


    }

    public function aboutExpiration() {

        $notice_query =  DB::select("on.object_id")
                        ->from(array("object_notice","on"))
                        ->where("on.object_id","=",DB::expr("o.id"))
                        ->where("on.name","=", Model_Object_Notice::EXPIRATION)
                        ->where("on.date","<", DB::expr("NOW() + interval '7 day'"));

        $objects_query =  DB::select("o.id")
                        ->from(array("object","o") )
                        ->where("o.active", "=", 1 )
                        ->where("o.is_published", "=", 1 )
                        ->where("o.number", "IS", NULL )
                        ->where(DB::expr("NOW()"),">", DB::expr("o.date_expiration - interval '7 days'"))
                        ->where(DB::expr("NOW()"),"<", DB::expr("o.date_expiration - interval '6 days'"))
                        ->where("o.category", "NOT IN", array(0))
                        ->where("o.id", "NOT IN", $notice_query );

        $subquery_authors = DB::select("oa.author")
                                ->from(array("object","oa") )
                                ->where("oa.id","IN", $objects_query)
                                ->group_by("oa.author");

        $users = ORM::factory('User')
                    ->where("id", "IN", $subquery_authors)
                    ->where("email","IS NOT", NULL)
                    ->find_all();

        if (!count($users)) {
            Minion_CLI::write('nothing to send about expiration');
            return;
        } else {
             Minion_CLI::write('email about expiration will send to '.count($users).' users');
        }

        foreach ($users as $user) {

           $objects = DB::select("o.*")
                            ->from(array("object","o") )
                            ->where("o.id","IN", $objects_query)
                            ->where("o.author","=", $user->id)
                            ->execute();

            if (!count($objects)) continue;

            $city_id = $objects[0]['city_id'];

            $ids = array();
            $objects_for_email = array();
            $i = 0;
            foreach ($objects as $object) {
                if ($object['number']) continue;
                array_push($ids, $object['id']);
                array_push($objects_for_email,  $object);
                $i++;
                if ($i > 10) break;

            }

            if (count($objects_for_email) > 0) {

                $params = array(
                    'objects' => $objects_for_email,
                    'ids' => join('.', $ids),
                    'domain' => $city_id
                );

                Email_Send::factory('object_expiration')
                        ->to( $user->email )
                        ->set_params($params)
                        ->set_utm_campaign('object_expiration')
                        ->send();


                Minion_CLI::write('notice about '.Model_Object_Notice::EXPIRATION.' in '.count($objects).' objects sent to: '.$user->email);
            }
            
            $query = DB::select("o.id", DB::expr("'".Model_Object_Notice::EXPIRATION."'"))
                ->from(array("object","o"))
                ->where("o.id", "IN", $objects_query)
                ->where("o.author","=", $user->id);

            DB::insert('object_notice', array('object_id', 'name'))
                 ->select( $query )
                 ->execute();

        }


    }


    private $_max_count_empty_subscriptions = 31;

    public function aboutSubscription() {

        $subscriptions = ORM::factory('Subscription_Surgut')->get_enabled();

        foreach($subscriptions as $subscription) {

            $empty_counter = ($subscription->empty_counter) ? $subscription->empty_counter: 0;
            $url = sprintf('%s%s', $subscription->data->path, ($subscription->data->query) ? "?".$subscription->data->query : "");

            $user = ORM::factory('User',$subscription->user_id);

            $subscription->filters['order'] = 'id';

            if ($subscription->last_object_id) {
                $subscription->filters['gt_id'] = $subscription->last_object_id;
            }

            $main_search_query = Search::searchquery($subscription->filters, array( 
                "limit" => 20,
                "page" => 1
            ));
            
            $main_search_result = Search::getresult($main_search_query->execute()->as_array());

            if (count($main_search_result)) {



                $count_new  = (int) Search::searchquery($subscription->filters, array(), array(
                    "count" => TRUE
                ))->execute()->get("count");

                $params = array(
                    'objects' => $main_search_result,
                    'title' => $subscription->data->title,
                    'count_new' => $count_new,
                    'url' => $url,
                    'domain' => $subscription->filters['city_id'],
                );

                Email_Send::factory('subscription')
                            ->to( $user->email )
                            ->set_params($params)
                            ->set_utm_campaign('subscription')
                            ->send();

                $last_object_id = $main_search_result[0]['id'];

                $ss = ORM::factory('Subscription_Surgut', $subscription->id);
                $ss->last_object_id = $last_object_id;
                $ss->empty_counter = $empty_counter = 0;
                $ss->sent_on = DB::expr('NOW()');
                $ss->update();
                
                Minion_CLI::write( sprintf('Подписка %s отправлена %s', $subscription->data->title,$user->email) );

            } else {

                $ss = ORM::factory('Subscription_Surgut')
                                ->where('id','=', $subscription->id)
                                ->where('sent_on','<',DB::expr("NOW() - INTERVAL '6 hours'"))
                                ->find();
                if ($ss->loaded()) {
                    $ss->empty_counter = $empty_counter = $empty_counter + 1;
                    $ss->update();
                }

            }

            if ($empty_counter >= $this->_max_count_empty_subscriptions) {

                

                $params = array(
                    'objects' => $main_search_result,
                    'title' => $subscription->data->title,
                    'url' => $url,
                    'domain' => $subscription->filters['city_id']
                );

                Email_Send::factory('subscription_cancel')
                            ->to( $user->email )
                            ->set_params($params)
                            ->set_utm_campaign('subscription_cancel')
                            ->send();

                Minion_CLI::write( sprintf('Подписка %s остановлена для %s', $subscription->data->title,$user->email) );

                $ss = ORM::factory('Subscription_Surgut', $subscription->id);
                $ss->empty_counter = 0;
                $ss->enabled = 0;
                $ss->update();

            }

        }

    }


}
