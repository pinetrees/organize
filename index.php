<?php require_once ("conn.php"); ?>
<head>
<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="css/style.css" rel="stylesheet" type="text/css">
<?php $tsqli->fetchStyle()->renderStyle(); ?>
<?php $var = $tsqli->getVariables_(); ?>
<?php $types = $tsqli->getTypes_() ?>
</head>
<div class="row row-plain" id="primary-row">
  <div class="col-md-2 col-plain">
    <div class="list-group" data-id="0">
      <?php 
      $tsqli->query("SELECT * FROM objects WHERE parent=0 ORDER BY position"); 
      while( $obj = $tsqli->fetch_object() ) { 
      ?>
      <a class="list-group-item object" id="_<?php echo $obj->ID; ?>" data-id="<?php echo $obj->ID; ?>" data-type="<?php echo $obj->type; ?>"><span class="name"><?php echo $obj->name; ?></span><!--<span class="badge position"><?php echo $obj->position; ?></span>--><span class="time badge"><?php echo $obj->time; ?></span></a>
      <?php } ?>
      <a class="list-group-item new">New</a>
      <a class="list-group-item selector"></a>
    </div>
  </div>
<!--
  <div class="col-md-2 col-plain">
    <div class="list-group hiding">
    </div>
  </div>
-->
  <div class="col-plain empty">
  </div>
</div><!--row-->
<div class="row row-plain"></div>


<div id="contextMenu" class="dropdown clearfix">
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
        <li><a tabindex="-1" href="#" class="rename">Rename</a></li>
        <li class="multiselect"><a tabindex="-1" href="#" class="delete">Delete</a></li>
        <li class="multiselect"><a tabindex="-1" href="#" class="complete">Mark as complete</a></li>
        <li class="divider"></li>
        <li class="dropdown-submenu style">
          <a tabindex="-1" href="#">Style</a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a>Background</a>
              <ul class="dropdown-menu">
                <li><a class="color" data-attribute="background" data-value="lightgreen">Green</a></li>
                <li><a class="color" data-attribute="background" data-value="#FFFF7E">Yellow</a></li>
                <li><a class="color" data-attribute="background" data-value="red">Red</a></li>
                <li><a class="color" data-attribute="background" data-value="none">None</a></li>
              </ul>
            </li>
          </ul>
        </li>
	<?php foreach( $var['context menu'] as $element ) { ?>
        <li class="dropdown-submenu <?php echo $element; ?>">
          <a tabindex="-1" href="#"><?php echo ucfirst($element); ?></a>
          <ul class="dropdown-menu">
<?php if( is_scalar($var[$element]) ) $var[$element] = array( $var[$element] ); foreach( $var[$element] as $option ) { ?>
            <li><a class="<?php echo $element; ?>-option" data-value="<?php echo $option; ?>"><?php echo ucfirst( $option ); ?></a></li>
<?php } ?>
          </ul>
        </li>
	<?php } ?>
    </ul>
</div>


<script src="js/jquery.js"></script>
<script src="js/jquery-ui.js"></script>
<script>
var $$fields = new Array();
<?php foreach( $types as $type => $contents ) { ?>
$fields = new Array();
<?php foreach( $contents['fields'] as $field ) { ?>
$fields.push('<?php echo $field; ?>');
//$$types['<?php echo $type; ?>']['fields'] = 'one';
<?php } ?>
console.log($fields);
$$fields['<?php echo $type; ?>'] = $fields;
<?php } ?>
console.log($$fields['product'].indexOf('pricee'));
</script>
<script>
var col = '<div class="col-md-2 col-plain"></div>';
var group = '<div class="list-group"></div>'; 
var span = '<span></span>';
var listit = '<a class="list-group-item"></a>';
var listob = '<a class="list-group-item object"></a>';
var input = '<div class="input-group"><input type="text" class="form-control" placeholder=""></div>';
var newb = '<div class="input-group"><input type="text" class="form-control new" placeholder=""></div>';
var fieldtable = '<table class="field"><tr><td class="half-width key"></td><td class="half-width value"><div class="input-group"><input type="text" class="form-control" placeholder=""></td></tr></table>';
$('.new').click(newOb);
var editing;
function newOb() {
  editing = true;
  $(listob).clone().append(newb).insertBefore($(this)).find('input').focus().blur(regOb);
}
function regOb() {
  editing = false;
  var $parent = $(this).closest('.list-group').attr('data-id');
  var $type = $(this).closest('.list-group').attr('data-type');
  var val = $(this).val();
  var cont = $(this).closest('.list-group-item');
  if( val == '' ) return cont.remove();
  $.post('process.php', { action: 'register', parent: $parent, type: $type, name: val }, function(res) {
    ID = res;
    console.log(res);
    cont.html($(span).addClass('name').html(val)).attr('data-id', res).attr('id', '_'+res).click(listClick).mousedown(setElement).mousedown(actionFilters);
  });
}
</script>
<script src="js/context-menu.js"></script>

<!--Sortable Memory-->
<script>
var element = $('<div></div>');
var $element;
element.attr('data-id', 0);
var multiselect;
var selection;
$( ".list-group" ).sortable({ items: "> .object", connectWith: '.list-group' }).on( "sortupdate", sortUpdate ).on( "sortreceive", sortReceive ).on( "sortremove", sortRemove );
$('.row').sortable({ items: "> .col-plain", connectWith: '.row' });
function sortRemove() {
  console.log('Removed');
  console.log('');
}
var received;
function sortReceive() {
  received = true; 
  if( multiselect == true ) return $.post('process.php', { action: 'multitransfer', IDs: selection, position: $div.index(), parent: $(this).attr('data-id') }, function(res){console.log(res); $div.before($div.find('.list-group-item').addClass('object')).remove(); });
  $.post('process.php', { action: 'transfer', ID: element.attr('data-id'), position: element.index(), parent: $(this).attr('data-id') }, function(res){console.log(res);});
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
  $.post('process.php', { action: 'order', ID: element.attr('data-id'), position: element.index(), parent: $(this).attr('data-id') }, function(res){});
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
  time.last = time.this;
  time.this = new Date().getTime();
  time.change = (time.this-time.last)/1000;
  console.log(time.this-time.last);
  console.log($element.attr('data-id'));
  $.post('process.php', { action: 'time', ID: $element.attr('data-id'), time: time.change }, function(res){
    console.log(res);
    $element.find('.time').filter(function() { return !$(this).hasClass('total'); }).html(parseInt($element.find('.time').html()) + parseInt(time.change));
  });
}
$(window).bind('beforeunload', function(){
  $element = element;
  incrementTime();
});
var $$$fields = new Array();
function listClick(e) {
  incrementTime();
  if( e.shiftKey == true ) return false;
  multiselect = false;
  selection = new Array;
  $(this).parent().find('.list-group-item').removeClass('list-group-item-active');
  $(this).addClass('list-group-item-active');
  $group = $(group).sortable({ items: "> .object", connectWith: '.list-group' }).on( "sortupdate", sortUpdate ).on( "sortreceive", sortReceive ).on( "sortremove", sortRemove );
  $column = $(this).closest('.col-plain')
  $('#primary-row').find('.col-plain').filter(function(){ return ( $(this).index() > $column.index() ); }).remove();
  $('#primary-row').append($(col).append($group));
  $ID = $(this).attr('data-id');
  $type = $(this).attr('data-type');
  $group.attr('data-id', $ID).attr('data-type', $type);
  $.post('process.php', { action: 'populate', ID: $ID, type: $type }, function(res) { 
    console.log(res); 
    if( res != 'null' )
    $.each($.parseJSON( res ), function(index, value) {
      console.log("FIELDS: " + value.fields.location);
      $$$fields[value.ID] = value.fields;
      var l = $(listob).clone().attr('id', '_'+value.ID).attr('data-id', value.ID).attr('data-type', value.type).html($(span).addClass('name').html(value.name)).addClass(value.classes.join(' ')).appendTo($group).click(listClick).mousedown(setElement).mousedown(actionFilters);
//.append($(span).addClass('badge').html(value.position))
      l.append($(span).addClass('time badge').html(value.time).click(timeClick));
    });
    if( $type != 'reference' ) $group.append($(listit).addClass('new').html('New').click(newOb)).show().attr('data-id', $(this).attr('data-id'));
    console.log($$fields[$type]);
    console.log($$$fields[$ID]);
    $.each($$fields[$type], function(index, value) {
      ft = $(fieldtable);
      ft.find('.key').html(value);
      ft.find('.value input').val($$$fields[$ID][value]).blur(fieldUpdate);
      $group.append($(listit).append(ft));
      console.log(value);
    });
  });
}

$('.badge').filter(function() { return $(this).hasClass('time'); }).click(timeClick);
function timeClick() {
  var $badge = $(this);
  $(this).toggleClass('total');
  if( $(this).hasClass('total') ) $.post('process.php', { action: 'sumtime', ID: element.attr('data-id') }, function(res){ $badge.html(res); });
  if( !$(this).hasClass('total') ) $.post('process.php', { action: 'scalartime', ID: element.attr('data-id') }, function(res){ $badge.html(res); });
}

function fieldUpdate() {

  $.post('process.php', { action: 'setfield', ID: $ID, key: $(this).closest('.field').find('.key').html(), value: $(this).val() }, function(res) {
    console.log(res);
  });

}

</script>

