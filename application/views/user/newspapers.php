<div class="m_content">
<div class="winner">
<section class="main-cont">
    <div class="hheader persomal_room-header"><h1 class="ta-c">Личный кабинет &rarr; Газеты</h1></div>
    <div class="fl100 shadow-top z1 persomal_room">

			<?=View::factory('user/_left_menu')?>

        <section class="p_room-inner">
            <header>
                <span class="title">
                    <a class="pay" href="<?=CI::site('billing/pay_service/26')?>">Купить свежий номер электронной версии газеты &laquo;Ярмарка&raquo;</a>
                </span>
            </header>
            <div class="p_cont myadd mysub">
                <div class="nav">
                    <div class="input style2">
                        <div class="inp-cont-bl ">
                            <div class="inp-cont">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="checkbox" value="checkbox" id="select_all"><span>Выделить все</span>
                                    </label>

                                    <label class="no-box">
                                        <input type="checkbox" name="checkbox" value="checkbox" id="delete_selected"><span>Удалить выделенное</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cont ">

                    <?php if ($user_papers) :
                        foreach ($user_papers as $user_paper) : ?>

                            <div class="li">
                                <div class="left-bl">
                                    <div class="top-bl">
                                        <div class="col1">
                                            <div class="input">
                                                <div class="inp-cont-bl ">
                                                    <div class="inp-cont">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="to_del[]" value="<?=$user_paper->id?>">
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col2">
                                            <p class="title clickable">
                                                <a href="<?=$main_category->get_url(NULL, $user_paper->planningofnumber->edition->city_id)?>?source=<?=$user_paper->planningofnumber_id?>" class="clickable">
                                                    <?=$user_paper->planningofnumber->edition->title?> №<?=$user_paper->planningofnumber->number?> от <?=date('d.m.Y', strtotime($user_paper->planningofnumber->date_to_show))?>
                                                </a>
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Пусто</p>
                    <?php endif; ?>
                </div>
            </div>

            <?=$pagination?>
			<div class="clear"></div>
			<br />
        </section>
    </div>

</section>
</div><!--end content winner-->
</div>
