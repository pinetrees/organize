<script src="/js/jquery.js"></script>
<script src="/js/jquery-ui.js"></script>
<script src="/js/templates.js"></script>
<script src="/js/context-menu.js"></script>
<script src="/js/app.js"></script>

@if(isset($path))
<script>
//Incomplete, because of asyncronization
var path = {{ $path }}
$.each(path, function(index, value){
	console.log(value);
	$('.list-group-item').filter(function(){
		return ( $(this).attr('data-id') == value );
	}).click();
})
</script>
@endif
