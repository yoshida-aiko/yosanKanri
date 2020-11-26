function popmenu(options) {

	var opts = {
		x : event.clientX,
		y : event.clientY,
		key : '',
		id : '',
		items : {},
		callback : null
	}


    if (typeof options === 'object') {
        $.extend(opts, options);
    } else {
    	return false;
    }

	var orderrequestid = opts.key;
	var ctrlid = opts.id;


	$("#" + ctrlid + " > ul").css('display','block');
	$("#" + ctrlid + " > ul").css({'left' : opts.x, 'top' : opts.y });

	$("#" + ctrlid + " > ul > li:not(.popmenu-header)").bind("click",function(e){

		if (ctrlid == 'popmenu-budgetlist'){
			var id = $(this).attr('id');
			this.id = id;
			var budgetid = id.replace('BudgetId-','');
			/*opts.callback(this);*/
		}
		else if (ctrlid == 'popmenu-orderrequestlist'){
			var budgetid = '-1';
		}
		var ret = updateOrderRequestGiveBudget(budgetid,orderrequestid);
		var deferred = ret.deferred;
		deferred.done(function(){
			$.unblockUI();
			if (ret.result) {
				location.reload();
			}
			else {
				location.href = './Error/systemError';
			}
		});

		$(this).parent().hide();
		/*$("[role='popmenu-layer']").remove();*/
		$("#" + ctrlid).css('display','block');
	});

	$("#" + ctrlid + " > ul").focus();

	$("#" + ctrlid + " > ul > li:not(.popmenu-header)").hover(function() {
		$(this).addClass('popmenu-hover');
	}, function() {
		$(this).removeClass('popmenu-hover');
	});
	
	/*$("#" + ctrlid + " .popmenu-layer").mousedown(function(event) {*/
	$("#" + ctrlid).mousedown(function(event) {
			$("#" + ctrlid + " > ul").css('display','none');
		/*$("[role='popmenu-layer']").remove();*/
		$("#" + ctrlid).css('display','block');
	});
	
	$(window).blur(function(event) {
		$("#" + ctrlid + " > ul").css('display','none');
		/*$("[role='popmenu-layer']").remove();*/
		$("#" + ctrlid).css('display','block');
	});
	// 
	$(window).mousedown(function(event){
		$("#" + ctrlid + " > ul").css('display','none');
		/*$("[role='popmenu-layer']").remove();*/
		$("#" + ctrlid).css('display','block');
	})
	
	$("#" + ctrlid + " > ul").mousedown(function(event){
		event.stopPropagation();
	})
	
	$(window).resize(function(event) {
		$("#" + ctrlid + " > ul").css('display','none');
		/*$("[role='popmenu-layer']").remove();*/
		$("#" + ctrlid).css('display','block');
	});
}
