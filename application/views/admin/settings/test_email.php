<form method="POST">
	<textarea name="to" placeholder="To... (delimiter is ,)" cols="50" rows="10"><?php if(isset($_POST['to'])) { ?><?php echo htmlspecialchars($_POST['to']); ?><?php } ?></textarea>
	<input type="submit" value="Send" />
</form>