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

        $subquery = DB::select("o.id")
                        ->from(array("object","o") )
                        ->where("o.date_expiration","<", DB::expr("NOW()"))
                        ->where("o.in_archive", "=", 'N' )
                        ->where("o.active", "=", 1)
                        ->limit($limit);

        $subquery_objects_without_author = clone $subquery;
        $subquery_objects_with_author = clone $subquery;

        $subquery_objects_without_author = $subquery_objects_without_author->where("o.author", "IS", NULL);

        $subquery_objects_with_author = $subquery_objects_with_author->where("o.author", "IS NOT", NULL);
    
        Minion_CLI::write( 'objects without author to archive: '.count($subquery_objects_without_author->execute()) );
        Minion_CLI::write( 'objects with author to archive: '.count($subquery_objects_with_author->execute()) );

        DB::update(array("object","o"))
            ->set( array("in_archive" => "T", "is_published" => 0) )
            ->where("o.id", "IN", $subquery_objects_without_author)
            ->execute();

        if ($sendmail) {
            $subquery_authors = DB::select("o.author")
                                        ->from(array("object","o") )
                                        ->where("o.id","IN", $subquery_objects_with_author)
                                        ->group_by("o.author");

            $users = ORM::factory('User')
                        ->where("id", "IN", $subquery_authors)
                        ->where("email","IS NOT", NULL)
                        ->find_all();

            foreach ($users as $user) {

                $objects = DB::select("o.*")
                                ->from(array("object","o") )
                                ->where("o.id","IN", $subquery_objects_with_author)
                                ->where("o.author","=",$user->id)
                                ->execute();

                $msg = View::factory('emails/object_to_archive',
                        array(
                            'objects' => $objects
                        ));

                Minion_CLI::write( 'notice send to: '.$user->email );

                Email::send('a.vagapov@yarmarka.biz', Kohana::$config->load('email.default_from'), 'Ваши объявления перемещены в архив', $msg);

            }
        }

        DB::update(array("object","o"))
            ->set( array("in_archive" => "T", "is_published" => 0) )
            ->where("o.id", "IN", $subquery_objects_with_author)
            ->execute();


    }

}