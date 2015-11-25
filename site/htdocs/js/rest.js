var rest =
{	
	'setMethod': function(parent)
	{
		if (Object.prototype.toString.call(parent) == '[object Object]') {
			parent.find('form.rest input:submit').click(function(e){
				var method = $(e.target.form).attr('method').toUpperCase();
				if (method == 'PUT' || method == 'DELETE') {
					var elem = $('<input>').attr('type', 'hidden').attr('name', '_method');
					elem.val(method);
					$(e.target.form).attr('method', 'POST');
					$(e.target.form).append(elem);
				}
			});
		} else {
			var forms = document.getElementsByTagName('form');
			for (var i = 0; i < forms.length; i++) {
				forms[i].onsubmit = function() {
					var method = this.getAttribute('method').toUpperCase();
					if (method == 'PUT' || method == 'DELETE') {
						var elem = document.createElement('input');
						elem.type = 'hidden';
						elem.name = '_method';
						elem.value = method;
						this.method = 'POST';
						this.appendChild(elem);
					}
				}
			}
		}
	}
}

if(typeof(jQuery) == 'function') {
	$(function(){
		rest.setMethod($('body'));
	});	
} else {
	window.onload = function() {
		rest.setMethod(document.body);
	}
}

