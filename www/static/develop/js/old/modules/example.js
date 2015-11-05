(function( $ ){
	// настройки по умолчанию
	var defaults = {
	};

	// все методы плагина описываем здесь
	var methods = {
		init : function (settings) {
			// мержим дефолтные настройки с тему что пришли
			var options =$.extend({}, defaults, settings);

			// всегда возвращаем this чтобы работал method chaning
			// если селектор выбирает много элементов, возвращаем для каждого через this.each()
			return this.each(function(){
				// юзаем прокси чтобы передавать окружение
				// подробнее читаем здесь http://learn.javascript.ru/this
				$.proxy(methods.testMethod, this)();
			});
		},
		testMethod : function() {
			// code
		}
	};

	$.fn.profileContacts = function(method) {
		// немного магии для вызова методов плагина
		if ( methods[method] ) {
			// если запрашиваемый метод существует, мы его вызываем
			// все параметры, кроме имени метода прийдут в метод
			// this так же перекочует в метод	
	        return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			// если первым параметром идет объект, либо совсем пусто
			// выполняем метод init	
			return methods.init.apply( this, arguments );
		} else {
			$.error('Метод "' +  method + '" не найден');
		}
	};
})( jQuery );