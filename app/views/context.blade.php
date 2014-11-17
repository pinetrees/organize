<div id="contextMenu" class="dropdown clearfix">
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:block;position:static;margin-bottom:5px;">
        <li><a tabindex="-1" href="#" class="rename">Rename</a></li>
        <li class="multiselect"><a tabindex="-1" href="#" class="delete">Delete</a></li>
        <li class="multiselect"><a tabindex="-1" href="#" class="do" action="mark-as-class">Class</a></li>
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
	@if(isset($context_menu))
	@if(is_array($context_menu))
	@foreach($context_menu as $element)
        <li class="dropdown-submenu {{ $element }}">
          <a tabindex="-1" href="#">{{ ucfirst($element) }}</a>
          <ul class="dropdown-menu">
<?php if( is_scalar($var[$element]) ) $var[$element] = array( $var[$element] ); foreach( $var[$element] as $option ) { ?>
            <li><a class="<?php echo $element; ?>-option" data-value="<?php echo $option; ?>"><?php echo ucfirst( $option ); ?></a></li>
<?php } ?>
          </ul>
        </li>
	@endforeach
	@endif
	@endif
    </ul>
</div>

