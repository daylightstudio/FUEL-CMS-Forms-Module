// jqx.load('plugin', 'date');

FormsController = jqx.createController(fuel.controller.BaseFuelController, {

	init: function(initObj){
		this._super(initObj);
	},

	items : function(){
		
		// call parent
		this._super();
		var _this = this;
		/*$('.ico_export').unbind().click(function(e){
			e.preventDefault();
			if ($('#form_id').val() == '') {
				alert('Please select a form in the dropdown menu to the right of this button before exporting.');
				return false;
			}
			var url = $(this).attr('href');
			$('#form').attr('method', 'post').attr('action', url).submit();
		})*/

	},

	add_edit: function(initSpecFields){
		var _this = this;
		// do this first so that the fillin is in the checksaved value
		fuel.controller.BaseFuelController.prototype.add_edit.call(this, initSpecFields);

		// To trigger change of form field
		$(document).on('cloned.fuel', function(e){
			$(e.clonedNode).find('select').trigger('change')
		});
	}
});