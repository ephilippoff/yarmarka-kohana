<?php

Class SearhRedirectTest extends Unittest_TestCase
{
    public function setUp() {

    }

    public function tearDown() {
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
    }

    function providerCheckTrueSearchRedirect()
    {
        return array(
            array(
                "avtotransport",
                array(),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili/bmw",
                array(),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili/bmw/x-5",
                array(),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili/bmw/order_price",
                array("page"=>1),
                "avtotransport/legkovye-avtomobili/bmw",
            ),
            array(
                "avtotransport/limit_30",
                array(),
                "avtotransport",
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array("page"=>2),
                false,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array("page"=>1),
                "avtotransport/legkovye-avtomobili",
            )
        );
    }

    /**
     * @dataProvider providerCheckTrueSearchRedirect
     */
    function testCheckTrueSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl)
    {
        $this->checkSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl);
    }

    function providerCheckErrorSearchRedirect()
    {
        return array(
            array(
                "avtotransport/limit_131",
                array(),
                "avtotransport",
            ),
            array(
                "avtotransport/legkovye-avtomobili/page_1",
                array(),
                "avtotransport/legkovye-avtomobili",
            ),
            
            array(
                "avtotransport/legkovye-avtomodsfsf",
                array(),
                "avtotransport",
            ),
            array(
                "avtotransport/legkovye-avtomobili/bmw1",
                array(),
                "avtotransport/legkovye-avtomobili",
            ),
            array(
                "avtotransport/legkovye-avtomobili/bmw/x-1",
                array(),
                "avtotransport/legkovye-avtomobili/bmw",
            ),
            
            array(
                "avtotransport/legkovye-avtomobili/bmw/x-5/order_sdfsdf",
                array(),
                "avtotransport/legkovye-avtomobili/bmw/x-5",
            ),
            array(
                "avtotransport/legkovye-avtomobi/limit_30",
                array(),
                "avtotransport",
            )
        );
    }

    /**
     * @dataProvider providerCheckErrorSearchRedirect
     */
    function testCheckErrorSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl)
    {
        $this->checkSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl);
    }

    function providerCheckOldSeoSearchRedirect()
    {
        return array(
            array(
                "avtotransport/legkovye-avtomobili/bmw",
                array(
                    "marka6" => 223
                ),
                "avtotransport/legkovye-avtomobili/acura",
            ),
            array(
                "avtotransport/legkovye-avtomobili/bmw",
                array(
                    "model2" => 1426,
                ),
                "avtotransport/legkovye-avtomobili/acura/cl",
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "model2" => 1426,
                    "marka6" => 223
                ),
                "avtotransport/legkovye-avtomobili/acura/cl",
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "model2" => array(1426),
                    "marka6" => array(223, 227)
                ),
                FALSE,
            ),
            array(
                "avtotransport/legkovye-avtomobili",
                array(
                    "model2" => array(1426, 1427),
                    "marka6" => array(223, 227)
                ),
                FALSE,
            ),
        );
    }

    /**
     * @dataProvider providerCheckOldSeoSearchRedirect
     */
    function testCheckOldSeoSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl)
    {
        $this->checkSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl);
    }

    function checkSearchRedirect($sourceUrl, $query_params, $rightRedirectedUrl)
    {
        $error_params = $redirectedUrl = FALSE;

        $_SERVER['HTTP_HOST'] = "tyumen.yarmarka.dev".$sourceUrl;
        $_SERVER['REQUEST_URI'] = "bmw?marka6%5B%5D=223&god-vypuska%5Bmin%5D=&god-vypuska%5Bmax%5D=&tsena%5Bmin%5D=&tsena%5Bmax%5D=&probeg%5Bmin%5D=&probeg%5Bmax%5D=&tsvet=&k=&search_by_params=1";

        $uri = new Search_Url($sourceUrl, $query_params);
        try {
            $uri->check_uri_segments();
        } catch (Kohana_Exception $e) {
            $error_params = $e->getParams();
            $redirectedUrl = $error_params["uri"];
        }
        
        $this->assertEquals( $redirectedUrl, $rightRedirectedUrl);

    }

 }