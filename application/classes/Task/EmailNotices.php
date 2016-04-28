<?php defined('SYSPATH') or die('No direct script access.');


class Task_EmailNotices extends Minion_Task
{
    protected $_options = array(
        // 'limit' => 50
    );

    protected function _execute(array $params)
    {
        $this->aboutPhoto();
        $this->aboutUp();
        $this->aboutExpiration();

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
        }

        foreach ($users as $user) {

           $objects = DB::select("o.*")
                            ->from(array("object","o") )
                            ->where("o.id","IN", $objects_query)
                            ->where("o.author","=", $user->id)
                            ->execute();

            if (!count($objects)) continue;

            Minion_CLI::write('notice about '.Model_Object_Notice::EXPIRATION.' in '.count($objects).' objects sent to: '.$user->email);

            $query = DB::select("o.id", DB::expr("'".Model_Object_Notice::EXPIRATION."'"))
                ->from(array("object","o"))
                ->where("o.id", "IN", $objects_query)
                ->where("o.author","=", $user->id);

            DB::insert('object_notice', array('object_id', 'name'))
                 ->select( $query )
                 ->execute();

        }


    }


}
