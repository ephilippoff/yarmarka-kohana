<?php defined('SYSPATH') OR die('No direct access allowed.');
return array(
      'noindex_routes'  => array(
            "add","user","admin", "backend", "cart", "userpage", "object_edit"
      ),

      'title_postfix'         => ' - Газета бесплатных объявлений Ярмарка',


      'city_neiboors' => array(
            'main' => array(
                  '/nizhnevartovsk/',
                  '/surgut/',
                  '/kogalym/',
                  '/langepas/',
                  '/pyt\-yakh/',
                  '/tyumen/',
                  '/nefteyugansk/',
                  '/izluchinsk/',
                  '/khanty-mansiisk/',
                  '/megion/',
                  '/raduzhnyi/',
                  '/ekaterinburg/',
                  '/ishim/',
                  '/tobolsk/',
                  '/yalutorovsk/',
                  '/zavodoukovsk/',
            ),
            'surgut' => array(
                  '/nefteyugansk/',
                  '/pyt\-yakh/',
                  '/nizhnevartovsk/',
                  '/tyumen/'
                  
            ),
            'nefteyugansk' => array(
                  '/nizhnevartovsk/',
                  '/surgut/',
                  '/kogalym/',
                  '/langepas/',
                  '/pyt\-yakh/',
                  '/tyumen/'
            ),
            'nizhnevartovsk' => array(
                  '/surgut/',
                  '/nefteyugansk/',
                  '/izluchinsk/',
                  '/khanty-mansiisk/',
                  '/megion/',
                  '/raduzhnyi/',
                  '/tyumen/'
            ),
             'tyumen' => array(
                 '/ekaterinburg/',
                 '/ishim/',
                 '/tobolsk/',
                 '/yalutorovsk/',
                 '/zavodoukovsk/',
                 '/nizhnevartovsk/',
                  '/surgut/',
            ),
      ),

      'category_map_filter' => array(
            'index' => array(),
            'search' => array(

                  '/^lichnie-veshi/',
                  '/^v-horoshie-ruki/',
                  '/^znakomstvo-i-obshenie/',
                  '/^novosti/',
                  '/^prodovolstvennye-tovary/',
                  '/^sport-otdih-hobby/',
                  '/^avtotransport\/transportnye-uslugi/',
                  '/drugoi-kommercheskii-transport/',
                  '/vodnii-transport/',
                  '/avtotransport\/arenda/',
                  '/avtotransport\/mototsikly-velosipedy\/drugoi-moto-transport/',
                  '/uslugi\/prochie/',
                  '/stroitelstvo-i-remont\/remont-prochie/',
                  '/dlya-doma-i-dachi/',
                  '/domashnie-zhivotnye-i-rasteniya\/rasteniya/',
                  '/domashnie-zhivotnye-i-rasteniya\/akvariumistika/',
                  '/domashnie-zhivotnye-i-rasteniya\/accesoares-dlya-zhivotnih/',
            )
      ),

      'links_with_low_priority' => array(
        'index' => array(
            // '/^\/lichnie-veshi/',
            // '/^\/v-horoshie-ruki/',
            // '/^\/znakomstvo-i-obshenie/',
            // '/^\/novosti/',

            // '/beloyarskii\.yarmarka\./',
            // '/ishim\.yarmarka\./',
            // '/labytnangi\.yarmarka\./',
            // '/lyantor\.yarmarka\./',
            // '/nyagan\.yarmarka\./',
            // '/pokachi\.yarmarka\./',
            // '/pyt\-yakh\.yarmarka/',
            // '/raduzhnyi\.yarmarka\./',
            // '/sovetskii\.yarmarka\./',
            // '/urai\.yarmarka\./',
            // '/uvat\.yarmarka\./',
            // '/vagai\.yarmarka\./',
            // '/yalutorovsk\.yarmarka\./',
            // '/yugorsk\.yarmarka\./',
            // '/zavodoukovsk\.yarmarka\./',
        ),
        'search' => array(
            // '/^\/lichnie-veshi/',
            // '/^\/v-horoshie-ruki/',
            // '/^\/znakomstvo-i-obshenie/',
            // '/^\/novosti/',

            // "/^\/novosti$/",
            // "/^\/rabota$/",
            // "/^\/nedvizhimost$/",
            // "/^\/znakomstvo-i-obshenie$/",
            // "/^\/sport-otdih-hobby$/",
            // "/^\/biznes$/",
            // "/^\/v-horoshie-ruki$/",
            // "/^\/kupony$/",
            // "/^\/prodovolstvennye-tovary$/",
            // "/^\/avtotransport$/",
            // "/^\/domashnie-zhivotnye-i-rasteniya$/",
            // "/^\/lichnie-veshi$/",
            // "/^\/uslugi$/",
            // "/^\/tovary-dlya-detei$/",
            // "/^\/meditsina-zdorove-tovary-i-uslugi$/",
            // "/^\/dlya-doma-i-dachi$/",
            // "/^\/bitovaya-elektronika$/",
            // "/^\/modulnaya-reklama$/",
            // "/^\/stroitelstvo-i-remont$/",

            // '/beloyarskii\.yarmarka\./',
            // '/ishim\.yarmarka\./',
            // '/labytnangi\.yarmarka\./',
            // '/lyantor\.yarmarka\./',
            // '/nyagan\.yarmarka\./',
            // '/pokachi\.yarmarka\./',
            // '/pyt\-yakh\.yarmarka/',
            // '/raduzhnyi\.yarmarka\./',
            // '/sovetskii\.yarmarka\./',
            // '/urai\.yarmarka\./',
            // '/uvat\.yarmarka\./',
            // '/vagai\.yarmarka\./',
            // '/yalutorovsk\.yarmarka\./',
            // '/yugorsk\.yarmarka\./',
            // '/zavodoukovsk\.yarmarka\./',

            // '/\/drugoe\.\.\./'
        ),
        'detail' => array(
            // '/^\/lichnie-veshi/',
            // '/^\/v-horoshie-ruki/',
            // '/^\/znakomstvo-i-obshenie/',
            // '/^\/novosti/',
            // '/^\/prodovolstvennye-tovary/',
            // '/^\/sport-otdih-hobby/',

            // "/^\/novosti$/",
            // "/^\/rabota$/",
            // "/^\/nedvizhimost$/",
            // "/^\/znakomstvo-i-obshenie$/",
            // "/^\/sport-otdih-hobby$/",
            // "/^\/biznes$/",
            // "/^\/v-horoshie-ruki$/",
            // "/^\/kupony$/",
            // "/^\/prodovolstvennye-tovary$/",
            // "/^\/avtotransport$/",
            // "/^\/domashnie-zhivotnye-i-rasteniya$/",
            // "/^\/lichnie-veshi$/",
            // "/^\/uslugi$/",
            // "/^\/tovary-dlya-detei$/",
            // "/^\/meditsina-zdorove-tovary-i-uslugi$/",
            // "/^\/dlya-doma-i-dachi$/",
            // "/^\/bitovaya-elektronika$/",
            // "/^\/modulnaya-reklama$/",
            // "/^\/stroitelstvo-i-remont$/",

            // "/\.html$/",

            // '/beloyarskii\.yarmarka\./',
            // '/ishim\.yarmarka\./',
            // '/izluchinsk\.yarmarka\./',
            // '/khanty-mansiisk\.yarmarka\./',
            // '/kogalym\.yarmarka\./',
            // '/labytnangi\.yarmarka\./',
            // '/langepas\.yarmarka\./',
            // '/lyantor\.yarmarka\./',
            // '/megion\.yarmarka\./',
            // '/nefteyugansk\.yarmarka\./',
            // '/nyagan\.yarmarka\./',
            // '/pokachi\.yarmarka\./',
            // '/pyt\-yakh\.yarmarka/',
            // '/raduzhnyi\.yarmarka\./',
            // '/sovetskii\.yarmarka\./',
            // '/tobolsk\.yarmarka\./',
            // '/urai\.yarmarka\./',
            // '/uvat\.yarmarka\./',
            // '/vagai\.yarmarka\./',
            // '/yalutorovsk\.yarmarka\./',
            // '/yugorsk\.yarmarka\./',
            // '/zavodoukovsk\.yarmarka\./',
        )
      )
);
