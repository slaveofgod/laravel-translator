<p>{{ trans_choice('{1} :value minute ago|[2,*] :value minutes ago', 5, ['value' => 5]) }}</p>

<p>{{ trans_choice(  '{1} :value day ago|[2,*] :value days ago'   , 5, ['value' => 5]) }}</p>

<p>{{ __('Welcome, :name', ['name' => 'dayle']) }}</p>

<p>@lang('Hi, :name', ['name' => 'dayle'])</p>

<p>@lang("You'r welcome :name", ['name' => 'dayle'])</p>

<p>{{ __("You'r welcome") }}</p>

<p>{{ __('New Users') }}</p>

<p>@lang('Table View: User')</p>

<p>@choice('{1} :value minute ago|[2,*] :value minutes ago', 5, ['value' => 5])</p>

<p>@lang_ab('Hi, :name', ['name' => 'dayle'])</p>
                    
<p>@choice_ab('{1} :value minute ago|[2,*] :value minutes ago', 5, ['value' => 5])</p>

<p>@lang_ab('BASIC STATS & BEHAVIOUR: <b>by Source</b>')</p>