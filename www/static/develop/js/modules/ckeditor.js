window.CKEDITOR_BASEPATH = '/static/develop/js/lib/ckeditor/';
define([ 'ckeditor', 'ckeditorJqueryAdapter' ], function () {

	//config
	CKEDITOR.editorConfig = function( config ) {
		config.language = 'ru';
		config.contentsCss = '/assets/css/css.css';
		config.toolbarCanCollapse = true;
		config.stylesSet = [];
	};

	//plugins

	var CkEditor = function () {
		this.initialize();
	};

	$.extend(CkEditor.prototype, {
		initialize: function () {
			var config = {};
			CKEDITOR.editorConfig(config);
			$('.ckeditor').each(function (index, item) {
				$(item).ckeditor(config);
			});
		}
	});

	return CkEditor;
});