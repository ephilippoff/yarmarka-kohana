<?php

Class SearhQueryParamsTest extends Unittest_TestCase
{
    public function setUp() {

    }

    public function tearDown() {

    }


    function providerCheckSearchQueryParams()
    {
        return array(
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "marka6" => 223
                ),
                TRUE,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "marka6" => 223,
                    "model2" => 1432,
                ),
                TRUE,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "model2" => 1432,
                    "marka6" => 223
                ),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "model2" => 1432,
                    "marka6" => array(223,224)
                ),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "marka6" => array(223,224),
                    "model2" => 1432,
                    "tsvet" => "синий"
                ),
                FALSE,
            )
        );
    }

    /**
     * @dataProvider providerCheckSearchQueryParams
     */
    function testCheckSearchQueryParams($sourceUrl, $query_params, $result)
    {
        $this->checkSearchRedirect($sourceUrl, $query_params, $result);
    }

    function checkSearchRedirect($sourceUrl, $query_params, $result)
    {
        $testResult = TRUE;

        $uri = new Search_Url($sourceUrl, $query_params);
        try {
            $uri->check_query_params($query_params);
        } catch (Kohana_Exception_Withparams $e) {
            $testResult = FALSE;
        }
        
        $this->assertEquals( $result, $testResult);

    }

 }