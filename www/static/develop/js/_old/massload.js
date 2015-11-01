var logCollection = Backbone.Collection.extend({

});

var applicationView = Backbone.View.extend({
    el : 'fn-main-cont',
    step : 1,

    _init_ajax_upload : function () {
        var self = this;


        new AjaxUpload('fn-userfile-upload', {
            action: '/ajax/massload/checkfile',
            name: 'file',
            data : {context :self},
            autoSubmit: true,
            onSubmit: self.onFileSubmit,
            onComplete: self.onFileLoaded
        });

    },

    initialize : function () {
        this.log = new logCollection();
        this.log.on("add", this.writelog);
        this._init_ajax_upload();
    },

    onFileSubmit : function(filename, response) {
        var self = this._settings.data.context;
        self.category_id = $("#fn-category").val();
        self.user_id = $("#fn-user").val();
        self.ignore_errors = ($("#fn-ignore_errors").prop("checked")) ? 1 : 0;
        this.setData({ context : self, category_id : self.category_id, ignore_errors : self.ignore_errors, user_id : self.user_id});
        self.clearlog();
        self.tracelog("info", "Загружаю файл");
        self.waiting();
        //TODO показать индикацию загрузки
    },

    onFileLoaded : function (filename, response) {
        var data = null;
        var self = this._settings.data.context;         

        self.tracelog("info", "Файл загружен. Начинаю проверку...");

        if (response) {
            data = $.parseJSON(response);
        }
        else 
            self.stopWaiting("Ошибка загрузки"); 

        self.tracelog("info", "В загружаемом файле найдено " + data.count + " объявлений");

        if (data.data != "ok") {
            self.stopWaiting("При проверке файла обнаружены следующие " + data.errorcount + " ошибок: ");
            if (data.errors) {
                self.writeErrors(data.errors);
            }
            return;
        }

        self.count = data.count;
        self.row_num = 1;
        self.ignoreCount = 0;

        if (!data.count) {
            self.stopWaiting("Количество строк в файле = 0");
            return;
        }

        var params = {
            category_id : self.category_id,
            pathtofile : data.pathtofile,
            pathtoimage : data.pathtoimage,
            user_id : data.user_id,
            row_num : self.row_num
        }

        if(data.errorcount>0)
            self.tracelog("info", "Файл проверен. Будет проигнорировано "+data.errorcount+" ошибок. Начинаю сохранять объявления...");
        else
            self.tracelog("info", "Файл проверен. Начинаю сохранять объявления...");

        self.saveStrings(params);

    },

    saveStrings : function(params){ 
        var self = this;

        self.ajaxSave(params, function(data){          

            if (data.data[0].object_id)
                self.tracelog("successfull", data.data);
            else if (data.data[0].error) {
                var error = data.data[0].error
                var external_id = data.data[0].external_id

                if (error.signature){
                    self.tracelog("error", 'ID: '+external_id+' - '+error.signature);
                } else
                if (error.contacts){
                    self.tracelog("error", 'ID: '+external_id+' - '+error.signature);
                } else
                if (error.plan){
                    self.tracelog("error", 'ID: '+external_id+' - '+error.plan);
                    self.tracelog("error", 'ID: '+external_id+' - '+error.plan_description);
                } else {
                    self.tracelog("error", 'ID: '+external_id+' - '+data.error);
                }
            }
            
        });
    },

    ajaxSave : function(params, callback){
        var self = this;
        
        $.post( "/ajax/massload/load_next_strings", params, function( data ) {
            var result = data.data;
            
            if (result.length>0){
                callback(data);
            } else {
                self.ignoreCount++;
            }
            
            if (params.row_num < self.count) {
                params.row_num++;
                self.saveStrings(params);                                        
            } else {
                self.stopWaiting("Загрузка завершена"); 
                self.tracelog("error",  self.ignoreCount + " строк с ошибками были проигнорированы."); 
            }

            
        }, 'json');
    },

    clearlog : function (){
        $('#fn-log-area').html("");
    },

    writelog : function(data){

        var logtext = $('#fn-log-area').html();
        var text = "";
        var end = true;

        text += "</br>";

        switch(data.get("type")){
            case "info":
                text += "<span class='green'>"+data.get("data")+"</span>";
            break;
            case "successfull":
                var objects = data.get("data");
                var log_objs = [];
                _.each(objects, function(item){
                    var external_info = 'ID: '+item.external_id+' - ';
                    var object_info = ' <a href="http://'+location.hostname+'/detail/'+item.object_id+'" target="_blank">'+item.object_id+'</a> ';
                    var edit_info = (item.is_edit) ? "(обновление)": "(создание)";
                    log_objs.push(external_info+object_info+edit_info);
                });
                text += "<span class='green'>"+"Объявления успешно созданы/обновлены: " + log_objs.join(", ")+"</span>"
            break;
            case "error":
                var objects = data.get("data");
                var decoded = $('<div/>').html(objects).text();
                text += "<span>"+decoded+"</span>";
            break;
            default:
                end = false;
            break;
        }

        text += "</br>";
        
        $('#fn-log-area').html(logtext+text);
        var elm = $('#fn-log-area');
        elm.scrollTop(elm.get(0).scrollHeight);
    },

    writeErrors : function (data){
        console.log(data);

        var self = this;
        _.each(data, function(item){
            self.tracelog("error", item);
        });
    },

    tracelog : function (type, data){
        this.log.add({type : type, data : data })
    },

    waiting : function () {
        $('#fn-log-area').html($('#fn-log-area').html());
        this.timeout = setInterval(function(){
           
            $('#fn-log-area').html($('#fn-log-area').html()+". ");
        }, 100);
    },

    stopWaiting : function(text){
        window.clearTimeout(this.timeout);
        this.tracelog("info", text);
    }

});

$(document).ready(function() {

    var application = new applicationView();

});
