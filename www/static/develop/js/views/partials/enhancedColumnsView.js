(function () {
	var dependencies = [ 'backbone', 'jquery' ];

	var init = function (Backbone, $) {
		var EnhancedColumnsView = Backbone.View.extend({
	        initialize: function(options) {
	            this.$el.addClass('enc-columns-table');
	            this.render();
	            this.bindEvents();
	        },

	        bindEvents: function () {
	        	var me = this;
	        	//render when window resized
	        	$(window).on('resize', function () {
	        		me.render();
	        	});
	        },

	        render: function() {
	            //fill build options
	            this.items = this.getItems();
	            this.columnWidth = this.maxWidth();
	            this.containerWidth = this.getContainerWidth();
	            this.columnsCount = this.getColumnsCount();
	            this.columnRows = this.getColumnRows();
	            this.columnWidthPerc = this.getColumnWidthPerc();

	            //clear container
	            this.$el.empty();

	            //debug
	            console.log('Enhanced columns view render: ', this);

	            //build table
	            this.build();
	        },

	        build: function() {
	            var me = this;
	            var rows = me.columnRows;
	            var colsToAppend1 = me.items.length - me.columnsCount * Math.floor(me.columnRows);
	            var col = 0;
	            var rowIndex = 0;
	            var $column = null;
	            $.each(this.items, function (index, item) {
	                if (col == colsToAppend1) {
	                    rows = Math.floor(rows);
	                }

	                if (rowIndex >= rows) {
	                    col++;
	                    rowIndex = 0;
	                    $column = null;
	                }

	                if ($column == null) {
	                    $column = me.buildColumn();
	                    me.addColumn($column);
	                }

	                $column.append(me.buildItem(item));
	                rowIndex++;
	            });
	        },

	        addColumn: function($column) {
	            this.$el.append($column);
	        },

	        buildItem: function(options) {
	            var $link = $('<a />')
	                .addClass('enc-columns-link')
	                .attr({
	                    title: options.title,
	                    href: options.link
	                })
	                .html(options.html);
	            return $('<li />').addClass('enc-columns-row').append($link);
	        },

	        buildColumn: function() {
	            return $('<ul />')
	                .css({
	                    width: this.columnWidthPerc + '%'
	                })
	                .addClass('enc-columns-col');
	        },

	        getColumnRows: function() {
	            return this.items.length / this.columnsCount;
	        },

	        getItems: function() {
	            return this.$el.find('a').map(function(index, item) {
	                return {
	                    link: $(item).attr('href'),
	                    title: $(item).attr('title'),
	                    html: $(item).html(),
	                    width: $(item).outerWidth()
	                };
	            }).toArray();
	        },

	        getContainerWidth: function() {
	            return this.$el.parent().width();
	        },

	        getColumnsCount: function() {
	            return Math.floor(this.containerWidth / this.columnWidth);
	        },

	        getColumnWidthPerc: function() {
	            return 100 / this.columnsCount;
	        },

	        maxWidth: function() {
	            return Math.max.apply(
	                null,
	                $(this.items).map(function(index, item) {
	                    return item.width;
	                }).toArray());
	        }
	    });

		return EnhancedColumnsView;
	};

	define(dependencies, init);
})();