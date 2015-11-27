//namespaces
var Yarmarka = {
    Modules:{},
    UI:{},
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

//Yarmarka.UI.EnhancedColumnsView
(function () {
	var EnhancedColumnsView = function (options) {
        this.options = {
            forceColumnsCount: false,
            forceOneColumnMobile: false
        };
        $.extend(this.options, options);
        this.$el = $(options.el);
        this.initialize(options);
    };

    $.extend(EnhancedColumnsView.prototype, {
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

            //clear container
            this.$el.empty();

            //debug
            console.log('Enhanced columns view render: ', this);

            //build table
            this.build();
            //trigger resize event -> allow others to react on changes
            //this.$el.trigger('resize');
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

	Yarmarka.UI.EnhancedColumnsView = EnhancedColumnsView;
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
            console.log(this.$el);
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
            console.log('Render AutoCollapse');
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
            console.log('AutoCollapse: animate height to', height);
            return this.$el.animate({ height: height + 'px' }, this.options.animateDuration);
        }
    });

    //export plugin
    Yarmarka.UI.AutoCollapse = AutoCollapse;
})();