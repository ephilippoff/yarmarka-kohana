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
			this.config = config;

			this.replaceAll();
		},

		replaceAll: function () {
			var me = this;
			$('.ckeditor').each(function (index, item) {
				var append = {};
				var itemData = $(item).data();
				if (itemData.fileupload) {
					append.fileUpload = true;
				}
				me.replaceOne(item, append);
			});
		},

		replaceOne: function (item, options) {
			var append = {};
			if (options.fileUpload) {
				append.filebrowserBrowseUrl = '/files';
			}
			$(item).ckeditor($.extend(this.config, append));
		}
	});

	return new CkEditor;
});