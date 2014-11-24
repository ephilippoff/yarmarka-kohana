<table class="contact-bl-info">
	<tr><th></th><th></th></tr>
	<?php  foreach ($contacts as $contact) :?>
		<tr>
			<?php
			$contact_icon_class = null;
			switch($contact->contact_type_id)
			{
				case 1:
				case 2:
				$contact_icon_class = 'tel';
				$contact_str = Contact::format_phone($contact->contact_clear);
				break;
				case 3:
				$contact_icon_class = 'skype';
				$contact_str = $contact->contact;
				break;
				case 4:
				$contact_icon_class = 'icq';
				$contact_str = $contact->contact;
				break;
				case 5:
				$contact_icon_class = 'mail';
				$contact_str = '<a href="mailto:'.$contact->contact.'">'.$contact->contact.'</a>';	
				break;
			}

			?>
			<td><div class="ico <?=$contact_icon_class?>" title="<?=$contact->contact_type->name?>"></div></td>
			<td><?=$contact_str?></td>
		</tr>
	<?php  endforeach;?>
</table>