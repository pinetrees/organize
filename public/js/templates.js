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
  $.post('/register', { parent: $parent, type: $type, name: val }, function(res) {
    ID = res;
    console.log(res);
    cont.html($(span).addClass('name').html(val)).attr('data-id', res).attr('id', '_'+res).click(listClick).mousedown(setElement).mousedown(actionFilters);
  });
}
