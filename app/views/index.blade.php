<head>
@include('head')
</head>
<div class="row row-plain" id="primary-row">
  <div class="col-md-2 col-plain">
    <div class="list-group" data-id="0">
	  @foreach( $objects as $object )
      <a class="list-group-item object" id="_{{$object->ID}}" data-id="{{$object->ID}}" data-type="{{ $object->type }}">
		<span class="name">{{ $object->name }}</span>
		<!--<span class="badge position">{{$object->position}}</span>-->
		<span class="time badge">{{$object->time}}</span>
	  </a>
	  @endforeach
      <a class="list-group-item new"></a>
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

@include('context')
@include('foot')
