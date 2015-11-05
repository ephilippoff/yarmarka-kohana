function ValidateContactValue(type, value)
{
	if (type == 'phone')
	{
		var regex = /^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/;
		return regex.test(value);
	}
	if (type == 'email')
	{
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(value);
	}
	return true;
}