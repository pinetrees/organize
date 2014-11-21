var global_data = {};
//var $$$fields = new Array();
//var $$fields = new Array();
var element = $('<div></div>');
var $element;
element.attr('data-id', 0);
var multiselect;
var selection;
var $row = $('.row').first();
$( ".list-group" ).sortable({ items: "> .object", connectWith: '.list-group' }).on( "sortupdate", sortUpdate ).on( "sortreceive", sortReceive ).on( "sortremove", sortRemove );
$('.row').sortable({ items: "> .col-plain", connectWith: '.row' });
function sortRemove() {
  console.log('Removed');
  console.log('');
}
var received;
var visible_columns;
function sortReceive() {
  received = true; 
  if( multiselect == true ) return $.post('/process', { action: 'multitransfer', IDs: selection, position: $div.index(), parent: $(this).attr('data-id') }, function(res){console.log(res); $div.before($div.find('.list-group-item').addClass('object')).remove(); });
  $.post('/process', { action: 'transfer', ID: element.attr('data-id'), position: element.index(), parent: $(this).attr('data-id') }, function(res){console.log(res);});
  console.log('Received');
  console.log('');
}
function sortUpdate() {
  if( $(this).attr('data-id') != element.closest('.list-group').attr('data-id') ) return;
  if ( received == true ) return received = false;
  console.log('List ID: ' + $(this).attr('data-id'));
  console.log('Element parent: ' + element.closest('.list-group').attr('data-id'));
  console.log('Index: ' + element.index());
  console.log('');
  $.post('/process', { action: 'order', ID: element.attr('data-id'), position: element.index(), parent: $(this).attr('data-id') }, function(res){
	console.log(res)
  });
}
$('.list-group-item').mousedown(setElement).mousedown(actionFilters);
function setElement(e) {
  $element = element;
  element = $(this);
}

var selection;
var $div;
function actionFilters(e) {
  if( e.shiftKey == true ) { 
    multiselect = true;
    $div = $('<div class="object"></div>');
    $element.before($div);
    $selection = $(this).parent().find('.list-group-item').filter( function() {
      return (( $(this).index() <= Math.max(element.index(), $element.index()) ) && ( $(this).index() >= Math.min(element.index(), $element.index()) ));
    })
    $.each($selection, function(index, value) { 
      $(this).appendTo($div).removeClass('object').addClass('list-group-item-active');
      selection.push($(value).attr('data-id'));
      console.log(selection);
    });
    $div.closest('.list-group').sortable( "destroy" ).sortable({ items: "> .object", connectWith: '.list-group' });
  }
}


var time = {};
time.this = new Date().getTime();
$('.list-group-item').not( $('.new') ).click(listClick);
function incrementTime() {
  //Depreciated
  return;
  time.last = time.this;
  time.this = new Date().getTime();
  time.change = (time.this-time.last)/1000;
  console.log(time.this-time.last);
  console.log($element.attr('data-id'));
  $.post('/process', { action: 'time', ID: $element.attr('data-id'), time: time.change }, function(res){
    console.log(res);
    $element.find('.time').filter(function() { return !$(this).hasClass('total'); }).html(parseInt($element.find('.time').html()) + parseInt(time.change));
  });
}
$(window).bind('beforeunload', function(){
  $element = element;
  incrementTime();
});
function listClick(e) {
  incrementTime();
  if( e.shiftKey == true ) return false;
  multiselect = false;
  selection = new Array;
  $(this).parent().find('.list-group-item').removeClass('list-group-item-active');
  $(this).addClass('list-group-item-active');
  $group = $(group).sortable({ items: "> .object", connectWith: '.list-group' }).on( "sortupdate", sortUpdate ).on( "sortreceive", sortReceive ).on( "sortremove", sortRemove );
  $column = $(this).closest('.col-plain')
  $row.find('.col-plain').filter(function(){ return ( $(this).index() > $column.index() ); }).remove();
  columns = $row.find('.col-plain').length;
  visible_columns = $row.find('.col-plain:visible').length;
  if( visible_columns == 6 ){
  //This is good and working to generate new rows
  //  $row = $(row).clone().insertAfter($row);	
	console.log('hide');
	//$row.find('.col-plain:visible').first().hide();
  } 
  $row.append($(col).append($group));
  visible_columns = $row.find('.col-plain:visible').length;
  if( visible_columns <= 5 ) {
	//console.log('show');
	for( i=0; i < 6 - visible_columns; i++ ){
		//$row.find('.col-plain:hidden').last().show();
	}
  }
  $ID = $(this).attr('data-id');
  $type = $(this).attr('data-type');
  $group.attr('data-id', $ID).attr('data-type', $type);

//Populate object
  $.post('/populate', { ID: $ID, type: $type }, function(res) { 
    //console.log(res); 
	global_data.res = res;
    if( res != 'null' )
    $.each($.parseJSON( res ), function(index, value) {
      //console.log("FIELDS: " + value.fields.location);
      //$$$fields[value.ID] = value.fields;
      var l = $(listob).clone()
			.attr('id', '_'+value.ID)
			.attr('data-id', value.ID)
			.attr('data-type', value.type)
			.html($(span).addClass('name')
				.html(value.name)
			)
			//.addClass(value.classes.join(' '))
			.appendTo($group)
			.click(listClick)
			.mousedown(setElement)
			.mousedown(actionFilters);
//.append($(span).addClass('badge').html(value.position))
      l.append($(span).addClass('time badge').html(value.time).click(timeClick));
    });
    if( $type != 'reference' ) $group.append($(listit).addClass('new').html('').click(newOb)).show().attr('data-id', $(this).attr('data-id'));
    //console.log($$fields[$type]);
    //console.log($$$fields[$ID]);
    //$.each($$fields[$type], function(index, value) {
    //  ft = $(fieldtable);
    //  ft.find('.key').html(value);
    //  ft.find('.value input').val($$$fields[$ID][value]).blur(fieldUpdate);
    //  $group.append($(listit).append(ft));
    //  console.log(value);
    //});
  });


}

$('.badge').filter(function() { return $(this).hasClass('time'); }).click(timeClick);
function timeClick() {
  var $badge = $(this);
  $(this).toggleClass('total');
  if( $(this).hasClass('total') ) $.post('/process', { action: 'sumtime', ID: element.attr('data-id') }, function(res){ $badge.html(res); });
  if( !$(this).hasClass('total') ) $.post('/process', { action: 'scalartime', ID: element.attr('data-id') }, function(res){ $badge.html(res); });
}

function fieldUpdate() {

  $.post('/process', { action: 'setfield', ID: $ID, key: $(this).closest('.field').find('.key').html(), value: $(this).val() }, function(res) {
    console.log(res);
  });

}

