function add_vote_user(poll_id){ 
	jQuery(document).ready(function($) {	
		var answ_id = $('.poll_id_'+poll_id+' input:radio[name=answ]:checked').val();
		// console.log("answ_id" + answ_id);
		// console.log(poll_id);
		var data = {
			'action': 'add-vote',
			'nonce': ajax_obj.nonce,
			'poll_id':poll_id,
			'answ_id': answ_id
		};

		jQuery.ajax({
			  type: "POST",
			  url: ajax_obj.url,
			  data: data,
			  success: function(res){
			  	// var empt_for = 0;
			  	// for(var i=0;i<50000;i++){
			  	// 	empt_for = i + i;
			  	// }
			  	res = res.substr( 0, res.length-1 );
			    // $("#alex-poll-wrap").html(res);				    
			    $(".poll_id_" + poll_id).html(res);				    
			     // console.log( res );
			  },
			  beforeSend: function() {
			    // $(".poll_id_" + poll_id).text("ЖДИТЕ....");
			    $(".poll_id_" + poll_id).html("<img src='"+ajax_obj.loader+"' />");
			  }
		});
	});
}

