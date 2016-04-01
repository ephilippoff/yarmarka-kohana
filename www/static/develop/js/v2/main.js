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
        },

        qsort: function (arr, l, r, p) {
            if (typeof(p) !== 'function') {
                p = function (a, b) { return a < b; }
            }

            var qs = function (arr, l, r, p) {
                if (l < r) {
                    var c = part(arr, l, r, p);

                    qs(arr, l, c, p);
                    qs(arr, c + 1, r, p);
                }

                return arr;
            };

            var part = function (arr, l, r, p) {
                var x = arr[r];
                var j = l - 1;

                for(var i = l; i < r; i++) {
                    if (p(arr[i], x)) {
                        j++;
                        var tmp = arr[i];
                        arr[i] = arr[j];
                        arr[j] = tmp;
                    }
                }

                if (j < r - 1) {
                    j++;
                    var tmp = arr[j];
                    arr[j] = arr[r];
                    arr[r] = tmp;
                }

                return j;
            };

            return qs(arr, l, r, p);
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
            forceOneColumnMobile: false,
            elementSelector: 'a',
            itemWidthSelector: null
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
            this.containerHeight = this.getContainerHeight();
            this.itemHeight = this.getItemHeight();

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
            var $link = $('<div />')
                .addClass('enc-columns-item')
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
            return this.$el.find(this.options.elementSelector).map(function(index, item) {
                return me.convertItem(item, index);
            }).toArray();
        },

        convertItem: function (domItem, index) {
            return {
                html: $(domItem).html(),
                width: this.options.itemWidthSelector 
                    ? $(domItem).find(this.options.itemWidthSelector).outerWidth() 
                    : $(domItem).outerWidth()
            };
        },

        getItemHeight: function () {
            return this.$el.find(this.options.elementSelector).height();
        },

        getContainerHeight: function () {
            return this.$el.parent().height();
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
                allowedHeight: 85
            }
        }, Base.prototype.options),

        initialize: function (options) {
            var me = this;
            this.sortAll = false;
            if (this.options.enableAutoCollapse) {
                this.options.autoCollapseOptions.el = this.$el;
                this.autoCollapseObject = new Yarmarka.UI.AutoCollapse(options.autoCollapseOptions);
                this.autoCollapseObject.$content.on('expand_start', function () {
                    me.sortAll = true;
                    me.render();
                });
            }

            Base.prototype.initialize.apply(this, arguments);
        },

        draw: function () {
            var p = function (a, b) {
                return $(a.html).text() < $(b.html).text();
            };
            if (this.options.enableAutoCollapse) {
                this.height = this.getItemHeight();
                this.visibleRowsCount = Math.floor(this.options.autoCollapseOptions.allowedHeight / this.height);
                this.visibleItemsCount = this.visibleRowsCount * this.columnsCount;

                if (this.visibleItemsCount < this.items.length) {
                    
                    var tmp = this.takeItemsPart(0, this.visibleItemsCount);
                    var visibleItemsTable = this.getItemsTable(
                        Yarmarka.Helpers.qsort(tmp, 0, tmp.length - 1, p), 
                        this.columnsCount,
                        this.visibleRowsCount);
                    tmp = this.takeItemsPart(this.visibleItemsCount, this.items.length);
                    var hiddenItemsTable = this.getItemsTable(
                        Yarmarka.Helpers.qsort(tmp, 0, tmp.length - 1, p),
                        this.columnsCount,
                        this.columnRows - this.visibleRowsCount);
                    this.itemsTable = $.merge(visibleItemsTable, hiddenItemsTable);
                } 
                if (this.sortAll) {
                    var x = Yarmarka.Helpers.qsort(this.items, 0, this.items.length - 1, p);
                    this.itemsTable = this.getItemsTable(
                        x,
                        this.columnsCount,
                        this.columnRows);
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
                , html: ret.html,
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
            expandCssClass: 'auto-collapse-expand more-button button bg-color-blue',
            expandWrapperCssClass: 'auto-collapse-expand-wrapper',
            el: '[data-auto-collapse]',
            expandLabel: 'Показать весь список',
            animateDuration: 1500,
            height: 80
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
            this.$expandWarpper = $('<div />')
                .addClass(this.options.expandWrapperCssClass)
                .appendTo(this.$el);
            this.$expand = $('<a />')
                .addClass(this.options.expandCssClass)
                .attr({ href: '#' })
                .html(this.options.expandLabel)
                //initially hidden
                .hide()
                //add to container
                .appendTo(this.$expandWarpper);

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
            this.$content.trigger('expand_start');
            this.state(true).promise().done(function () {
                //restore height to auto -> avoid problems when resize
                me.$el.css('height', 'auto');
            });
        },

        collapse: function () {
            var me = this;
            this.state(false).promise().done(function () {
                me.$content.trigger('resize');
            });
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