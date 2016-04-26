// admin scripts

jQuery(document).ready(function($) {
	// alert("test");
	$("#add_answ").click(function(e){
		e.preventDefault();
		var count = $(".table1 tr.answ").length;
		// count = count-2+1;
		count = count+1;
		$(".table1").append('<tr class="answ" id="poll_answ_'+ count +'"><th scope="row"><label for="answ'+ count +'">' +dataL10n.l_answer +' '+ count +'</label></th><td><input name="answ'+ count +'" type="text" id="answ'+ count +'" class="regular-text" /><td></tr>');
	});

	$("#add_answ_html").click(function(e){
		e.preventDefault();
		var count = $(".table1 tr.answ").length;
		count = count+1;
		$(".table1").append('<tr class="answ" id="poll_answ_'+ count +'"><th scope="row"><label for="answ'+ count +'">'+dataL10n.l_answer +' '+ count +'</label></th><td><input name="answ'+ dataL10n.l_answer +' '+ count +'" type="text" id="answ'+ count +'" class="regular-text"/></td><td><input type="submit" onclick="del_answ_html(' + count + ');return false;" name="del_answ" class="button" value="'+ dataL10n.del_poll+'"></td></tr>');
	});

});

function del_answ_html(answ_id){
	jQuery(document).ready(function($) {			
		 $("#poll_answ_" + answ_id).remove();

		 var count = $(".table1 tr.answ").length;
		 var k=1;
		 for(var i=0; i<count; i++){
		 	$("tr.answ label").eq(i).text(dataL10n.l_answer +' '+(i+k));
		 	// console.log(count);
		 	// console.log( $("tr.answ label").eq(i) );
		 }
	});
}



// admin ajax scripts

function delete_poll(poll_id){
	jQuery(document).ready(function($) {
			//"Вы действительно хотите удалить этот опрос?"
			if (confirm(dataL10n.conf_del_poll)) {
				var data = {
					'action': 'admin-poll',
					'poll_id': poll_id
				};
				jQuery.post(ajax_object.ajax_url, data, function(response) {
					// alert('res: ' + response);
					if( !$("#message").hasClass( "mes-del" ) )
					$("#for_message").append('<div id="message" class="updated fade mes-del"><p style="color: green;">'+dataL10n.success_del_poll+'</p></div>');
					 $("#delete_poll_" + poll_id).remove();
				});
			}

		});

}

function poll_answ(poll_id, answ_id){
	jQuery(document).ready(function($) {

		console.log(answ_id);
			//"Вы действительно хотите удалить этот ответ?" 
			if (confirm(dataL10n.conf_del_answ)) {
				var data = {
					'action': 'admin-del-answ',
					'poll_id': poll_id,
					'answ_id': answ_id
				};
				jQuery.post(ajax_object.ajax_url, data, function(response) {
					// alert('res: ' + response);
					if( !$("div").is("#message") )
					{ 
						//Ответ успешно удален
						$("#for_message").append('<div id="message" class="updated fade"><p style="color: green;">'+dataL10n.success_del_answ+'</p></div>');
					}
					 $("#del_answ_" + answ_id).remove();
 					 var count = $(".table1 tr.answ").length;
 					 var k=1;
					 for(var i=0; i<count; i++){
					 	$("tr.answ label").eq(i).text(dataL10n.l_answer +' '+(i+k));
					 	// console.log(count);
					 	// console.log( $("tr.answ label").eq(i) );
					 }
				});
			}

		});

}