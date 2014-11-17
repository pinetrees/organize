$(function () {

    var $contextMenu = $("#contextMenu");
    var $field = $contextMenu.find('li').filter(function() { return $(this).hasClass('field'); });
    console.log($field.html());
    var $cell;
    var $ID;
    var node;
    var name;
    var val;
    var $parent;

    $("body").on("contextmenu", ".list-group-item", function (e) {
	console.log($(this).attr('class'));
	if(!$(this).hasClass('object')) return false;
	console.log('s');
	if( editing == true ) return;
        $contextMenu.find('li').show();
        if( multiselect == true ) $contextMenu.find('li').not('.multiselect').hide();
        $cell = $(this)
	$('.class-option').removeClass('active-selection');
	$.each($('.class-option'), function(index, value){
		if( $cell.hasClass($(value).attr('data-value')) ) $(value).addClass('active-selection');
	});
	$('.field-option').removeClass('active-selection');
	if( $(this).attr('data-type') == 'type' ) $.each($('.field-option'), function(index, value){
		if( $$fields[$cell.find('.name').html().toLowerCase()].indexOf(($(value).attr('data-value'))) != -1 ) $(value).addClass('active-selection');
	});
	$('.type-option').removeClass('active-selection').filter(function(){ return ( $cell.attr('data-type') == $(this).attr('data-value') ); }).addClass('active-selection');
	(($cell.attr('data-type') == 'type')) ? $field.show() : $field.hide();
        $parent = $cell.closest('.list-group');
        $val = $(this).find('.name').html();
	$ID = $(this).attr('data-id');
        $contextMenu.css({
            display: "block",
            left: e.pageX,
            top: e.pageY
        });
        return false;
    });

    $contextMenu.on("click", "a", function () {
        $contextMenu.hide();
    });

    $(document).click(function () {
        $contextMenu.hide();
    });

    $contextMenu.find('.rename').click(function() {
      $_ = $(input);
      $cell.html($_).find('input').val($val).focus().blur(rename);
    });

    $contextMenu.find('.delete').click(obdelete);
    $contextMenu.find('.complete').click(obcomplete);

    function rename() {

      val = $(this).val();
      $ID = $cell.attr('data-id');
      if( val == '' ) return obdelete();
      $.post('/process', { action: 'rename', ID: $ID, name: val, parent: $parent.attr('data-id'), type: $parent.attr('data-type') }, function(res) {
        console.log(res);
        $cell.html($(span).addClass('name').html(val));
      });

    }

    var time;
    function obdelete() {

      time = new Date().getTime();
      if( multiselect == true ) return $.post('/process', { action: 'multidelete', IDs: selection }, function(res) {
	console.log(new Date().getTime() - time);
	console.log(res);
        $selection.remove();
	$('#primary-row').find('.col-plain').filter(function(){ return ( $(this).index() > $column.index() ); }).remove();
	multiselect = false;
      });
      console.log($parent.attr('data-id'));
      $.post('/delete', { ID: $ID, name: $val, parent: $parent.attr('data-id'), type: $parent.attr('data-type') }, function(res) {
	console.log(new Date().getTime() - time);
	console.log(res);
	$column = $cell.closest('.col-plain');
	if($cell.hasClass('list-group-item-active')) $('#primary-row').find('.col-plain').filter(function(){ return ( $(this).index() > $column.index() ); }).remove();
        $cell.remove();
      });

    }

    function obcomplete() {

      $.post('/process', { action: 'complete', ID: $ID }, function(res) { console.log(res); element.css('background', 'lightgreen'); });

    }

    $('.style .color').click( function() {
      attribute = $(this).attr('data-attribute');
      value = $(this).attr('data-value');
      $.post('/process', { action: 'style', ID: $ID, attribute: attribute, value: value }, function(res){console.log(res); element.css(attribute, value)});
    });
    
    $('.style .none').click( function() {
      attribute = $(this).attr('data-attribute');
      value = 'none';
      $.post('/process', { action: 'style', ID: $ID, attribute: attribute, value: value }, function(res){console.log(res); element.css(attribute, value)});
    });
    
    $('.type-option').click(function() {
      type = $(this).attr('data-value');
      console.log($ID);
      $.post('/process', { action: 'type', ID: $ID, type: type }, function(res){
        console.log(res);
        $cell.attr('data-type', type);
      }); 
    });

    $('.class-option').click(function() {
      classification = $(this).attr('data-value');
      console.log($ID);
      $.post('/process', { action: 'class', ID: $ID, classification: classification }, function(res){
        console.log(res);
        $cell.toggleClass(classification);
      }); 
    });

    $('.field-option').click(function() {
      field = $(this).attr('data-value');
      console.log("FIELD: " + field);
      $.post('/process', { action: 'addfield', ID: $ID, field: field }, function(res){
        console.log(res);
      });
    });

	$('.do').click(function() {
		route = $(this).attr('action').replace(/-/g, '/');
		$.post('/' + route, { ID: $ID }, function(res){
			console.log(res);
		});
	});

});
