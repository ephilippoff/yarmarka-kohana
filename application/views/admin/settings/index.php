<h2>Настройки</h2>

<form class="form-inline" method="post" action="">
	<div class="input-prepend">
		<span class="add-on"><i class="icon-envelope"></i></span>
		<input class="span2" id="prependedInput" type="text" placeholder="User email" name="email" value="<?=Arr::get($_GET, 'email')?>">
    </div>
    <input type="submit" name="" value="Enter" class="btn btn-primary">
</form>