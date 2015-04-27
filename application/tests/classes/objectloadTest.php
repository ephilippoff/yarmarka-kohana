<?php

Class ObjectloadTest extends Unittest_TestCase
{
    const USER_ID = 327190;
    const OBJECTLOAD_ID = 123123;

    function providerGetFilesAndUrls()
    {
        return array(
            array(
                array("1", "2", "3"),
                array("1", "2", "3"), 
                TRUE,
                array()
            ),
            array(
                array("1", "2", "3", "4"),
                array("1", "2"), 
                TRUE,
                array(
                    array("action" => "add", "type" => "file", "path" =>TRUE, "url" =>"3"),
                    array("action" => "add", "type" => "file", "path" =>TRUE, "url" =>"4")
                )
            ),
            array(
                array("1", "2"),
                array("1", "2", "3"), 
                TRUE,
                array(
                    array("action" => "delete", "path" =>"3", "url" =>"3")
                )
            ),
            array(
                array("1", "2", "3"),
                array("1", "2", "3"), 
                FALSE,
                array()
            ),
            array(
                array("1", "2", "3", "4"),
                array("1", "2"), 
                FALSE,
                array(
                    array("action" => "add", "type" => "url", "path" =>"3", "url" =>"3"),
                    array("action" => "add", "type" => "url", "path" =>"4", "url" =>"4")
                )
            ),
            array(
                array("1", "2", "3"),
                array("1", "2", "4"), 
                FALSE,
                array(
                    array("action" => "add", "type" => "url", "path" =>"3", "url" =>"3"),
                    array("action" => "delete", "path" =>"4", "url" =>"4")
                )
            ),
            array(
                array("1"),
                array(), 
                FALSE,
                array(
                    array("action" => "add", "type" => "file", "path" =>TRUE, "url" =>"1")
                )
            ),
            array(
                array("1"),
                array("2"), 
                FALSE,
                array(
                    array("action" => "add", "type" => "file", "path" =>TRUE, "url" =>"1"),
                    array("action" => "delete", "path" =>"2", "url" =>"2")
                )
            ),
        );
    }

    /**
     * @dataProvider providerGetFilesAndUrls
     */
    function testGetFilesAndUrls($images, $existed_images, $save_images_accepted, $rightResult)
    {

        $mock = $this->getMockBuilder('Objectload')
            ->setMethods(array('saveFile'))
            //->setConstructorArgs(array(self::USER_ID, self::OBJECTLOAD_ID))
            //->setMockClassName('')
            ->disableOriginalConstructor()
            //->disableOriginalClone()
           // ->disableAutoload()
            ->getMock();

        $mock->expects($this->any())
                    ->method('saveFile')
                    ->will($this->returnValue(true));

        $filesAndUrls = $mock->getFilesAndUrls($images, $existed_images, $save_images_accepted);

        $this->assertTrue( is_array($filesAndUrls) );


        $this->assertEquals( $filesAndUrls, $rightResult );
        

    }

    function providerSavePhoto()
    {
        return array(
            array(
                3572255, 
                "http://s2.media.etagi.com/photos/520/390/0/0/1/1/0/10/0/553074f7e7b63.jpg;http://s2.media.etagi.com/photos/520/390/0/0/1/1/0/10/0/5530755254666.jpg;http://www.emls.ru/tum/term/images/export/flats/_1/14931/b5814e0f8923749bc4949229b768dd3d.jpg",
                FALSE
            )
        );
    }

    /**
     * @dataProvider providerSavePhoto
     */
    function testSavePhoto($object_id, $filesstr, $save_images_accepted)
    {

        $ol = new Objectload(self::USER_ID, self::OBJECTLOAD_ID);

        echo Debug::vars($ol->savePhotos($object_id, $filesstr, $save_images_accepted));
    }
}