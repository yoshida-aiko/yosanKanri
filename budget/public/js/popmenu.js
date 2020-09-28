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


	/*var menu_items = '';

	for (var item in opts.items) {

		var name = '';
		if($.type(opts.items[item].name) === "string") {
			name = opts.items[item].name + " ";
		}
		var icon = '';
		if($.type(opts.items[item].icon) === "string") {
			icon = opts.items[item].icon + " ";
		}
		var divid = '';
		if(opts.items[item].divid === true) {
			divid = "<hr>";
		} 

		menu_items += "<li id='" + item + "' name='"+ name +"''>" + icon + opts.items[item].name + "</li>" + divid;
	}
	menu_items = "<div role='popmenu-layer'><ul role='popmenu'><li class='popmenu-header'><span class='fa fa-hand-o-left'></span>予算リストへ</li>" + menu_items + "</ul></div>";
	*/
	/*$("body").append(menu_items);*/
	/*$("div.container").append(menu_items);*/

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
		var deferred = updateOrderRequestGiveBudget(budgetid,orderrequestid);
		deferred.done(function(){
			$.unblockUI();
			location.reload();
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
