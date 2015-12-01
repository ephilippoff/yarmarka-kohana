//namespaces
var Yarmarka = {
    Modules:{},
    UI:{
        Columns: {}
    },
    Instances:{},
    Helpers:{}
};

//Helpers
(function () {
    var Helpers = {
        detectMobile: function () {
            return this.testMaxWidth(767);
        },

        testMaxWidth: function (val) {
            return this.testMediaQuery('screen and (max-width:' + val + 'px)');
        },

        testMediaQuery: function (query) {
            return window.matchMedia(query).matches;
        }
    };

    $.extend(Yarmarka.Helpers, Helpers);
})();

//Yarmarka.UI.Columns.View
(function () {
	var EnhancedColumnsView = function (options) {
        $.extend(this.options, options);
        this.$el = $(options.el);
        this.initialize(options);
    };

    $.extend(EnhancedColumnsView.prototype, {
        options: {
            forceColumnsCount: false,
            forceOneColumnMobile: false
        },

        initialize: function(options) {
            this.$el.addClass('enc-columns-table');
            //calculate only first time
            this.items = this.getItems();
            this.initialMaxWidth = this.maxWidth();

            //process options
            if (this.options.forceColumnsCount !== false) {
                this.containerWidth = this.getContainerWidth();
                this.columnWidth = this.containerWidth / this.options.forceColumnsCount;
            }

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
            this.containerWidth = this.getContainerWidth();

            //process forceOneColumnMobile
            if (this.options.forceOneColumnMobile && Yarmarka.Helpers.detectMobile()) {
                this.columnWidth = this.containerWidth;
            } else {
                this.columnWidth = this.initialMaxWidth;
            }

            //fill build options
            this.items = this.getItems();
            this.columnsCount = this.getColumnsCount();
            this.columnRows = this.getColumnRows();
            this.columnWidthPerc = this.getColumnWidthPerc();
            this.itemsTable = this.getItemsTable(
                this.items,
                this.columnsCount,
                this.columnRows);

            this.draw();
        },

        draw: function () {
            //clear container
            this.$el.empty();

            //build table
            this.build();
            //trigger resize event -> allow others to react on changes
            //this.$el.trigger('resize');
        },

        getItemsTable: function (items, columns, rows) {
            var table = [];
            //init table
            for(var i = 0;i < columns;i++) {
                var column = [];
                for(var j = 0;j < Math.ceil(rows);j++) {
                    column.push(null);
                }
                table.push(column);
            }
            
            //fill
            var colsToAppend1 = items.length - columns * Math.floor(rows);
            var col = 0;
            var rowIndex = 0;
            $.each(items, function (index, item) {
                if (col == colsToAppend1) {
                    rows = Math.floor(rows);
                }

                if (rowIndex >= rows) {
                    col++;
                    rowIndex = 0;
                }

                table[col][rowIndex] = item;
                rowIndex++;
            });

            return table;
        },

        build: function() {
            for(var i = 0;i < this.itemsTable.length;i++) {
                //create column
                var $column = this.buildColumn();
                this.addColumn($column);

                for (var j = 0;j < this.itemsTable[i].length;j++) {
                    if (this.itemsTable[i][j] === null) {
                        continue;
                    }

                    var $row = this.buildItem(this.itemsTable[i][j]);
                    $column.append($row);
                }
            }
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
            var me = this;
            return this.$el.find('a').map(function(index, item) {
                return me.convertItem(item, index);
            }).toArray();
        },

        convertItem: function (domItem, index) {
            return {
                link: $(domItem).attr('href'),
                title: $(domItem).attr('title'),
                html: $(domItem).html(),
                width: $(domItem).outerWidth()
            };
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

	Yarmarka.UI.Columns.View = EnhancedColumnsView;
})();

//Yarmarka.UI.Columns.OrderedHeight
// Using:
// - Yarmarka.UI.AutoCollapse
(function () {
    var Base = Yarmarka.UI.Columns.View;
    var Me = function (options) {
        Base.apply(this, arguments);
    };

    $.extend(Me.prototype, Base.prototype, {
        options: $.extend({
            enableAutoCollapse: true,
            autoCollapseOptions: {
                allowedHeight: 100
            }
        }, Base.prototype.options),

        initialize: function (options) {
            if (this.options.enableAutoCollapse) {
                this.options.autoCollapseOptions.el = this.$el;
                this.autoCollapseObject = new Yarmarka.UI.AutoCollapse(options.autoCollapseOptions);
            }

            Base.prototype.initialize.apply(this, arguments);
        },

        draw: function () {
            if (this.options.enableAutoCollapse) {
                this.height = this.getItemHeight();
                this.visibleRowsCount = Math.floor(this.options.autoCollapseOptions.allowedHeight / this.height);
                this.visibleItemsCount = this.visibleRowsCount * this.columnsCount;

                if (this.visibleItemsCount < this.items.length) {
                    var visibleItemsTable = this.getItemsTable(
                        this.takeItemsPart(0, this.visibleItemsCount), 
                        this.columnsCount,
                        this.visibleRowsCount);
                    var hiddenItemsTable = this.getItemsTable(
                        this.takeItemsPart(this.visibleItemsCount, this.items.length),
                        this.columnsCount,
                        this.columnRows - this.visibleRowsCount);
                    this.itemsTable = $.merge(visibleItemsTable, hiddenItemsTable);
                }
            }
            Base.prototype.draw.apply(this, arguments);
        },

        takeItemsPart: function (start, stop) {
            var ret = [];
            for(var i = start;i < stop;i++) {
                ret.push(this.items[i]);
            }
            return ret;
        },

        getItemHeight: function () {
            var items = this.getItems();
            if (items.length) {
                return items[0].height;
            }
            return 0;
        },

        convertItem: function(domItem, index) {
            var ret = Base.prototype.convertItem.apply(this, arguments);
            return $.extend(ret, {
                height: $(domItem).outerHeight()
                //debug
                , html: ret.html + ' ' + index,
            });
        }
    });

    Yarmarka.UI.Columns.OrderedHeight = Me;
})();

//Yarmarka.UI.AutoCollapse
(function () {
    var AutoCollapse = function (options) {
        this.options = {
            cssClass: 'auto-collapse',
            expandCssClass: 'auto-collapse-expand',
            el: '[data-auto-collapse]',
            expandLabel: 'Посмотреть все',
            animateDuration: 1500,
            height: 100
        };
        $.extend(this.options, options);

        this.initialize();
    };

    $.extend(AutoCollapse.prototype, {
        initialize: function () {
            //wrap element in container
            this.$content = $(this.options.el);
            this.$content.wrap($('<div />').addClass(this.options.cssClass));
            this.$el = this.$content.parent();
            //append the expand button
            this.$expand = $('<a />')
                .addClass(this.options.expandCssClass)
                .attr({ href: '#' })
                .html(this.options.expandLabel)
                //initially hidden
                .hide()
                //add to container
                .appendTo(this.$el);

            //call render
            this.render();

            //bind events
            this.bindEvents();
        },

        render: function () {
            //calc content height
            this.updateContentHeight();
            //check if we need to collapse
            if (this.contentHeight <= this.options.allowedHeight) {
                //hide all stuff
                this.$expand.fadeOut();
                //and exit
                return;
            }

            //set styles to container
            this.$el.css({
                //restrict max-height
                height: this.options.allowedHeight + 'px'
            });
            //enable expand button
            this.$expand.fadeIn();
        },

        bindEvents: function () {
            var me = this;

            //expand button click event handler
            this.$expand.on('click', function (e) {
                e.preventDefault();
                me.expand();
            });

            //element resize event handler
            this.$content.on('resize', function (e) {
                //just render it
                me.render();
            });
        },

        updateContentHeight: function() {
            this.contentHeight = this.$content.outerHeight();
        },

        expand: function () {
            var me = this;
            this.state(true).promise().done(function () {
                //restore height to auto -> avoid problems when resize
                me.$el.css('height', 'auto');
            });
        },

        collapse: function () {
            this.state(false);
        },

        state: function (value) {
            this.updateContentHeight();
            this.$expand[value ? 'fadeOut' : 'fadeIn']();
            return this.run(value ? this.contentHeight : this.options.allowedHeight);
        },

        run: function (height) {
            return this.$el.animate({ height: height + 'px' }, this.options.animateDuration);
        }
    });

    //export plugin
    Yarmarka.UI.AutoCollapse = AutoCollapse;
})();