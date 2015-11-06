
var conformityModel = Backbone.Model.extend({
	url : '/ajax/massload/conformity'
});

var conformityView = Backbone.View.extend({
	events : {
		'click .fn-save' 	: 'saveConformity',
		'click .fn-delete'  : 'deleteConformity',
		'click .fn-add'  	: 'addConformity',
		'change .fn-conformity'  : 'changeConformity',
		'keyup .fn-conformity'  : 'keyupConformity'
	},
	initialize : function () {
		this.value = this.$el.find(".fn-conformity");
	},
	saveConformity : function() {
		var self = this;
		
		this.model.save({"conformity" : $(this.value).val().trim()}, { success: function(model, response){
								self.model.set("conformity", $(self.value).val().trim());
								self.changeState();
                            },
                            error: function(model, response){
                                console.log("error", response);
                            }});
	},
	deleteConformity : function() {
		$(this.value).val("");
		this.saveConformity();
	},
	changeConformity : function() {
		this.$el.find(".fn-save").click();
	},
	keyupConformity : function() {
		this.changeState();
	},
	changeState : function() {
		if ($(this.value).val().trim() != this.model.get("conformity"))
			this.$el.find("input").removeClass("saved").addClass("unsaved");
		else
			this.$el.find("input").removeClass("unsaved").addClass("saved");	
	}
});

var applicationView = Backbone.View.extend({

	initialize : function () {
		var user_id = $("#fn-user").val();        
        _.each($(".fn-row"), function(item){
        	var input = $(item).find(".fn-conformity");
        	var model = new conformityModel({
				        		value : $(input).data("value"),
				        		type : $(input).data("type"),
				        		massload : $(input).data("ml"),
				        		conformity : $(input).val().trim(),
				        		user_id : user_id
				        	});
        	var conformity = new conformityView({el : item, model : model});
        });
    },
});

$(document).ready(function() {

    var application = new applicationView();

});
