<div class="bg-top-fix"></div>

<div class="aside-group">
	
    <aside class="person-menu-bl start-position">
        					 			        
		<div class="aside-group-logo">
			
			<?php if (isset($data->images['logo'])) : ?>
			
					<img class="ag-logo" src="<?=trim($data->images['logo']['120x90'], '.')?>" />       
					
			<?php endif ?>			
					
		</div>
		
		<?php if ($data->object['is_published'] == 1) : ?>
		
		<p class="contact-bl-title">Контакты</p>

		<div class="contact-bl fn-contact-bl">

			<div class="cf"></div>
               
            <?php if ($data->contacts) : ?> 
			
                <table class="contact-bl-info fn-contact-bl-info">
					
                <tr><th></th><th></th></tr>
				
                <?php  foreach ($data->contacts as $contact) :?>
                    <tr>
                        <?php
                            $contact_icon_class = null;
                            switch($contact['contact_type_id'])
                            {
                            case 1:
                            case 2:
                                $contact_icon_class = 'tel';
                                break;
                            case 3:
                                $contact_icon_class = 'skype';
                                break;
                            case 4:
                                $contact_icon_class = 'icq';
                                break;
                            case 5:
                                $contact_icon_class = 'mail';
                                break;
                            }

                        ?>
                        <td><div class="ico <?=$contact_icon_class?>" title="<?=$contact['contact_type']['name'] ?>"></div></td>
                        <td><?=contact::hide($contact['contact_clear']) ?></td>
                    </tr>
                <?php  endforeach;?>
                </table>
				<br />
                <span class="button blue fn-show-cont-bl" data-id="<?=$data->object['id'] ?>">
                    <span>Показать контакты</span>
                </span>
            <?php else : ?>
                <table class="contact-bl-info">
                <tr><th></th><th></th></tr>
                <tr>
                    <td><div class="ico tel"></div></td>
                    <td><?=htmlspecialchars($data->object['contact']);?></td>
                </tr>
                </table>
            <?php endif; ?>

            <br />

            <?php if ( ! empty($linked_company)) : ?>
            <p class="contact-bl-title">Компания</p>
            <p class="who">
                <?=htmlspecialchars($linked_company->org_name)?>
            </p>
             <?php if ($linked_company->filename) { ?>
                <p><img class="s120x90" src="/<?=Imageci::getThumbnailPath($linked_company->filename, '120x90')?>"></p>
            <?php } ?>
            <?php elseif (($data->user['org_type'] == 2) && ($data->user['org_name'])) : ?>
            <p class="contact-bl-title">Компания</p>
            <p class="who">
                <?=htmlspecialchars($data->user['org_name'])?>
            </p>
             <?php if ($data->user['author_logo']) { ?>
                <p><img class="s120x90" src="/<?=Imageci::getThumbnailPath($data->user['author_logo'], '120x90')?>"></p>
            <?php } ?>
            <?php endif; ?>

            <?php if ( ! empty($linked_company)) : ?>
            <span onclick="window.location='<?=base_url()?>tyumenskaya-oblast/glavnaya-kategoriya?user_id=<?=$linked_company->id?>'" class="moreinfo span-link">Все объявления компании</span>
            <?php endif; ?>
</div>
<?php else: ?>

        <?php if ($data->object['org_type'] == 2) : ?>
            <p class="contact-bl-title">Компания</p>
            <p class="who">
                <?=htmlspecialchars($data->object['org_name'])?>
            </p>
            <?php if ($data->object['author_logo']) { ?>
                <p><img class="s120x90" src="/<?=Imageci::getThumbnailPath($data->object['author_logo'], '120x90')?>"></p>
            <?php } ?>
        <?php endif; ?>
<?php endif; ?>

        <?/*<td class="logo">
                                                   
                                                </td>*/?>
		
		
		
        <div class="pmenu-second">
            <ul>
                <?php if (Auth::instance()->get_user()) : ?>
					<li>
						<a href="" class="" onclick="favorites(<?=$data->object['id']?>); event.preventDefault()">
							<i class="ico add"></i>
							<span class="favor-text fn-favor-text"><?=($data->favorite == 1) ? 'Удалить из избранного' : 'Добавить в избранное' ?></span>
						</a>
					</li>
                <?endif;?>
				
				<? if (!$data->object['is_bad'] && Auth::instance()->get_user() && Auth::instance()->get_user()->id == $data->object['author'] && $data->object['in_archive'] == 'f') : ?>
                      <li>
                          <a class="icon-2" href="/user/edit_ad/<?php echo $data->object['id'] ?>"><i class="ico change"></i>Редактировать</a>
                      </li>
                <? endif; ?>
            </ul>
        </div>


<? if($data->object['is_bad'] == 2 && Auth::instance()->get_user() && Auth::instance()->get_user()->id == $data->object['author'] && $data->object['in_archive'] == 't')
{ ?>
<div class="pmenu-second">
    <p class="contact-bl-title">Это объявление заблокировано и перемещено в архив</p>
</div>
<?}?>

	
		
		
        <div class="like">
			<p class="line">Поделиться с друзьями</p>
            <script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
            <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,lj,friendfeed,moikrug,gplus"></div> 		
        
			<div class="iPage-alert-bl w100p">
				<div class="box">
					<div class="img"></div>
					<p class="text">Расскажите друзьям в соц. сетях, что вы продаете и они помогут вам найти покупателя.</p>
				</div>
			</div>
		</div>
        
    </aside>
                                     
    </aside>
</div>	