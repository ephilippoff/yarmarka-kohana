define([ 
	//used as parameters
	'templates/ckeditor/loader',
	//not used as parameters
	'ckeditor', 
	'ckeditorJqueryAdapter'
], function (moduleTemplates) {

	//config
	CKEDITOR.editorConfig = function( config ) {
		config.allowedContent = true;
		config.language = 'ru';
		config.contentsCss = '/assets/css/css.css';
		config.toolbarCanCollapse = true;
		config.stylesSet = [];
		config.templates = 'customTemplates';
	};

	//add custom templates to ckeditor
	CKEDITOR.addTemplates('customTemplates', {
		imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + 'templates/images/' ),
		templates: [
			{
				title: 'Купон',
				html: moduleTemplates.kupon
			}
		]
	});

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
			if ($(item).data('_ckeditorInstanceLock')) {
				return;
			}
			try {
				$(item).ckeditor($.extend(this.config, append));
			} catch (e) {
				//empty
			}
		}
	});

	return new CkEditor;
});