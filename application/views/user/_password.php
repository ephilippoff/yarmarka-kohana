<?php if (Session::instance()->get_once('success')) : ?>
	<div class="row mb10">
		<div class="col-md-3 col-xs-4 labelcont">
			<label></label>
		</div>
		<div class="col-md-9 col-xs-8">
			<div style="color:green">Пароль успешно изменен</div>
		</div>
	</div>
<?php endif ?>

<div class="form-cont">
	<form action="/user/password" id="password" method="POST">
		
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>Пароль:</label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="row">
					<div class="col-md-6">
						<div class="inp-cont <?php if ($error) echo 'error'?>">
							<input class="w100p" type="password" name="password" value="" />
							<span class="inform">
								<?=$error?>
							</span>							
						</div>
					</div>
				</div>				
			</div>
		</div>	
		
		<div class="row mb10">
			<div class="col-md-3 col-xs-4 labelcont">
				<label>Повтор пароля:</label>
			</div>
			<div class="col-md-9 col-xs-8">
				<div class="row">
					<div class="col-md-6">
						<div class="inp-cont <?php if ($error) echo 'error'?>">
							<input class="w100p" type="password" name="password_repeat" value="" />
							<span class="inform">
								<?=$error?>
							</span>							
						</div>
					</div>
				</div>				
			</div>
		</div>
		
		<div class="row mb20">
			<div class="col-md-3 labelcont">
				<label></label>
			</div>
			<div class="col-md-9 ta-r">	
				<span onclick="$('#password').submit()" class="button button-style1 bg-color-blue btn-next">Сохранить</span>
			</div>
		</div>		
	</form>
</div>